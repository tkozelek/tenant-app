<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class TenantUserTable extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Locked]
    public Tenant $tenant;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $selectedRole = '';

    #[Url(except: 'first_name')]
    public string $sortField = 'first_name';

    #[Url(except: 'asc')]
    public string $sortDirection = 'asc';

    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        setPermissionsTeamId($this->tenant->id);
    }

    #[Computed]
    public function roles()
    {
        return Role::whereNull('tenant_id')
            ->whereDoesntHave('permissions', function ($query) {
                $query->where('name', 'platform.access');
            })
            ->get();
    }

    #[Computed]
    public function users()
    {
        setPermissionsTeamId($this->tenant->id);

        $sortField = in_array($this->sortField, ['first_name', 'last_name', 'email'])
            ? $this->sortField
            : 'first_name';

        return User::query()
            ->where(function (Builder $query) {
                $query->where('id', $this->tenant->owner_id)
                    ->orWhereHas('roles', function ($q) {
                        $q->where('model_has_roles.tenant_id', $this->tenant->id);
                    });
            })
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedRole, function (Builder $query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->selectedRole)
                        ->where('model_has_roles.tenant_id', $this->tenant->id);
                });
            })
            ->with(['roles' => function ($query) {
                $query->where('model_has_roles.tenant_id', $this->tenant->id);
            }])
            ->orderBy($sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }

    public function updateRole(User $user, string $roleName): void
    {
        $this->authorize('assignRoles', $this->tenant);

        if ($user->id === $this->tenant->owner_id) {
            $this->dispatch('toast', message: 'Rolu majiteľa nie je možné zmeniť.', type: 'error');

            return;
        }

        $role = Role::where('name', $roleName)->whereNull('tenant_id')->first();

        if ($role) {
            setPermissionsTeamId($this->tenant->id);

            // Explicitly sync roles for this tenant
            $user->roles()->where('model_has_roles.tenant_id', $this->tenant->id)->detach();
            $user->assignRole($role);

            $this->dispatch('toast', message: 'Rola bola úspešne aktualizovaná.', type: 'success');
        }
    }

    public function removeUser(User $user): void
    {
        $this->authorize('assignRoles', $this->tenant);

        if ($user->id === $this->tenant->owner_id) {
            $this->dispatch('toast', message: 'Nemôžete odstrániť majiteľa obchodu.', type: 'error');

            return;
        }

        setPermissionsTeamId($this->tenant->id);
        $user->roles()->where('model_has_roles.tenant_id', $this->tenant->id)->detach();

        $this->dispatch('toast', message: 'Používateľ bol odstránený z obchodu.', type: 'success');
    }

    public function render()
    {
        return view('livewire.tenant-user-table');
    }
}

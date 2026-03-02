<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TenantController extends Controller
{
    public function show(Tenant $tenant)
    {
        return view('tenant.landing', ['tenant' => $tenant]);
    }

    public function create()
    {
        return view('tenant.create');
    }

    public function edit(Tenant $tenant)
    {
        Gate::authorize('update', $tenant);

        return view('tenant.create', ['tenant' => $tenant]);
    }

    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();
        $validated['owner_id'] = auth()->id();
        $validated['is_public'] = $request->boolean('is_public');

        $tenant = Tenant::create($validated);

        if ($request->hasFile('image')) {
            $tenant->addMediaFromRequest('image')->toMediaCollection('images');
        }

        if ($request->hasFile('title_image')) {
            $tenant->addMediaFromRequest('title_image')->toMediaCollection('titles');
        }

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Obchod bol úspešne vytvorený.');
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $validated = $request->validated();
        $validated['is_public'] = $request->boolean('is_public');

        $tenant->update($validated);

        if ($request->hasFile('image')) {
            $tenant->clearMediaCollection('images');
            $tenant->addMediaFromRequest('image')->toMediaCollection('images');
        }

        if ($request->hasFile('title_image')) {
            $tenant->clearMediaCollection('titles');
            $tenant->addMediaFromRequest('title_image')->toMediaCollection('titles');
        }

        return redirect()
            ->route('tenant.edit', $tenant)
            ->with(['status' => 'tenant-updated', 'success' => 'Obchod aktualizovaný.']);
    }

    public function destroy(Tenant $tenant, Request $request)
    {
        Gate::authorize('delete', $tenant);

        $request->validateWithBag("shopDeletion_{$tenant->id}", [
            'password' => ['required', 'current_password'],
        ]);

        $tenant->delete();

        return redirect()->route('dashboard.index')->with('success', 'Obchod bol úspešne zmazaný.');
    }

    public function deleteImage(Tenant $tenant, bool $title)
    {
        Gate::authorize('update', $tenant);

        $collection = $title ? 'titles' : 'images';
        $tenant->clearMediaCollection($collection);

        return redirect()->route('tenant.edit', $tenant)->with('success', 'Obrázok bol úspešné zmazaný.');
    }

    public function changeOwner(Tenant $tenant, Request $request)
    {
        Gate::authorize('update', $tenant);

        $request->validateWithBag("changeOwner_{$tenant->id}", [
            'password' => ['required', 'current_password'],
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $newOwner = User::where('email', $request->email)->firstOrFail();

        if ($newOwner->id === $tenant->owner_id) {
            return back()->withErrors(['email' => 'Tento používateľ už je majiteľom obchodu.'], "changeOwner_{$tenant->id}");
        }

        $tenant->update(['owner_id' => $newOwner->id]);

        return redirect()->route('dashboard.index')->with('success', 'Majiteľ obchodu bol úspešne zmenený.');
    }
}

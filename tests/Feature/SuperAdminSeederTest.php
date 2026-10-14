<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_is_created_from_config(): void
    {
        config(['app.super_admin.email' => 'root@example.test', 'app.super_admin.password' => 'secret-from-env']);

        $this->seed(RoleSeeder::class);

        $user = User::where('email', 'root@example.test')->firstOrFail();
        setPermissionsTeamId(null);
        $this->assertTrue($user->hasRole('Super Administrátor'));
        $this->assertTrue(Hash::check('secret-from-env', $user->password));
    }

    public function test_random_password_is_generated_when_none_is_configured(): void
    {
        config(['app.super_admin.email' => 'root@example.test', 'app.super_admin.password' => null]);

        $this->artisan('db:seed', ['--class' => RoleSeeder::class])
            ->expectsOutputToContain('Super admin root@example.test created with password:')
            ->assertSuccessful();

        $user = User::where('email', 'root@example.test')->firstOrFail();
        $this->assertFalse(Hash::check('password', $user->password));
    }

    public function test_no_super_admin_is_created_without_configured_email(): void
    {
        config(['app.super_admin.email' => null]);

        $this->seed(RoleSeeder::class);

        $this->assertSame(0, User::count());
    }

    public function test_pulse_is_limited_to_the_configured_super_admin(): void
    {
        config(['app.super_admin.email' => 'root@example.test']);

        $admin = User::factory()->create(['email' => 'root@example.test']);
        $other = User::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('viewPulse'));
        $this->assertFalse(Gate::forUser($other)->allows('viewPulse'));
    }

    public function test_pulse_is_closed_when_no_super_admin_is_configured(): void
    {
        config(['app.super_admin.email' => null]);

        $this->assertFalse(Gate::forUser(User::factory()->create())->allows('viewPulse'));
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Components\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('PermissionsTableSeeder');
        $this->seed('SystemsTableSeeder');
        $this->seed('CurrencyTableSeeder');
    }

    /** @test */
    public function adminsCanCreateEmployees()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');

        $this->actingAs($admin)
            ->json('POST', route('adminusers.store'), [
                'name' => 'John Doe',
                'email' => 'johnny@gmail.com',
                'mobile' => '123-456-0000',
                'password' => '123456',
                'confirm_password' => '123456',
                'role' => 2, // 1 = superadmin role, 2 = employee role
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johnny@gmail.com',
            'mobile' => '123-456-0000',
        ]);

        // Verify password entered in form is equal to the password stored in the database.
        $user = User::where('name', 'John Doe')->first();
        $this->assertTrue(Hash::check('123456', $user->password));

        $this->assertTrue($user->hasRole('employee'));
    }

    /** @test */
    public function adminsCanEditEmployees()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $user = factory(User::class)->create(['name' => 'John Doe'])->assignRole('employee');

        $this->actingAs($admin)
            ->json('PUT', route('adminusers.update', $user->id), [
                'name' => 'Jane Smith',
                'email' => $user->email,
            ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
        ]);
    }

    /** @test */
    public function adminsCanDeleteEmployees()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $user = factory(User::class)->create(['name' => 'John Doe'])->assignRole('employee');

        $this->actingAs($admin)->json('DELETE', route('adminusers.destroy', $user->id));

        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
        ]);
    }
}

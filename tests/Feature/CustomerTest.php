<?php

namespace Tests\Feature;

use App\Customer;
use Carbon\Carbon;
use Tests\TestCase;
use App\Components\User\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
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
    public function adminsCanDeleteCustomers()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $customer = factory(Customer::class)->create(['company' => 'Test 222', 'created_by' => $admin->id]);

        $this->actingAs($admin)->json('DELETE', route('admincustomers.destroy', $customer->id));

        // Customers are soft deleted, so verify customer still exists with a deleted_at value.
        $this->assertDatabaseHas('customers', [
            'company' => 'Test 222',
            'deleted_at' => Carbon::now(),
        ]);
    }

    /** @test */
    public function adminsCanEditCustomers()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $customer = factory(Customer::class)->create(['company' => 'My Old Company', 'state' => 'New York', 'created_by' => $admin->id]);

        $this->actingAs($admin)
            ->json('PUT', route('admincustomers.destroy', $customer->id), [
                'company' => 'My New Company 123',
                'mobile' => '999-555-3333',
                'email' => 'newemail@gmail.com',
                'website' => 'https://test.com',
                'city' => 'Los Angeles',
                'state' => 'California',
                'country' => 'United States',
            ]);

        $this->assertDatabaseHas('customers', [
            'company' => 'My New Company 123',
            'mobile' => '999-555-3333',
            'email' => 'newemail@gmail.com',
            'website' => 'https://test.com',
            'city' => 'Los Angeles',
            'state' => 'California',
            'country' => 'United States',
        ]);
    }

    /** @test */
    public function guestsCannotCreateCustomers()
    {
        $this->json('POST', route('admincustomers.store'), [
            'company' => 'Test 111',
            'currency_id' => 1,
            'mobile' => '123-456-5555',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseMissing('customers', [
            'company' => 'Test 111',
        ]);
    }

    /** @test */
    public function guestsCannotEditCustomers()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $customer = factory(Customer::class)->create(['company' => 'My Old Company', 'state' => 'New York', 'created_by' => $admin->id]);

        $this->json('PUT', route('admincustomers.update', $customer->id), [
            'company' => 'My New Company 123',
            'state' => 'California',
        ]);

        $this->assertDatabaseHas('customers', [
            'company' => 'My Old Company',
            'state' => 'New York',
        ]);
    }

    /** @test */
    public function guestsCannotDeleteCustomers()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');
        $customer = factory(Customer::class)->create(['company' => 'My Old Company', 'created_by' => $admin->id]);

        $this->assertDatabaseHas('customers', [
            'company' => 'My Old Company',
            'deleted_at' => null,
        ]);

        $this->json('DELETE', route('admincustomers.destroy', $customer->id));

        // Customers are soft deleted, so verify customer still exists without a deleted_at value.
        $this->assertDatabaseHas('customers', [
            'company' => 'My Old Company',
            'deleted_at' => null,
        ]);
    }
}

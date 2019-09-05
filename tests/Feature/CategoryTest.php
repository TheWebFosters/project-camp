<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Components\User\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
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
    public function adminsCanCreateCategories()
    {
        $admin = factory(User::class)->create()->assignRole('superadmin');

        $this->actingAs($admin)
            ->json('POST', route('admincategories.store'), [
                'name' => 'Finance',
                'type' => 'projects',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Finance',
            'type' => 'projects',
        ]);
    }
}

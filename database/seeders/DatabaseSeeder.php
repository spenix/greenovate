<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            BloodTypeSeeder::class,
            DesignationSeeder::class,
            EmployeeTypeSeeder::class,
            ShiftSeeder::class,
            LeaveEntitlementSeeder::class,
            EducationalLevelSeeder::class,
            ViolationCategorySeeder::class,
            RelationshipTypeSeeder::class
        ]);
    }
}

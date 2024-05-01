<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ViolationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Drunk Working'
            ],
            [
                'name' => 'Stealing Materials'
            ],
            [
                'name' => 'Fighting while Working'
            ],
        ];

        ViolationCategory::insert($data);
    }
}

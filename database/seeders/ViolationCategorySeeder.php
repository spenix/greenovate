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
                'name' => 'MAJOR'
            ],
            [
                'name' => 'MINOR'
            ],
        ];

        ViolationCategory::insert($data);
    }
}

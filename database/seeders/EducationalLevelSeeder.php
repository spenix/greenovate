<?php

namespace Database\Seeders;

use App\Models\EducationalLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'No Education'
            ],
            [
                'name' => 'Elementary'
            ],
            [
                'name' => 'Junior High School'
            ],
            [
                'name' => 'Senior High School Level'
            ],
            [
                'name' => 'Senior High School Graduate'
            ],
            [
                'name' => 'College Level'
            ],
            [
                'name' => 'College Graduate'
            ],
        ];

        EducationalLevel::insert($data);
    }
}

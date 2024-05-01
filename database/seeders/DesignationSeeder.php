<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Carpenter'
            ],
            [
                'name' => 'Engineer'
            ],
            [
                'name' => 'Welder'
            ],
            [
                'name' => 'Plumber'
            ],
            [
                'name' => 'Foreman'
            ],
            [
                'name' => 'Labor'
            ],
        ];

        Designation::insert($data);
    }
}

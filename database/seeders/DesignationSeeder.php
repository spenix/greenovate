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
                'name' => 'Accountant I',
                'department_id' => 1
            ],
            [
                'name' => 'Accountant II',
                'department_id' => 1
            ],
            [
                'name' => 'Carpenter',
                'department_id' => 2
            ],
            [
                'name' => 'Engineer',
                'department_id' => 2
            ],
            [
                'name' => 'Welder',
                'department_id' => 2
            ],
            [
                'name' => 'Plumber',
                'department_id' => 2
            ],
            [
                'name' => 'Foreman',
                'department_id' => 2
            ],
            [
                'name' => 'Labor',
                'department_id' => 2
            ],
            [
                'name' => 'Security Guard',
                'department_id' => 2
            ],
            [
                'name' => 'HR Manager',
                'department_id' => 3
            ],
            [
                'name' => 'HR Coordinator',
                'department_id' => 3
            ],
            [
                'name' => 'HR Specialist',
                'department_id' => 3
            ],
            [
                'name' => 'Compensation Analyst',
                'department_id' => 3
            ],
            [
                'name' => 'Employee Relations Manager
                ',
                'department_id' => 3
            ],
        ];

        Designation::insert($data);
    }
}

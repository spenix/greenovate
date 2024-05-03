<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Morning'
            ],
            [
                'name' => 'Afternoon'
            ],
            [
                'name' => 'Night'
            ],
            [
                'name' => 'Morning/Afternoon'
            ],
        ];

        Shift::insert($data);
    }
}

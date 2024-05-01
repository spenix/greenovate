<?php

namespace Database\Seeders;

use App\Models\RelationshipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelationshipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Mother'
            ],
            [
                'name' => 'Father'
            ],
            [
                'name' => 'Brother'
            ],
            [
                'name' => 'Sister'
            ],
        ];

        RelationshipType::insert($data);
    }
}

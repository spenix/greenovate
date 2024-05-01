<?php

namespace Database\Seeders;

use App\Models\LeaveEntitlement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveEntitlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Payable'
            ],
            [
                'name' => 'Non-Payable'
            ],
        ];

        LeaveEntitlement::insert($data);
    }
}

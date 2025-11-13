<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Doe',
                'phone' => '081234567890',
                'email' => 'john@example.com'
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '082345678901',
                'email' => 'jane@example.com'
            ],
            [
                'name' => 'Robert Johnson',
                'phone' => '083456789012',
                'email' => 'robert@example.com'
            ],
            [
                'name' => 'Emily Davis',
                'phone' => '084567890123',
                'email' => 'emily@example.com'
            ],
            [
                'name' => 'Michael Wilson',
                'phone' => '085678901234',
                'email' => 'michael@example.com'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
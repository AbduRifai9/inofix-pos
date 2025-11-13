<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create additional users if needed
        // $users = [
        //     [
        //         'name' => 'Cashier 1',
        //         'email' => 'cashier1@example.com',
        //         'password' => Hash::make('password'),
        //     ],
        //     [
        //         'name' => 'Cashier 2',
        //         'email' => 'cashier2@example.com',
        //         'password' => Hash::make('password'),
        //     ],
        // ];

        // foreach ($users as $userData) {
        //     User::create([
        //         'name' => $userData['name'],
        //         'email' => $userData['email'],
        //         'password' => Hash::make($userData['password']),
        //     ]);
        // }
    }
}

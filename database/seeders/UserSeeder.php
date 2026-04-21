<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
            ],
            [
                'name' => 'Adi',
                'email' => 'adi@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
            ],
            [
                'name' => 'Eric',
                'email' => 'eric@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
            ],
            [
                'name' => 'Thesa',
                'email' => 'thesa@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

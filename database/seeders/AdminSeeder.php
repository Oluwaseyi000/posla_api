<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'name' => 'Adebajo Seyi',
                'username' => 'Adebajo Seyi',
                'email' => 'adebajo.oluwaseyi@gmail.com',
                'password' => bcrypt('posla@password'),
                'role_type' => User::ROLE_TYPE_ADMIN,
                'status' => User::ACTIVE,
            ],
            [
                'name' => 'Admin',
                'username' => 'Admin',
                'email' => 'admin@mailinator.com',
                'password' => bcrypt('posla@password'),
                'role_type' => User::ROLE_TYPE_ADMIN,
                'status' => User::ACTIVE,
            ],
            [
                'name' => 'Super Admin',
                'username' => 'Super Admin',
                'email' => 'superamin@mailinator.com',
                'password' => bcrypt('posla@password'),
                'role_type' => User::ROLE_TYPE_SUPERADMIN,
                'status' => User::ACTIVE,
            ],

        ];

        foreach ($admins as  $admin) {
            User::updateOrCreate([
                'email' => $admin['email'],
            ], $admin);
        }
        dump('admin seeded');
    }
}

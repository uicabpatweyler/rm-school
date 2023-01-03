<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userSuperAdmin = User::create([
            'name' => 'Weyler A. Uicab P.',
            'email' => 'uicabpatweyler@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        $otherUser = User::create([
            'name' => 'Jesus R. Uicab K.',
            'email' => 'uicabkurafael@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        $roleSuperAdmin = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Admin'
        ]);

        $roleAdmin = Role::create([
            'name' => 'admin',
            'display_name' => 'Admin'
        ]);

        $userSuperAdmin->assignRole($roleSuperAdmin);
    }
}

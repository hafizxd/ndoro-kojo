<?php

namespace Database\Seeders;

use App\Constants\UserRole;
use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'role' => UserRole::SUPERADMIN
        ], [ 'password' => Hash::make('123456') ]);

        User::firstOrCreate([
            'name' => 'Admin',
            'username' => 'admin',
            'role' => UserRole::ADMIN
        ], [ 'password' => Hash::make('123456') ]);

        $district = District::first();
        User::firstOrCreate([
            'name' => 'Operator',
            'username' => 'operator',
            'role' => UserRole::OPERATOR,
            'district_id' => $district?->id
        ], [ 'password' => Hash::make('123456') ]);
    }
}

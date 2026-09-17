<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name' => 'General Store', 'email' => 'generalstore@warehouse.com', 'password' => bcrypt('generalstore21'), 'role' => 'user', 'warehouse_id' => 1]);
        User::create(['name' => 'Admin', 'email' => 'admin@warehouse.com', 'password' => bcrypt('adminwarehouse21'), 'role' => 'admin']);
    }
}

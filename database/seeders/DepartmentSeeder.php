<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create(['name' => 'Food & Beverage']);
        Department::create(['name' => 'Kitchen']);
        Department::create(['name' => 'Accounting']);
        Department::create(['name' => 'FO']); 
        Department::create(['name' => 'HouseKeeping']); 
        Department::create(['name' => 'Engineering']); 
    }
}

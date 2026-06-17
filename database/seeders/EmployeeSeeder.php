<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $employees = [
            ['schedule_id' => 1, 'department_id' => 2, 'designation_id' => 1, 'firstname' => 'Mohona', 
            'lastname' => 'Akter', 'unique_id' => 'EMP-20230811-001',
             'email' => 'mohona@gmail.com', 
            'phone' => '+88 (014) 22-455656', 'address' => 'Mirpur-01, Dhaka-1216',
             'dob' => '2001-01-01', 'gender' => 2, 
             'religion' => 1, 'marital' => 2, 'status' => 1,'user_id' => 1 ],

           
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $classes = DB::table('classes')->pluck('id')->toArray();
        $firstNames = ['John', 'Mary', 'Peter', 'Grace', 'David', 'Sarah', 'James', 'Alice', 'Robert', 'Catherine',
                       'Michael', 'Elizabeth', 'William', 'Margaret', 'Joseph', 'Susan', 'Charles', 'Dorothy',
                       'Thomas', 'Jane', 'George', 'Nancy', 'Paul', 'Ruth', 'Mark', 'Judith', 'Daniel', 'Evelyn'];
        $lastNames = ['Mwangi', 'Ochieng', 'Akinyi', 'Odhiambo', 'Njeri', 'Kariuki', 'Okoth', 'Wanjiru', 'Kiprop',
                      'Muthoni', 'Kamau', 'Auma', 'Omondi', 'Atieno', 'Kibe', 'Wangui', 'Oduor', 'Chelangat',
                      'Maina', 'Oloo', 'Kimeto', 'Chebet', 'Rono', 'Jepchirchir', 'Koech', 'Chesang', 'Rotich', 'Chepkwony'];

        $students = [];
        for ($i = 1; $i <= 40; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $isBoarding = rand(0, 1) === 1;
            $classId = $classes[array_rand($classes)];

            // Create user
            $userId = DB::table('users')->insertGetId([
                'uuid' => Str::uuid(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@student.school.com'),
                'password' => Hash::make('password123'),
                'phone' => '07' . rand(10000000, 99999999),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign student role
            $role = \Spatie\Permission\Models\Role::where('name', 'Student')->first();
            if (!$role) {
                $role = \Spatie\Permission\Models\Role::create(['name' => 'Student', 'guard_name' => 'web']);
            }
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ]);

            // Create student
            $students[] = [
                'user_id' => $userId,
                'admission_number' => 'ADM-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'class_id' => $classId,
                'classification' => $isBoarding ? 'boarding' : 'day',
                'status' => 'active',
                'guardian_name' => $lastName . ' ' . $firstName . ' Sr.',
                'guardian_phone' => '07' . rand(10000000, 99999999),
                'guardian_email' => 'guardian.' . strtolower($firstName . '.' . $lastName) . '@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('students')->insert($students);
    }
}

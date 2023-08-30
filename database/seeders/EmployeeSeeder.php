<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Posisi;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data_divisi = [
            ['name' => 'Product Engineering'],
            ['name' => 'Designer'],
            ['name' => 'Human Resource'],
        ];
        $data_posisi = [
            ['division_id' => 1,'name' => 'Backend Developer'],
            ['division_id' => 1,'name' => 'Business Analyst'],
            ['division_id' => 2,'name' => 'UI/UX Deisgner'],
            ['division_id' => 1,'name' => 'Content Creator'],
            ['division_id' => 3,'name' => 'Human Resource Development'],
        ];
        $data_user = [
            ['name' => 'Ibrahim Khalish','email' => 'ibrahim@gmail.com'],
            ['name' => 'Fathir Akmal','email' => 'fathir@gmail.com'],
            ['name' => 'Muhammad Arrafi','email' => 'arra@gmail.com'],
            ['name' => 'Putri Kirana','email' => 'putri@gmail.com'],
            ['name' => 'Stephanie Jesselyn','email' => 'stephanie@gmail.com'],
            ['name' => 'Sarah Chani','email' => 'sarah@gmail.com'],
            ['name' => 'Rafly Fachri','email' => 'rafly@gmail.com'],
        ];
        $data_employee = [
            ['firstname' => 'Ibrahim','lastname' => 'khalish','user_id' => 2,'id_number' => '8551785','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimanggis','date' => '2002-08-12'],
            ['firstname' => 'Fathir','lastname' => 'Akmal','user_id' => 3,'id_number' => '8551786','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimahi','date' => '2002-01-04'],
            ['firstname' => 'Muhammad','lastname' => 'Arrafi','user_id' => 4,'id_number' => '8551787','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cibinong','date' => '2003-04-24'],
            ['firstname' => 'Putri','lastname' => 'Kirana','user_id' => 5,'id_number' => '8551788','position_id' => 4,'division_id' => 1,'gender' => 'female','address' => 'Tangerang','date' => '2001-05-09'],
            ['firstname' => 'Stephanie','lastname' => 'Jesselyn','user_id' => 6,'id_number' => '8551789','position_id' => 5,'division_id' => 3,'gender' => 'female','address' => 'Cilodong','date' => '1998-11-12'],
            ['firstname' => 'Sarah','lastname' => 'Chani','user_id' => 7,'id_number' => '8551790','position_id' => 2,'division_id' => 1,'gender' => 'female','address' => 'Cipete','date' => '2001-09-28'],
            ['firstname' => 'Rafly','lastname' => 'Fachri','user_id' => 8,'id_number' => '8551791','position_id' => 3,'division_id' => 2,'gender' => 'male','address' => 'Cibubur','date' => '2001-07-13'],
        ];
        foreach ($data_divisi as $data) {
            Division::insert([
                'name' => $data['name'],
                'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
            ]);
        }
        foreach ($data_posisi as $data) {
            Position::insert([
                'division_id' => $data['division_id'],
                'name' => $data['name'],
            ]);
        }
        foreach ($data_user as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('123'),
                'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
            ]);

            $user->assignRole('employee');
        }

        foreach ($data_employee as $data) {
            Employee ::insert([
                'first_name' => $data['firstname'],
                'last_name' => $data['lastname'],
                'user_id' => $data['user_id'],
                'avatar' => 'bri.png',
                'id_number' => $data['id_number'],
                'position_id' => $data['position_id'],
                'division_id' => $data['division_id'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'birth_date' => $data['date'],
                'is_active' => true,
                'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
            ]);
        }
    }
}

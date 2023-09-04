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
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

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
        $data_position = [
            ['division_id' => 1,'name' => 'Backend Developer'],
            ['division_id' => 1,'name' => 'Frontend Developer'],
            ['division_id' => 1,'name' => 'Business Analyst'],
            ['division_id' => 1,'name' => 'Business Development'],
            ['division_id' => 1,'name' => 'System Analyst'],
            ['division_id' => 1,'name' => 'System Architect'],
            ['division_id' => 1,'name' => 'Mobile App Developer'],
            ['division_id' => 2,'name' => 'UI/UX Deisgner'],
            ['division_id' => 2,'name' => 'Graphic & UI/UX Deisgner'],
            ['division_id' => 2,'name' => 'Content Creator'],
            ['division_id' => 3,'name' => 'Human Resource Development'],
            ['division_id' => 3,'name' => 'Quality Assurance'],
            ['division_id' => 3,'name' => 'Finance Staff'],
        ];
        // COBA DATA FAKER
        // $faker = Faker::create('id_ID');

        // for($i = 1; $i <= 50; $i++){

        //     $user = User::insert([
        //       'name' => $faker->name,
        //       'email' => $faker->unique()->safeEmail(),
        //       'password' => bcrypt('password@123')
        //     ]);

        //     Employee::insert([
        //         'first_name' => $faker->firstName,
        //         'first_name' => $faker->lastName,
        //         'user_id' => $user->id,
        //         'avatar' => 'bri.png',
        //         'id_number' => $faker->unique()->randomNumber(8),
        //         'position_id' => $faker->unique()->numberBetween(1,13),
        //         'division_id' => $faker->unique()->numberBetween(1,3),
        //         'gender' => $faker->randomElement(['male','female']),
        //         'address' => $faker->address,
        //         'date' => $faker->date,
        //         'is_active' => true,
        //     ]);


        // }
        $data_user = [
            ['name' => 'Ibrahim Khalish','email' => 'ibrahim@gmail.com',],
            ['name' => 'Fathir Akmal','email' => 'fathir@gmail.com',],
            ['name' => 'Muhammad Arrafi','email' => 'arra@gmail.com',],
            ['name' => 'Putri Kirana','email' => 'putri@gmail.com',],
            ['name' => 'Stephanie Jesselyn','email' => 'stephanie@gmail.com',],
            ['name' => 'Sarah Chani','email' => 'sarah@gmail.com',],
            ['name' => 'Fauzan Alghifari','email' => 'fauzan@gmail.com',],
            ['name' => 'Kemal Al Ghifari','email' => 'kemal@gmail.com',],
            ['name' => 'Rizky Atmaja','email' => 'atmaja@gmail.com',],
            ['name' => 'Mahesa Alfian','email' => 'mahesa@gmail.com',],
        ];
        $data_employee = [
            ['firstname' => 'Ibrahim','lastname' => 'Khalish','user_id' => 2,'id_number' => '8551785','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimanggis','date' => '2002-08-12'],
            ['firstname' => 'Fathir','lastname' => 'Akmal','user_id' => 3,'id_number' => '8551786','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimahi','date' => '2002-01-04'],
            ['firstname' => 'Muhammad','lastname' => 'Arrafi','user_id' => 4,'id_number' => '8551787','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cibinong','date' => '2003-04-24'],
            ['firstname' => 'Putri','lastname' => 'Kirana','user_id' => 5,'id_number' => '8551788','position_id' => 4,'division_id' => 1,'gender' => 'female','address' => 'Tangerang','date' => '2001-05-09'],
            ['firstname' => 'Stephanie','lastname' => 'Jesselyn','user_id' => 6,'id_number' => '8551789','position_id' => 5,'division_id' => 3,'gender' => 'female','address' => 'Cilodong','date' => '1998-11-12'],
            ['firstname' => 'Sarah','lastname' => 'Chani','user_id' => 7,'id_number' => '8551790','position_id' => 2,'division_id' => 1,'gender' => 'female','address' => 'Cipete','date' => '2001-09-28'],
            ['firstname' => 'Fauzan','lastname' => 'Alghifari','user_id' => 8,'id_number' => '8551791','position_id' => 3,'division_id' => 2,'gender' => 'male','address' => 'Depok','date' => '2001-07-13'],
            ['firstname' => 'Kemal','lastname' => 'Al Ghifari','user_id' => 9,'id_number' => '8551792','position_id' => 3,'division_id' => 3,'gender' => 'male','address' => 'Harjamukti','date' => '2001-07-13'],
            ['firstname' => 'Rizky','lastname' => 'Atmaja','user_id' => 10,'id_number' => '8551732','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Harjamukti','date' => '2001-07-13'],
            ['firstname' => 'Mahesa','lastname' => 'Alfian','user_id' => 11,'id_number' => '855170','position_id' => 2,'division_id' => 2,'gender' => 'male','address' => 'Depok','date' => '2001-07-13'],
        ];

        $permissionMap = [
            'Ibrahim Khalish' => 'Ordinary Employee',
            'Fathir Akmal' => 'Ordinary Employee',
            'Muhammad Arrafi' => 'Ordinary Employee',
            'Putri Kirana' => 'Ordinary Employee',
            'Stephanie Jesselyn' => 'Ordinary Employee',
            'Sarah Chani' => 'Ordinary Employee',
            'Fauzan Alghifari' => 'Human Resource',
            'Rizky Atmaja' => 'Head of Tribe',
            'Mahesa Alfian' => 'Head of Tribe',

        ];
        foreach ($data_divisi as $data) {
            Division::insert([
                'name' => $data['name'],
                'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
            ]);
        }
        foreach ($data_position as $data) {
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

            if (isset($permissionMap[$data['name']])) {
                $user->givePermissionTo($permissionMap[$data['name']]);
            }
        }

        foreach ($data_employee as $data) {
            Employee::insert([
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

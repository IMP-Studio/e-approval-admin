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
            ['name' => 'Product Engineer'],
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
        
        $htPermissions = [
            'approve_preliminary', //khusus ht
            'reject_prensence',
            'view_request_pending',

            //DIVISI
            'view_divisions',
            'export_divisions',

            //POSISI
            'view_positions',
            'export_positions',

            //EMPLOYEE
            'view_employees',
            'export_employees',

            //PRESENCE
            'view_presences',
            'export_presences',

            //PARTNER
            'view_partners',
            'export_partners',

            //PROJECT
            'view_projects',
            'export_projects',

            //STAND UP
            'view_standups',
            'export_standups',
        ];

        $hrPermissions = [
            'approve_allowed', //khusus hr
            'reject_prensence',
            'view_request_preliminary',

            //DIVISI
            'view_divisions',
            'export_divisions',

            //POSISI
            'view_positions',
            'export_positions',

            //EMPLOYEE
            'view_employees',
            'export_employees',

            //PRESENCE
            'view_presences',
            'export_presences',

            //PARTNER
            'view_partners',
            'add_partners',
            'edit_partners',
            'delete_partners',
            'import_partners',
            'export_partners',

            //PROJECT
            'view_projects',
            'add_projects',
            'edit_projects',
            'delete_projects',
            'import_projects',
            'export_projects',

            //STAND UP
            'view_standups',
            'export_standups',
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
    
        // Use Faker to create dummy employees
        $faker = Faker::create('id_ID');

$hrManagementCount = 0; // Counter for 'Human Resource Development' position
$hrManagementLimit = 3; // The maximum allowed number of 'Human Resource Development' employees
$createdEmployees = 0; // Counter for created employees

while ($createdEmployees < 50) {

    $divisionId = $faker->numberBetween(1, 3); // Randomly choose a division

    $positionsInDivision = array_filter($data_position, function ($position) use ($divisionId) {
        return $position['division_id'] == $divisionId;
    });

    $positionKeys = array_keys($positionsInDivision);
    $positionIndex = $faker->numberBetween(0, count($positionKeys) - 1);
    $positionKey = $positionKeys[$positionIndex];
    $positionId = $positionKey + 1;
    $positionName = $positionsInDivision[$positionKey]['name'];

    // Check if we reached the limit for 'Human Resource Development' position
    if ($positionName == 'Human Resource Development' && $hrManagementCount >= $hrManagementLimit) {
        continue;  // skip this iteration
    } elseif ($positionName == 'Human Resource Development') {
        $hrManagementCount++;
    }

    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $username = $firstName . ' ' . $lastName;

    $user = User::create([
        'name' => $username,
        'email' => strtolower($firstName . $lastName) . '@gmail.com',
        'password' => bcrypt('password@123')
    ]);

    $user->assignRole('employee');  // Assign "employee" role

    Employee::insert([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'user_id' => $user->id,
        'id_number' => $faker->unique()->randomNumber(8),
        'division_id' => $divisionId,
        'position_id' => $positionId,
        'gender' => $faker->randomElement(['male', 'female']),
        'address' => $faker->address,
        'birth_date' => $faker->date,
        'is_active' => true,
    ]);

    $createdEmployees++; // Increment created employees counter
}


        
       // Randomly selecting 3 "HT" from "Product Engineering" and "Designer"
        $desiredDivisions = ['Product Engineering', 'Designer'];

        // Fetch all users from the specified divisions
        $usersFromDesiredDivisions = User::whereHas('employee', function ($query) use ($desiredDivisions) {
            $query->whereIn('division_id', Division::whereIn('name', $desiredDivisions)->pluck('id'));
        })->get();

        // Randomly select 3 users from that pool
        $selectedUsers = $usersFromDesiredDivisions->random(3);

        // Assign the "HT" permissions to the selected users
        foreach ($selectedUsers as $user) {
            $user->givePermissionTo($htPermissions);
        }

        // Assigning HR permissions to all employees in Human Resource division
        $hrUsers = User::whereHas('employee', function ($query) {
            $query->whereHas('position', function ($subQuery) {
                $subQuery->whereHas('division', function ($subSubQuery) {
                    $subSubQuery->where('name', 'Human Resource');
                });
            });
        })->get();

        foreach ($hrUsers as $user) {
            $user->givePermissionTo($hrPermissions);
        }   

            }
        }


// $data_user = [
        //     ['name' => 'Ibrahim Khalish','email' => 'ibrahim@gmail.com',],
        //     ['name' => 'Fathir Akmal','email' => 'fathir@gmail.com',],
        //     ['name' => 'Muhammad Arrafi','email' => 'arra@gmail.com',],
        //     ['name' => 'Putri Kirana','email' => 'putri@gmail.com',],
        //     ['name' => 'Stephanie Jesselyn','email' => 'stephanie@gmail.com',],
        //     ['name' => 'Sarah Chani','email' => 'sarah@gmail.com',],
        //     ['name' => 'Fauzan Alghifari','email' => 'fauzan@gmail.com',],
        //     ['name' => 'Kemal Al Ghifari','email' => 'kemal@gmail.com',],
        //     ['name' => 'Rizky Atmaja','email' => 'atmaja@gmail.com',],
        //     ['name' => 'Mahesa Alfian','email' => 'mahesa@gmail.com',],
        // ];
        // $data_employee = [
        //     ['firstname' => 'Ibrahim','lastname' => 'Khalish','user_id' => 2,'id_number' => '8551785','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimanggis','date' => '2002-08-12'],
        //     ['firstname' => 'Fathir','lastname' => 'Akmal','user_id' => 3,'id_number' => '8551786','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cimahi','date' => '2002-01-04'],
        //     ['firstname' => 'Muhammad','lastname' => 'Arrafi','user_id' => 4,'id_number' => '8551787','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Cibinong','date' => '2003-04-24'],
        //     ['firstname' => 'Putri','lastname' => 'Kirana','user_id' => 5,'id_number' => '8551788','position_id' => 4,'division_id' => 1,'gender' => 'female','address' => 'Tangerang','date' => '2001-05-09'],
        //     ['firstname' => 'Stephanie','lastname' => 'Jesselyn','user_id' => 6,'id_number' => '8551789','position_id' => 5,'division_id' => 3,'gender' => 'female','address' => 'Cilodong','date' => '1998-11-12'],
        //     ['firstname' => 'Sarah','lastname' => 'Chani','user_id' => 7,'id_number' => '8551790','position_id' => 2,'division_id' => 1,'gender' => 'female','address' => 'Cipete','date' => '2001-09-28'],
        //     ['firstname' => 'Fauzan','lastname' => 'Alghifari','user_id' => 8,'id_number' => '8551791','position_id' => 3,'division_id' => 2,'gender' => 'male','address' => 'Depok','date' => '2001-07-13'],
        //     ['firstname' => 'Kemal','lastname' => 'Al Ghifari','user_id' => 9,'id_number' => '8551792','position_id' => 3,'division_id' => 3,'gender' => 'male','address' => 'Harjamukti','date' => '2001-07-13'],
        //     ['firstname' => 'Rizky','lastname' => 'Atmaja','user_id' => 10,'id_number' => '8551732','position_id' => 1,'division_id' => 1,'gender' => 'male','address' => 'Harjamukti','date' => '2001-07-13'],
        //     ['firstname' => 'Mahesa','lastname' => 'Alfian','user_id' => 11,'id_number' => '855170','position_id' => 2,'division_id' => 2,'gender' => 'male','address' => 'Depok','date' => '2001-07-13'],
        // ];
        // foreach ($data_user as $data) {
        //     $user = User::create([
        //         'name' => $data['name'],
        //         'email' => $data['email'],
        //         'password' => bcrypt('123'),
        //         'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
        //         'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
        //     ]);

        //     $user->assignRole('employee');

        //     if (isset($permissionMap[$data['name']])) {
        //         $user->givePermissionTo($permissionMap[$data['name']]);
        //     }
        // }

        // foreach ($data_employee as $data) {
        //     Employee::insert([
        //         'first_name' => $data['firstname'],
        //         'last_name' => $data['lastname'],
        //         'user_id' => $data['user_id'],
        //         'id_number' => $data['id_number'],
        //         'position_id' => $data['position_id'],
        //         'division_id' => $data['division_id'],
        //         'gender' => $data['gender'],
        //         'address' => $data['address'],
        //         'birth_date' => $data['date'],
        //         'is_active' => true,
        //         'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
        //         'updated_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
        //     ]);
        // }
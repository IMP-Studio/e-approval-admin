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
            'can_access_web',
            'approve_preliminary', //khusus ht
            'reject_presence',
            'view_request_pending',

            'can_access_web',

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
            'can_access_web',
            'approve_allowed', //khusus hr
            'reject_presence',
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


        
$desiredDivisions = [1,2,3];

// Fetch random user from "Product Engineering"
$userFromProductEngineering = User::whereHas('employee', function ($query) {
    $query->whereIn('division_id', Division::where('id', 1)->pluck('id'));
})->inRandomOrder()->first();

// Fetch random user from "Designer"
$userFromDesigner = User::whereHas('employee', function ($query) {
    $query->whereIn('division_id', Division::where('id', 2)->pluck('id'));
})->inRandomOrder()->first();

// Fetch random user from "Human Resource" WITHOUT excluding any positions
$userFromHR = User::whereHas('employee', function ($query) {
    $query->whereIn('division_id', Division::where('id', 3)->pluck('id'));
})->inRandomOrder()->first();

$selectedUsers = collect([]);

if ($userFromProductEngineering) {
    $selectedUsers->push($userFromProductEngineering);
}
if ($userFromDesigner) {
    $selectedUsers->push($userFromDesigner);
}
if ($userFromHR) {
    $selectedUsers->push($userFromHR);
}

// Assign permissions to the selected users
foreach ($selectedUsers as $user) {
    // Check for the presence of the employee relation
    if ($user->employee && $user->employee->position) {
        // For HR Development position, give ONLY HR permissions
        if ($user->employee->position->name === 'Human Resource Development') {
            $user->givePermissionTo($hrPermissions);
        } else {
            // For other positions, give HT permissions
            $user->givePermissionTo($htPermissions);
        }
    }
}

// If you want to give HR permissions to all users with the "Human Resource Development" position
$hrDevelopmentUsers = User::whereHas('employee', function ($query) {
    $query->whereHas('position', function ($subQuery) {
        $subQuery->where('name', 'Human Resource Development');
    });
})->get();

foreach ($hrDevelopmentUsers as $hrDevUser) {
    $hrDevUser->givePermissionTo($hrPermissions);
}




            }
        }



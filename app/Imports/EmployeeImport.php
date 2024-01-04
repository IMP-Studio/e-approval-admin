<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EmployeeImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = User::updateOrCreate(
            ['email' => $row[3]],
            [
                'name' => $row[2],
                'password' => bcrypt('password@123'),
            ]
        );
        $user->assignRole('employee');
        $user->touch();

        $division = Division::updateOrCreate(['name' => $row[7]]);
        $division->touch();

        $position = Position::updateOrCreate(
            ['division_id' => $division->id],
            ['name' => $row[8]]
        );
        $position->touch();

        $excelDate = intval($row[10]);

        $unixTimestamp = ($excelDate - 25569) * 86400;

        $formattedDate = Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d');

        $employee = Employee::updateOrCreate(
            [
                'user_id' => $user->id,
                'id_number' => $row[6],
            ],
            [
                'first_name' => $row[4],
                'last_name' => $row[5],
                'division_id' => $division->id,
                'position_id' => $position->id,
                'gender' => $row[9],
                'birth_date' => $formattedDate,
                'address' => $row[11],
            ]
        );
        $employee->touch();

        return $employee;

    }
}

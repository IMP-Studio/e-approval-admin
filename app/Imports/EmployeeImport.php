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
        $user = User::create([
            'name' => $row[2],
            'email' => $row[3],
            'role' => 'employees',
            'password' => bcrypt('password@123'),
        ]);

        $division = Division::where('name', $row[7])->first();
        $position = Position::where('name', $row[8])->first();

        if (!$division) {
            $division = Division::create([
                'name' => $row[7]
            ]);
        }

        if (!$position) {
            $position = Position::create([
                'position_id' => $division->id,
                'name' => $row[8]
            ]);
        }
        $excelDate = intval($row[10]);

        $unixTimestamp = ($excelDate - 25569) * 86400;

        $formattedDate = Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d');

        return new Employee([
            'user_id' => $user->id,
            'first_name' => $row[4],
            'last_name' => $row[5],
            'id_number' => $row[6],
            'division_id' => $division->id,
            'position_id' => $position->id,
            'gender' => $row[9],
            'birth_date' => $formattedDate,
            'address' => $row[11]
        ]);

    }
}

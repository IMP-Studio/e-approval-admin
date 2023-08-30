<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class EmployeeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (empty($row[1])) {
            // Handle jika nama kosong
            return null;
        }

        $user = User::create([
            'name' => $row[1],
            'email' => $row[2],
            'role' => 'employees',
            'password' => bcrypt('password@123'),
        ]);


        $division = Division::where('division', $row[8])->first();

        if (!$division) {
            $division = Division::firstOrCreate(['division' => 'Default Division']);
        }
        // $imgFilename = $row[3]; // Assuming $row[3] contains the image filename with extension

        // // Handle image upload and storage
        // $imgPath = null;
        // if (!empty($imgFilename)) {
        //     $imgExtension = pathinfo($imgFilename, PATHINFO_EXTENSION);
        //     $newImgFilename = Str::random(20) . '.' . $imgExtension;

        //     // Simpan gambar ke penyimpanan lokal (storage/app/public/images)
        //     Storage::disk('public')->putFileAs('images', public_path($imgFilename), $newImgFilename);

        //     $imgPath = 'images/' . $newImgFilename;
        // }


        return new Employee([
            'avatar' => $row[3],
            'first_name' => $row[4],
            'last_name' => $row[5],
            'gender' => $row[6],
            'id_number' => $row[7],
            'user_id' => $user->id,
            'division_id' => $division->id,
            'birth_date' => $row[9],
            'address' => $row[10]
        ]);

    }
    public function rules(): array
    {
        return [
            '2' => 'required|email|unique:users,email',
            '8' => 'required',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '2' => 'email',
            '8' => 'division',
        ];
    }
}

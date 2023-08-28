<?php

namespace App\Imports;

use App\Models\division;
use App\Models\employee;
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

        
        $division = division::where('division', $row[8])->first();

        if (!$division) {
            $division = division::firstOrCreate(['division' => 'Default Division']);
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
        

        return new employee([
            'img_profile' => $row[3],
            'firstname' => $row[4],
            'lastname' => $row[5],
            'gender' => $row[6],
            'staff_id' => $row[7],
            'user_id' => $user->id,
            'division_id' => $division->id,
            'date_of_birth' => $row[9],
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

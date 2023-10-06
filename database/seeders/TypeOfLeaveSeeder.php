<?php

namespace Database\Seeders;

use App\Models\LeaveDetail;
use App\Models\TypeOfLeave;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TypeOfLeaveSeeder extends Seeder
{

    public function run(): void
    {
        $type_leave = [
            ['id' => 1,'leave_name' => 'yearly'],
            ['id' => 2,'leave_name' => 'exclusive'],
            ['id' => 3,'leave_name' => 'emergency'],
        ];

        $leave_detail = [
            //YEARLY
            [
                'id' => 1,
                'type_of_leave_id' => '1',
                'description_leave' =>'Cuti tahunan', 
                'days' => 0 
            ],

            //EXCLUSIVE
            [
                'id' => 2,
                'type_of_leave_id' => '2',
                'description_leave' =>'Menikah', 
                'days' => 3 
            ],
            [
                'id' => 3,
                'type_of_leave_id' => '2',
                'description_leave' =>'Menikahkan anak', 
                'days' => 2 
            ],
            [
                'id' => 4,
                'type_of_leave_id' => '2',
                'description_leave' =>'Mengkhitankan anak', 
                'days' => 2 
            ],
            [
                'id' => 5,
                'type_of_leave_id' => '2',
                'description_leave' =>'Membaptiskan anak', 
                'days' => 2 
            ],
            [
                'id' => 6,
                'type_of_leave_id' => '2',
                'description_leave' =>'Istri melahirkan', 
                'days' => 90 
            ],
            [
                'id' => 7,
                'type_of_leave_id' => '2',
                'description_leave' => 'Keguguran', 
                'days' => 45 
            ],
            [
                'id' => 8,
                'type_of_leave_id' => '2',
                'description_leave' => 'Melakukan ibadah haji', 
                'days' => 40 
            ],
            [
                'id' => 9,
                'type_of_leave_id' => '2',
                'description_leave' => 'Umroh', 
                'days' => 10 
            ], 

            //EMERGENCY
            [
                'id' => 10,
                'type_of_leave_id' => '3',
                'description_leave' => 'Saudara dalam satu rumah meninggal dunia', 
                'days' => 1 
            ],
            [
                'id' => 11,
                'type_of_leave_id' => '3',
                'description_leave' => 'Suami/Istri, Orangtua/Mertua/Anak/Menantu meninggal dunia', 
                'days' => 2 
            ],  
             
            [
                'id' => 12,
                'type_of_leave_id' => '3',
                'description_leave' => 'Merawat anak karyawan yang sakit dengan ketentuan anak berusia maksimal 6 (enam) tahun', 
                'days' => 3
            ],  
            [
                'id' => 13,
                'type_of_leave_id' => '3',
                'description_leave' => 'Merawat anggota keluarga karyawan yang sakit',
                'days' => 3
            ],  
        ];

        foreach ($type_leave as $data) {
            TypeOfLeave::insert([
                'id' => $data['id'],
                'leave_name' => $data['leave_name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        foreach ($leave_detail as $data) {
            LeaveDetail::insert([
                'id' => $data['id'],
                'type_of_leave_id' => $data['type_of_leave_id'],
                'description_leave' => $data['description_leave'],
                'days' => $data['days'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
}
}
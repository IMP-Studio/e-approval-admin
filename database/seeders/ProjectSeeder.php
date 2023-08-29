<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data_partner = [
            ['id' => 1,'name' => 'BNN',],
            ['id' => 2,'name' => 'Direktorat Pendidikan Agama Islam',],
            ['id' => 3,'name' => 'Madrasah Reform',],
            ['id' => 4,'name' => 'Direktorat Bimbingan Masyarakat Katolik',],
            ['id' => 5,'name' => 'Beasiswa Arjuna',],
            ['id' => 6,'name' => 'Telkom Indonesia',],
            ['id' => 7,'name' => 'Biro Humas Data dan Informasi',],
        ];
        $data_project = [
            ['id' => 1,'name' => 'BNN Aplikasi Tukin', 'partner_id' => 1,],
            ['id' => 2,'name' => 'Biaya Server Aplikasi SIAGA','partner_id' => 2,],
            ['id' => 3,'name' => 'Manage Service Layanan Kelembagaan Madrasah','partner_id' => 3,],
            ['id' => 4,'name' => 'Sewa Aplikasi Space','partner_id' => 4,],
            ['id' => 5,'name' => 'Aplikasi Beasiswa Arjuna','partner_id' => 5,],
            ['id' => 6,'name' => 'MAINTENANCE & SERVER TELKOM DIGIREVIEW 2.0','partner_id' => 6,],
            ['id' => 7,'name' => 'Sistem Pengaduan dan Aspirasi Publik Implementasi Modeasi Beragama','partner_id' => 7,],
        ];

        foreach ($data_partner as $data) {
            Partner::insert([
                'id' => $data['id'],
                'name' => $data['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        foreach ($data_project as $data) {
            Project::insert([
                'id' => $data['id'],
                'name' => $data['name'],
                'partner_id' => $data['partner_id'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

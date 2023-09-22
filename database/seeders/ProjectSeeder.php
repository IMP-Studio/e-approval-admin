<?php

namespace Database\Seeders;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Partner;
use App\Models\Project;
use App\Models\StandUp;
use App\Models\Perjadin;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\LeaveStatus;
use Faker\Factory as Faker;
use App\Models\StatusCommit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');


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
            ['id' => 1,'name' => 'BNN Aplikasi Tukin', 'partner_id' => 1,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 2,'name' => 'Biaya Server Aplikasi SIAGA','partner_id' => 2,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 3,'name' => 'Manage Service Layanan Kelembagaan Madrasah','partner_id' => 3,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 4,'name' => 'Sewa Aplikasi Space','partner_id' => 4,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 5,'name' => 'Aplikasi Beasiswa Arjuna','partner_id' => 5,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 6,'name' => 'MAINTENANCE & SERVER TELKOM DIGIREVIEW 2.0','partner_id' => 6,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
            ['id' => 7,'name' => 'Sistem Pengaduan dan Aspirasi Publik Implementasi Modeasi Beragama','partner_id' => 7,'start_date' => '2023-01-01', 'end_date' => '2023-02-01'],
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
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

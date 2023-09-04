<?php

namespace Database\Seeders;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Perjadin;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\LeaveStatus;
use App\Models\StatusCommit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PresenceSeeder extends Seeder
{
    public function run(): void
    {
        $data_presences = [
            ['user_id' => 2,'category' => 'telework','latitude' => null,'longitude' => null],
            ['user_id' => 3,'category' => 'telework','latitude' => null,'longitude' => null],
            ['user_id' => 4,'category' => 'WFO','latitude' => '123456.78','longitude' => '123456.78'],
            ['user_id' => 5,'category' => 'WFO','latitude' => '123456.78','longitude' => '123456.78'],
            ['user_id' => 6,'category' => 'work_trip','latitude' => null,'longitude' => null],
            ['user_id' => 7,'category' => 'work_trip','latitude' => null,'longitude' => null],
            ['user_id' => 8,'category' => 'leave','latitude' => null,'longitude' => null],
        ];
        $data_teleworks = [
            ['user_id' => 2,'presence_id' => 1,'telework_category' => 'kesehatan','category_description' => null],
            ['user_id' => 3,'presence_id' => 2,'telework_category' => 'other','category_description' => 'Jalan depan rumah lagi diaspal'],
        ];
        $data_work_trip = [
            ['user_id' => 6,'presence_id' => 5],
            ['user_id' => 7,'presence_id' => 6],
        ];
        $data_leave = [
            [
                'user_id' => 8,'presence_id' => 7,'submission_date' => '2023-09-10','type' => 'yearly','start_date' => '2023-09-15-',
                'end_date' => '2023-09-20','total_leave_days' => '20 days','entry_date' => '2023-09-21','description' => 'pulang kampung'
            ]
        ];
        foreach ($data_presences as $data) {
            Presence::create([
                'user_id' => $data['user_id'],
                'category' => $data['category'],
                'entry_time' => Carbon::now(),
                'temporary_entry_time' => Carbon::now(),
                'date' => Carbon::now(),
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
        }

        // Seeding Teleworks
        foreach ($data_teleworks as $data) {
            Telework::create([
                'user_id' => $data['user_id'],
                'presence_id' => $data['presence_id'],
                'telework_category' => $data['telework_category'],
                'category_description' => $data['category_description'],
                'face_point' => 'qwertyuiop',
            ]);
        }

        // Seeding WorkTrips
        foreach ($data_work_trip as $data) {
            WorkTrip::create([
                'user_id' => $data['user_id'],
                'presence_id' => $data['presence_id'],
                'file' => 'contoh_file',
                'start_date' => '2023-07-26',
                'end_date' => '2023-07-28',
                'entry_date' => '2023-07-29',
                'face_point' => 'qwertyuiop',
            ]);
        }

        // Seeding Leaves
        foreach ($data_leave as $data) {
            Leave::create([
                'user_id' => $data['user_id'],
                'presence_id' => $data['presence_id'],
                'submission_date' => $data['submission_date'],
                'type' => $data['type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_leave_days' => $data['total_leave_days'],
                'type_description' => 'mati',
                'entry_date' => $data['entry_date'],
            ]);
        }

        $data_status_commits = [

            [
                'statusable_id' => 1,

                'statusable_type' => Telework::class,
                'description' => null,
                'status' => 'allowed',
            ],

            [
                'statusable_id' => 2,
                'statusable_type' => Telework::class,
                'description' => null,
                'status' => 'allowed',
            ],

            [

                'statusable_id' => 1,
                'statusable_type' => WorkTrip::class,
                'description' => null,
                'status' => 'allowed',
            ],
            [

                'statusable_id' => 2,
                'statusable_type' => WorkTrip::class,
                'description' => null,
                'status' => 'allowed',
            ],

            [
                
                'statusable_id' => 1,
                'statusable_type' => Leave::class,
                'description' => null,
                'status' => 'allowed',
            ],
            [

                'statusable_id' => 2,
                'statusable_type' => Leave::class,
                'description' => null,
                'status' => 'allowed',
            ],
        ];

        foreach ($data_status_commits as $data) {
            StatusCommit::create($data);
        }

    }
}

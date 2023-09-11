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
use App\Models\StandUp;
use App\Models\StatusCommit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;


class PresenceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 200 ; $i++) {
            $category = $faker->randomElement([
                'WFO', 'WFO','WFO',
                'telework', 'work_trip', 'leave'
            ]);
            $latitude = null;
            $longitude = null;
            if ($category === 'WFO') {
                $latitude = $faker->latitude;
                $longitude = $faker->longitude;
            }
            $entry_time = $faker->dateTimeBetween('07:30:00', '12:30:00')->format('H:i:s');
            $date = $faker->dateTimeBetween('2023-01-01', '2023-12-31')->format('Y-m-d');

            $presenceId = Presence::insertGetId([
                'user_id' => $faker->numberBetween(2,51),
                'category' => $category,
                'entry_time' => $entry_time,
                'temporary_entry_time' => $faker->time('H:i:s'),
                'date' => $date,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            $presenceDate = Carbon::parse($date)->copy();

            if ($category === 'telework') {
                $teleworkCategory = $faker->randomElement(['kesehatan','pendidikan','keluarga','other']);
                $categoryDescription = null;

                if ($teleworkCategory === 'other') {
                    $categoryDescription = $faker->sentence;
                }

                $telework = Telework::create([
                    'user_id' => $faker->numberBetween(2,51),
                    'presence_id' => $presenceId,
                    'telework_category' => $teleworkCategory,
                    'category_description' => $categoryDescription,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $telework->id;
                $statusableType = Telework::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            } elseif ($category === 'work_trip') {
                $start_date = $presenceDate->addDays(2)->toDateString();
                $end_date = $presenceDate->addDays(random_int(1,3))->toDateString();
                $entry_date = $presenceDate->addDays(random_int(1,2))->toDateString();

                $workTrip = WorkTrip::create([
                    'user_id' => $faker->numberBetween(2,51),
                    'presence_id' => $presenceId,
                    'file' => 'contoh_file',
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'entry_date' => $entry_date,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $workTrip->id;
                $statusableType = WorkTrip::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            } elseif ($category === 'leave') {
                $start_date = $presenceDate->addDays(2)->toDateString();
                $end_date = $presenceDate->addDays(random_int(2, 4))->toDateString();
                $entry_date = $presenceDate->addDays(random_int(1, 2))->toDateString();
                $total_leave_days = Carbon::parse($start_date)->diffInDays($end_date) + 1;

                $leave = Leave::create([
                    'user_id' => $faker->numberBetween(2,51),
                    'presence_id' => $presenceId,
                    'submission_date' => $date,
                    'type' => $faker->randomElement(['yearly','exclusive','emergency']),
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'total_leave_days' => $total_leave_days,
                    'type_description' => $faker->sentence(random_int(3,5)),
                    'entry_date' => $entry_date,
                ]);

                $statusableId = $leave->id;
                $statusableType = Leave::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            }
            if ($category !== 'leave') {
                StandUp::create([
                    'user_id' => $faker->numberBetween(2,51),
                    'presence_id' => $presenceId,
                    'project_id' => $faker->numberBetween(1,7),
                    'done' => $faker->sentence(3),
                    'doing' => $faker->sentence(3),
                    'blocker' => $faker->optional()->sentence(1),
                ]);
            }
        }
        // PRESENCE TODAY
        for ($i = 1; $i <= 10; $i++) {
            $category = $faker->randomElement([
                'WFO', 'WFO', 'WFO',
                'telework', 'work_trip', 'leave'
            ]);

            $latitude = null;
            $longitude = null;

            if ($category === 'WFO') {
                $latitude = $faker->latitude;
                $longitude = $faker->longitude;
            }

            $entry_time = $faker->dateTimeBetween('07:30:00', '12:30:00')->format('H:i:s');
            $date = Carbon::now()->setTimezone('Asia/Jakarta'); // Menggunakan Carbon untuk mendapatkan tanggal hari ini

            $presenceId = Presence::insertGetId([
                'user_id' => $faker->numberBetween(2, 51),
                'category' => $category,
                'entry_time' => $entry_time,
                'temporary_entry_time' => $faker->time('H:i:s'),
                'date' => $date,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            if ($category === 'telework') {
                $teleworkCategory = $faker->randomElement(['kesehatan', 'pendidikan', 'keluarga', 'other']);
                $categoryDescription = null;

                if ($teleworkCategory === 'other') {
                    $categoryDescription = $faker->sentence;
                }

                $telework = Telework::create([
                    'user_id' => $faker->numberBetween(2, 51),
                    'presence_id' => $presenceId,
                    'telework_category' => $teleworkCategory,
                    'category_description' => $categoryDescription,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $telework->id;
                $statusableType = Telework::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            } elseif ($category === 'work_trip') {
                $start_date = $date->addDays(2)->toDateString();
                $end_date = $date->addDays(random_int(1, 3))->toDateString();
                $entry_date = $date->addDays(random_int(1, 2))->toDateString();

                $workTrip = WorkTrip::create([
                    'user_id' => $faker->numberBetween(2, 51),
                    'presence_id' => $presenceId,
                    'file' => 'contoh_file',
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'entry_date' => $entry_date,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $workTrip->id;
                $statusableType = WorkTrip::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            } elseif ($category === 'leave') {
                $start_date = $date->addDays(2)->toDateString();
                $end_date = $date->addDays(random_int(2, 4))->toDateString();
                $entry_date = $date->addDays(random_int(1, 2))->toDateString();
                $total_leave_days = Carbon::parse($start_date)->diffInDays($end_date) + 1;

                $leave = Leave::create([
                    'user_id' => $faker->numberBetween(2, 51),
                    'presence_id' => $presenceId,
                    'submission_date' => $date,
                    'type' => $faker->randomElement(['yearly', 'exclusive', 'emergency']),
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'total_leave_days' => $total_leave_days,
                    'type_description' => $faker->sentence(random_int(3, 5)),
                    'entry_date' => $entry_date,
                ]);

                $statusableId = $leave->id;
                $statusableType = Leave::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $faker->sentence,
                    'status' => $status,
                ]);
            }
            if ($category !== 'leave') {
                StandUp::create([
                    'user_id' => $faker->numberBetween(2,51),
                    'presence_id' => $presenceId,
                    'project_id' => $faker->numberBetween(1,7),
                    'done' => $faker->sentence(3),
                    'doing' => $faker->sentence(3),
                    'blocker' => $faker->optional()->sentence(1),
                ]);
            }
        }

    }
}

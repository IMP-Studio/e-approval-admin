<?php

namespace Database\Seeders;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\StandUp;
use App\Models\Perjadin;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\LeaveDetail;
use App\Models\LeaveStatus;
use Faker\Factory as Faker;
use App\Models\StatusCommit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class PresenceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 200 ; $i++) {
            $category = $faker->randomElement([
                'WFO', 'WFO', 'WFO', 'telework', 'work_trip', 'leave', 'skip' // Added 'skip' here
            ]);
        
            $latitude = null;
            $longitude = null;
            $entry_time = '00:00:00';
            $date = now()->format('Y-m-d');
        
            if ($category === 'WFO') {
                $latitude = -6.332835026352704;
                $longitude =  106.86452087283757;
                $entry_time = $faker->dateTimeBetween('07:30:00', '12:30:00')->format('H:i:s');
                $date = $faker->dateTimeBetween('2023-01-01', '2023-12-31')->format('Y-m-d');
            } elseif ($category === 'telework' || $category === 'work_trip') {
                $entry_time = '08:30:00';
                $date = $faker->dateTimeBetween('2023-01-01', '2023-12-31')->format('Y-m-d');
            } elseif ($category === 'leave') {
                $entry_time = '00:00:00';
                $date = $faker->dateTimeBetween('2023-01-01', '2023-12-31')->format('Y-m-d');
            }
        
            $users_id = $faker->numberBetween(2,51);
        
            $presenceId = Presence::insertGetId([
                'user_id' => $users_id,
                'category' => $category,
                'entry_time' => $entry_time,
                'temporary_entry_time' => $category === 'skip' ? '00:00:00' : $faker->time('H:i:s'),
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
                    'user_id' => $users_id,
                    'presence_id' => $presenceId,
                    'telework_category' => $teleworkCategory,
                    'category_description' => $categoryDescription,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $telework->id;
                $statusableType = Telework::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
            } elseif ($category === 'work_trip') {
                $start_date = $presenceDate->addDays(2)->toDateString();
                $end_date = $presenceDate->addDays(random_int(1,3))->toDateString();
                $entry_date = $presenceDate->addDays(random_int(1,2))->toDateString();

                $workTrip = WorkTrip::create([
                    'user_id' => $users_id,
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
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
            } elseif ($category === 'leave') {
                $start_date = Carbon::now()->setTimezone('Asia/Jakarta');
                    $submission_date = Carbon::now()->setTimezone('Asia/Jakarta')->subDays($faker->numberBetween(1,2));

                    $randomLeaveDetail = LeaveDetail::inRandomOrder()->first();
                    
                    $end_date = $start_date->copy()->addDays($randomLeaveDetail->days - 1); 
                    $entry_date = $end_date->copy()->addDays(random_int(1, 2));
                
                    $leave = Leave::create([
                        'user_id' => $users_id,
                        'presence_id' => $presenceId,
                        'submission_date' => $submission_date,
                        'leave_detail_id' => $randomLeaveDetail->id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'total_leave_days' => $randomLeaveDetail->days,
                        'entry_date' => $entry_date,
                    ]);

                $statusableId = $leave->id;
                $statusableType = Leave::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
            }
            if ($category !== 'leave') {
                StandUp::create([
                    'user_id' => $users_id,
                    'presence_id' => $presenceId,
                    'project_id' => $faker->numberBetween(1,7),
                    'done' => $faker->sentence(3),
                    'doing' => $faker->sentence(3),
                    'blocker' => $faker->optional()->sentence(1),
                ]);
            }
        }
        // PRESENCE TODAY
        for ($i = 1; $i <= 20; $i++) {
            $category = $faker->randomElement([
                'WFO', 'WFO', 'WFO',
                'telework', 'work_trip', 'leave', 'skip' // Added 'skip' here
            ]);
        
            $latitude = null;
            $longitude = null;
            $entry_time = '00:00:00';
            $date = Carbon::now()->setTimezone('Asia/Jakarta');
        
            if ($category === 'WFO') {
                $latitude = $faker->latitude;
                $longitude = $faker->longitude;
                $entry_time = $faker->dateTimeBetween('07:30:00', '12:30:00')->format('H:i:s');
            } elseif ($category === 'telework' || $category === 'work_trip') {
                $entry_time = '08:30:00';
            } elseif ($category === 'leave') {
                $entry_time = '00:00:00';
            }
        
            $user_id = $faker->numberBetween(2,51);
        
            $presenceId = Presence::insertGetId([
                'user_id' => $user_id,
                'category' => $category,
                'entry_time' => $entry_time,
                'temporary_entry_time' => $category === 'skip' ? '00:00:00' : $faker->time('H:i:s'),
                'date' => $date,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        
            if ($category === 'skip') {
                continue; // Skip to the next iteration and don't create any associated data.
            }

            if ($category === 'telework') {
                $teleworkCategory = $faker->randomElement(['kesehatan', 'pendidikan', 'keluarga', 'other']);
                $categoryDescription = null;

                if ($teleworkCategory === 'other') {
                    $categoryDescription = $faker->sentence;
                }

                $telework = Telework::create([
                    'user_id' => $user_id,
                    'presence_id' => $presenceId,
                    'telework_category' => $teleworkCategory,
                    'category_description' => $categoryDescription,
                    'face_point' => $faker->text(100),
                ]);

                $statusableId = $telework->id;
                $statusableType = Telework::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
            } elseif ($category === 'work_trip') {
                $start_date = Carbon::now();
                $end_date = $date->addDays(random_int(1, 3))->toDateString();
                $entry_date = $date->addDays(random_int(1, 2))->toDateString();

                $workTrip = WorkTrip::create([
                    'user_id' => $user_id,
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
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
                
            } elseif ($category === 'leave') {
                $start_date = Carbon::now()->setTimezone('Asia/Jakarta');
                    $submission_date = Carbon::now()->setTimezone('Asia/Jakarta')->subDays($faker->numberBetween(1,2));
                
                    $randomLeaveDetail = LeaveDetail::inRandomOrder()->first();
                    
                    $end_date = $start_date->copy()->addDays($randomLeaveDetail->days - 1); // Adjust this as necessary
                    $entry_date = $end_date->copy()->addDays(random_int(1, 2));
                
                    $leave = Leave::create([
                        'user_id' => $user_id,
                        'presence_id' => $presenceId,
                        'submission_date' => $submission_date,
                        'leave_detail_id' => $randomLeaveDetail->id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'total_leave_days' => $randomLeaveDetail->days,
                        'entry_date' => $entry_date,
                    ]);

                $statusableId = $leave->id;
                $statusableType = Leave::class;
                $status = $faker->randomElement(['allowed', 'rejected', 'allowed']);
                $description = '';
                if ($status === 'rejected') {
                    $description = $faker->sentence(1);
                }

                StatusCommit::create([
                    'statusable_id' => $statusableId,
                    'statusable_type' => $statusableType,
                    'description' => $description,
                    'status' => $status,
                ]);
            }
            if ($category !== 'leave') {
                StandUp::create([
                    'user_id' => $user_id,
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

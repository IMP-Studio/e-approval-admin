<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\User;  
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

//--- COMMAND UNTUK USER YANG BOLOS ---\\

class MarkSkippedUsersCommand extends Command
{
    protected $signature = 'users:mark-skipped';
    protected $description = 'Mark users who skipped work';
    
    public function handle()
    {
        try {
            $today = now()->toDateString();
        $currentTime = now()->format('H:i');
        $startYear = intval(date('Y', strtotime(now()->format('Y'))));
        $endYear = intval(date('Y', strtotime(now()->format('Y'))));

        $holidaysWithWeekends = [];
    
        for ($year = $startYear; $year <= $endYear; $year++) {
            $apiUrl = "https://api-harilibur.vercel.app/api?year={$year}";
            $response = file_get_contents($apiUrl);
    
            if ($response) {
                $holidayData = json_decode($response, true);
    
                if ($holidayData) {
                    $holidays = $holidayData;
    
                    foreach ($holidays as &$holiday) {
                        if (isset($holiday['holiday_date'])) {
                            $dateParts = explode('-', $holiday['holiday_date']);
                            $holiday['holiday_date'] = $dateParts[0] . '-' . str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dateParts[2], 2, '0', STR_PAD_LEFT);
                        }
                    }
    
                    $startDate = Carbon::createFromDate($year, 1, 1);
                    $endDate = Carbon::createFromDate($year, 12, 31);
    
                    while ($startDate->lte($endDate)) {
                            if ($startDate->isWeekend()) {
                                $weekendDate = $startDate->format('Y-m-d');
                                $holidays[] = [
                                    'holiday_name' => 'Weekend',
                                    'holiday_date' => $weekendDate,
                                    'is_national_holiday' => true,
                                ];
                            }     
                        $startDate->addDay();
                    }
    
                    $nationalHolidays = array_filter($holidays, function ($holiday) {
                        return isset($holiday['is_national_holiday']) ? $holiday['is_national_holiday'] === true : true;
                    });
    
                    $holidaysWithWeekends = array_merge($holidaysWithWeekends, $nationalHolidays);
                } else {
                    echo 'Failed to parse JSON response for year ' . $year . '.';
                }
            } else {
                echo 'Failed to fetch data from the API for year ' . $year . '.';
            }
        }


        $isHolidayOrWeekend = false;

        foreach ($holidaysWithWeekends as $holiday) {
            if ($today === $holiday['holiday_date']) {
                $isHolidayOrWeekend = true;
                break;
            }
        }

        if (!$isHolidayOrWeekend) {

            if ($currentTime >= '11:00') {
                $usersWhoHaveNotMarked = User::leftJoin('presences', function ($join) use ($today) {
                    $join->on('users.id', '=', 'presences.user_id')
                        ->where('presences.date', $today);
                })
                    ->whereNull('presences.id')
                    ->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'super-admin');
                    })
                    ->select('users.id')
                    ->get();

                foreach ($usersWhoHaveNotMarked as $user) {
                    DB::table('presences')->insert([
                        'user_id' => $user->id,
                        'category' => 'skip',
                        'entry_time' => '00:00:00',
                        'exit_time' => '00:00:00',
                        'temporary_entry_time' => '00:00:00',
                        'date' => $today,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $this->info('Marked users who skipped work today. (Menandakan users yang bolos kerja hari ini)');
                $this->info('Found ' . $usersWhoHaveNotMarked->count() . ' users to checkout.');
            } else {
                $this->info('The command is only executable after 11:00.');
            }
        } else {
            $this->info('Skipping marking users because today is a holiday or weekend.');
        }
    
            $this->info('Command executed successfully.');
        } catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $stackTrace = $e->getTraceAsString();
        
            // Log the error
            Log::error($errorMessage);
            Log::error($stackTrace);
        
            // Display the error in the console
            $this->error($errorMessage);
            $this->error($stackTrace);
        }
        
    }
  
}
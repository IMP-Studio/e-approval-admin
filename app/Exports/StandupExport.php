<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\StandUp;
use Carbon\Carbon;

class StandupExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;  
    }

    public function sheets(): array
    {
        $sheets = [];
        $month = $this->month;
        $year = $this->year;
    
        $startDate = Carbon::createFromDate($this->year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $employees = Employee::all();
        $debugDate = '2023-12-20';
    
        for ($day = 1; $day <= $endDate->day; $day++) {
            $currentDate = $startDate->copy()->addDays($day - 1);
    
            $employeeStandupsid = StandUp::whereHas('presence', function ($query) use ($currentDate) {
                $query->whereDate('date', $currentDate);
            })->pluck('user_id');

            $employeeStandups = StandUp::whereHas('presence', function ($query) use ($currentDate) {
                $query->whereDate('date', $currentDate);
            })->get();
    
            $employeesWithoutStandups = $employees->reject(function ($employee) use ($employeeStandupsid) {
                return in_array($employee->user_id, $employeeStandupsid->all());
            })->map(function ($employee) use ($currentDate) {
                return [
                    'user_id' => $employee->user_id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'date' => $currentDate->toDateString(),
                    'position' => $employee->position->name,
                    'division' => $employee->division->name,
                    'gender' => $employee->gender == 'male' ? 'L' : 'P' ?? 'Unknown',

                ];
            });
    
            $title = $currentDate->format('d F Y');
            $employeesWithoutStandups = $employeesWithoutStandups->sortBy('date')->all();
    
            $sheets[] = new StandupSheet($title, $year ,$currentDate->month, $employeeStandups, $employeesWithoutStandups);
        }
    
        return $sheets;
    }
}

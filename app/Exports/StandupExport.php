<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\StandUp;
use Carbon\Carbon;

class StandupExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::createFromDate($this->year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $standups = StandUp::whereHas('presence', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })->get();

            $sheets[] = new StandupSheet($month, $standups);
        }

        return $sheets;
    }
}

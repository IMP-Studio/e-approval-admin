<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class PresenceExport implements WithMultipleSheets
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
            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $sheets[] = new PresenceSheet($this->year, $month, $monthName);
        }

        return $sheets;
    }
}


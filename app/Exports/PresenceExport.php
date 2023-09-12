<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class PresenceExport implements FromQuery, WithMultipleSheets
{
    use Exportable;

    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function query()
    {
        // Sesuaikan query Anda untuk mengambil data presence berdasarkan tahun
        return Presence::query()->whereYear('date', $this->year);
    }

    public function sheets(): array
    {
        $sheets = [];

        // Loop melalui 12 bulan
        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $sheets[] = new PresenceSheet($this->year, $month, $monthName);
        }

        return $sheets;
    }
}


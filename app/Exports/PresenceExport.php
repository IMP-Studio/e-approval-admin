<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Presence; // Pastikan Anda mengimpor model Presence sesuai dengan namespace yang benar

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

        // Determine the actual start and end dates based on the year and month
        $startDate = Carbon::create($this->year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, $month, 1)->endOfMonth();

        // Ambil data absensi sesuai bulan dan tahun
        $absenceData = Presence::whereYear('date', $this->year)
            ->whereMonth('date', $month)
            ->get();

        // Add PresenceResumYearSheet for each month
        $sheets[] = new PresenceResumYearSheet($absenceData, $monthName, $startDate, $endDate, $this->year);

        // Add PresenceRekapYearSheet for each month
        $sheets[] = new PresenceRekapYearSheet($absenceData, $monthName, $startDate, $endDate);
    }

    return $sheets;
}

    
}

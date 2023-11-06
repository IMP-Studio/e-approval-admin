<?php

namespace App\Exports;

use App\Exports\PresenceByRangeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PresenceByRangeExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }


    public function sheets(): array
    {
        $sheets = [
            new PresenceRekapByRangeSheet($this->startDate, $this->endDate),
            new PresenceResumByRangeSheet($this->startDate, $this->endDate),
        ];


        return $sheets;
    }
}

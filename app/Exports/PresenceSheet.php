<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class PresenceSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithColumnWidths
{
    private $iteration = 0;

    protected $year;
    protected $month;
    protected $monthName;

    public function collection()
    {
        return Presence::all();
    }
    public function startCell(): string
    {
        return 'B2';
    }
    public function __construct($year, $month, $monthName)
    {
        $this->year = $year;
        $this->month = $month;
        $this->monthName = $monthName;
    }

    public function query()
    {
        return Presence::query()
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month);
    }

    public function title(): string
    {
        return $this->monthName;
    }
    public function map($presence): array
    {
        $this->iteration++;
        return [
            $this->iteration,
            $presence->user->name,
            $presence->user->employee->position->name,
            $presence->date,
            $presence->temporary_entry_time,
            $presence->entry_time,
            $presence->exit_time,
            ucwords(strtolower($presence->category === 'work_trip' ? 'Work Trip' : $presence->category)), // Mengubah teks menjadi capitalize
        ];
    }

    public function headings(): array
    {
        return [
            ["Data Presence $this->monthName"],
            [
                'No',
                'Username',
                'Position',
                'Date',
                'Temporary Entry Time',
                'Entry Time',
                'Exit Time',
                'Category',
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => [
                'font' => ['size' => 13,'bold' => true],
            ],
            3 => [
                'font' => ['size' => 12,'bold' => true],
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 4,
            'C' => 26,
            'D' => 28,
            'E' => 14,
            'F' => 22,
            'G' => 15,
            'H' => 15,
            'I' => 16,
        ];
    }
}



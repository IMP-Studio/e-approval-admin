<?php

namespace App\Exports;

use App\Models\Presence;
use App\Models\StandUp;
use Maatwebsite\Excel\Concerns\Exportable;
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
    use Exportable;
    private $iteration = 0;

    protected $year;
    protected $month;
    protected $monthName;

    public function __construct($year, $month, $monthName)
    {
        $this->year = $year;
        $this->month = $month;
        $this->monthName = $monthName;
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function query()
    {
        return Presence::query()
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->orderBy('date','asc');
    }

    public function title(): string
    {
        return $this->monthName;
    }

    public function headings(): array
    {
        return [
            ["Data Presence $this->monthName",'','','','','','','','',"Standup $this->monthName"],
            [
                'No',
                'Username',
                'Position',
                'Date',
                'Temporary Entry Time',
                'Entry Time',
                'Exit Time',
                'Category',
                '',
                'Done',
                'Doing',
                'Blocker'
            ]
        ];
    }

    public function map($presence): array
    {
        $this->iteration++;

        if ($presence->category === 'leave') {
            $standup = null;
        } else {
            $standup = StandUp::where('user_id', $presence->user_id)->first();
        }

        $done = '';
        $doing = '';
        $blocker = '';

        if ($standup !== null) {
            $done = $standup->done;
            $doing = $standup->doing;
            $blocker = $standup->blocker;
        }

        return [
            $this->iteration,
            $presence->user->name,
            $presence->user->employee->position->name,
            $presence->date,
            $presence->temporary_entry_time,
            $presence->entry_time,
            $presence->exit_time,
            ucwords(strtolower($presence->category === 'work_trip' ? 'Work Trip' : $presence->category)),
            '',
            $done,
            $doing,
            $blocker,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 8,
            'C' => 26,
            'D' => 28,
            'E' => 14,
            'F' => 24,
            'G' => 15,
            'H' => 15,
            'I' => 16,
            'J' => 4,
            'K' => 22,
            'L' => 22,
            'M' => 22
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        for ($col = 'C'; $col <= $lastColumn; $col++) {
            $sheet->getStyle($col)->getAlignment()->setVertical('center');
            $sheet->getStyle($col)->getAlignment()->setIndent(1);
        }
        return [
            2 => [
                'font' => ['size' => 13,'bold' => true],
            ],
            3 => [
                'font' => ['size' => 12,'bold' => true],
            ],
            'B3:B' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'F4:F' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'J' => [
                'alignment' => ['wrapText' => true],
            ],
            'K' => [
                'alignment' => ['wrapText' => true],
            ],
            'L' => [
                'alignment' => ['wrapText' => true],
            ],
        ];
    }


}



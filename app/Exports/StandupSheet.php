<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\StandUp;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StandupSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomStartCell, WithColumnWidths, WithStyles
{
    private $iteration = 0;
    protected $month;
    protected $standups;

    public function __construct($month, $standups)
    {
        $this->month = $month;
        $this->standups = $standups->sortBy('presence.date');
    }

    public function collection()
    {
        return $this->standups;
    }

    public function title(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        return [
            ["Data Standup $monthName"],
            [
                'No',
                'Username',
                'Position',
                'Date',
                'Done',
                'Doing',
                'Blocker',
            ]
        ];
    }

    public function map($standup): array
    {
        $this->iteration++;

        return [
            $this->iteration,
            $standup->user->name,
            $standup->user->employee->position->name,
            $standup->presence->date,
            $standup->done,
            $standup->doing,
            $standup->blocker,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 8,
            'C' => 26,
            'D' => 28,
            'E' => 14,
            'F' => 28,
            'G' => 28,
            'H' => 28,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        for ($col = 'C'; $col <= $lastColumn; $col++) {
            $sheet->getStyle($col)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
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
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'F' => [
                'alignment' => ['wrapText' => true],
            ],
            'G' => [
                'alignment' => ['wrapText' => true],
            ],
            'H' => [
                'alignment' => ['wrapText' => true],
            ]
        ];
    }
}


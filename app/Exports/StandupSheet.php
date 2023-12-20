<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\StandUp;
use Carbon\Carbon;
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
        $earliestDate = Carbon::parse($this->standups->min('presence.date'))->format('d');
        $latestDate = Carbon::parse($this->standups->max('presence.date'))->format('d');
        
        $dateRange = $earliestDate . ' ' . $monthName . ' - ' . $latestDate . ' ' .  $monthName;
                

        return [
            ["Data Standup $monthName"],
            [$dateRange],
            [
                'No',
                'Tanggal',
                'Nama Lengkap',
                'Posisi',
                'Divisi',
                'L/P',
                'Project Name',
                'Dong',
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
            $standup->presence->date,
            $standup->user->name,
            $standup->user->employee->position->name,
            $standup->user->employee->division->name,
            $standup->user->employee->gender == 'male' ? 'L' : 'P',
            $standup->project->name,
            $standup->done,
            $standup->doing,
            $standup->blocker,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 5,
            'F' => 22,
            'G' => 8,
            'H' => 38,
            'I' => 38,
            'J' => 38,
            'K' => 38,
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

        $sheet->getStyle('C5:C'. $lastRow)->getAlignment()->setTextRotation(90)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]); 
        
        $lastDate = null;
        $mergeStartRow = 0;
    
        for ($row = 5; $row <= $lastRow; $row++) {
            $currentDate = $sheet->getCell('C' . $row)->getValue();
    
            // Check if the current date is the same as the previous one
            if ($currentDate === $lastDate) {
                // If it's consecutive, update the merge end row
                $mergeEndRow = $row;
            } else {
                // If not consecutive, check if there was a previous merge
                if (isset($mergeStartRow) && isset($mergeEndRow) && $mergeEndRow > $mergeStartRow) {
                    // Calculate the number of rows to merge
                    $mergeRowCount = $mergeEndRow - $mergeStartRow + 1;

                    // Set the row height based on the number of merged rows
                    $sheet->getRowDimension($mergeStartRow)->setRowHeight($mergeRowCount * 35); // Adjust 20 as needed

                    // Merge cells for the current date
                    $sheet->mergeCells("C{$mergeStartRow}:C{$mergeEndRow}");
                }
    
                // Update the start of the next potential merge
                $mergeStartRow = $row;
            }
    
            // Update the last date for the next iteration
            $lastDate = $currentDate;
        }
    
        // Merge cells for the last set of consecutive dates (if any)
        if (isset($mergeStartRow) && isset($mergeEndRow) && $mergeEndRow > $mergeStartRow) {
             // Calculate the number of rows to merge
            $mergeRowCount = $mergeEndRow - $mergeStartRow + 1;

            // Set the row height based on the number of merged rows
            $sheet->getRowDimension($mergeStartRow)->setRowHeight($mergeRowCount * 35); // Adjust 20 as needed

            $sheet->mergeCells("C{$mergeStartRow}:C{$mergeEndRow}");
        }

        $sheet->getStyle('B' . ($lastRow + 1) . ':' . $lastColumn . ($lastRow + 1))->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->mergeCells('B2:' . $lastColumn . '2');
        $sheet->getStyle('B2:' . $lastColumn . '2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 13, 'bold' => true],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->mergeCells('B3:' . $lastColumn . '3');
        $sheet->getStyle('B3:' . $lastColumn . '3')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 13, 'bold' => true],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B4:' . $lastColumn . '4')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 12, 'bold' => true],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C2D9FF'],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'vertical' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle($lastColumn . '5:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('C5:C' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getStyle('G5:G' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheet->getColumnDimension('E')->setAutoSize(true);

        return [
            2 => [
                'font' => ['size' => 13, 'bold' => true],
            ],
            3 => [
                'font' => ['size' => 12, 'bold' => true],
            ],
            'B3:B' . $lastRow => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            'B5:B' . $lastRow => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
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
            ],
            'I' => [
                'alignment' => ['wrapText' => true],
            ],
            'J' => [
                'alignment' => ['wrapText' => true],
            ],
            'K' => [
                  'alignment' => ['wrapText' => true],
            ]
        ];
    }    

}


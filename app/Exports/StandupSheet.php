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
    protected $year;
    protected $employeeStandups;
    protected $employeesWithoutStandups;
    protected $title;

    public function __construct($title, $year, $month, $employeeStandups, $employeesWithoutStandups)
    {
        $this->month = $month;
        $this->year = $year;
        $this->employeeStandups = $employeeStandups->sortBy('presence.date');
        $this->employeesWithoutStandups = $employeesWithoutStandups;
        $this->title = $title;
    }

    public function collection()
    {
         return $this->employeeStandups->concat($this->employeesWithoutStandups);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        $monthName = date('F', mktime(0, 0, 0, $this->month, 1));
        $title = $this->title;

        return [
            ["Data Standup $monthName"],
            [$title],
            [
                'No',
                'Tanggal',
                'Nama Lengkap',
                'Posisi',
                'Divisi',
                'L/P',
                'Project Name',
                'Done',
                'Doing',
                'Blocker',
            ]
        ];
    }


    public function map($data): array
    {
        $this->iteration++;
    
        if (is_array($data)) {
            // employeesWithoutStandups data
            return [
                $this->iteration,
                $data['date'],
                $data['name'],
                $data['position'],
                $data['division'],
                $data['gender'],
                'No Standup',
                'No Standup',
                'No Standup',
                'No Standup',
            ];
        } else {
            //  employeeStandups data
            return [
                $this->iteration,
                $data->presence->date,
                $data->user->name,
                $data->user->employee->position->name ?? 'Unknown',
                $data->user->employee->division->name ?? 'Unknown',
                $data->user->employee->gender == 'male' ? 'L' : 'P',
                $data->project->name ?? 'Unknown',
                $data->done ?? '-',
                $data->doing ?? '-',
                $data->blocker ?? '-',
            ];
        }
    }
    


    public function columnWidths(): array
    {
        return [
            'B' => 5,
            'C' => 18,
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
                'allBorders' => [
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
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('D5:D'  . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('E5:E' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('F5:F' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        

        $sheet->getStyle('G5:G' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('H5:H' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('I5:I' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J5:J' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('K5:K' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
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


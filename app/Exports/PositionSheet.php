<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PositionSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomStartCell, WithColumnWidths, WithStyles
{

    private $positions;
    private $iteration = 0;
    private $currentPosition = null;
    
    public function __construct($positions)
    {
        $this->positions = $positions;
    }

    public function collection()
    {
        $collection = [];

        $this->currentPosition = null;

        foreach ($this->positions as $position) {
            $isFirstPosition = true;

            foreach ($position->employee as $employee) {
                $this->iteration++;

                if ($this->currentPosition !== $position->name) {
                    if (!$isFirstPosition) {
                        $collection[] = [
                            'position' => $position->name,
                        ];
                    }
                    $this->currentPosition = $position->name;
                    $isFirstPosition = false;
                }

                
                // Determine the role based on permissions
                $permissions = $employee->user->permissions->pluck('id')->toArray();

                // Check if both 38 and 42 permissions exist for HR role
                if (in_array(38, $permissions) && in_array(42, $permissions)) {
                    $role = 'Human Resource';
                }
                // Check if both 39 and 43 permissions exist for HT role
                elseif (in_array(39, $permissions) && in_array(43, $permissions)) {
                    $role = 'Head Of Tribe';
                } else {
                    $role = 'Ordinary Employee';
                }

                $gender = ($employee->user->employee->gender == 'male') ? 'L' : 'P';

                $collection[] = [
                    $this->iteration,
                    $position->name,
                    $employee->user->name,
                    $employee->user->employee->division->name,
                    $role,
                    $gender
                ];
            }
        }
        return collect($collection);
    }

    public function title(): string
    {
        return 'Position Data';
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        $formattedDate = strtoupper(now()->format('d F Y'));
        return [
            ['Rekap Position'],
            [$formattedDate],
            [
                'NO',
                'Nama Position',
                'Nama Lengkap',
                'Divisi',
                'Role',
                'L / P',
            ],
        ];
    }

    public function map($row): array
    {
        return $row;
    }

    public function columnWidths(): array
    {
        return [
            'B' => 8,
            'C' => 22,
            'D' => 22,
            'E' => 28,
            'F' => 22,
            'G' => 8,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last column and row
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
    
        // Merge cells for "REKAP PEGAWAI"
        $sheet->mergeCells('B2:' . $lastColumn . '2');
    
        // Apply styles to "REKAP PEGAWAI"
        $this->applyStyleToCell($sheet, 'B2', 'c2d9ff', '000000');
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getStyle('B2')->getFont()->setSize(16);
    
        // Merge cells for "30 DESEMBER 2023"
        $sheet->mergeCells('B3:' . $lastColumn . '3');
    
        // Apply styles to "30 DESEMBER 2023"
        $this->applyStyleToCell($sheet, 'B3', 'c2d9ff', '000000');
        $sheet->getRowDimension(3)->setRowHeight(15);
        $sheet->getStyle('B3')->getFont()->setSize(12);

        $positionNameColumn = 'C';

        // Initialize row counter
        $rowCounter = 5;

        
        // Apply styles to the entire table
        $sheet->getStyle('D2:D'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // // Apply styles to the entire table
        $sheet->getStyle('E2:E'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // // Apply styles to the entire table
        $sheet->getStyle('F2:F'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // // Apply styles to the entire table
        $sheet->getStyle('G2:G'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Merge cells for each group of position data
        foreach ($this->positions as $position) {
            $startRow = $rowCounter;
            $endRow = $startRow + count($position->employee) - 1;

            if ($endRow > $startRow) {
                $mergeRange = $positionNameColumn . $startRow . ':' . $positionNameColumn . $endRow;
                $sheet->mergeCells($mergeRange);

                // Menambahkan border ke dalam kolom yang telah digabungkan
                for ($col = 'D'; $col <= $lastColumn; $col++) {
                    $columnRange = $col . $startRow . ':' . $col . $endRow;
                    $sheet->getStyle($columnRange)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                }
            }else {
                for ($col = 'D'; $col <= $lastColumn; $col++) {
                    $sheet->getStyle($col . $startRow)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                }
            }

            $rowCounter = $endRow + 1;
        }

        $sheet->getStyle($positionNameColumn . '5:' . $positionNameColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF'],
            ],
        ]);

        // Additional styling for the position name cells
        $sheet->getStyle($positionNameColumn . '5:' . $positionNameColumn . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ffffff'],
            ],
            'font' => [
                'color' => ['rgb' => '000000'],
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    
        // Apply styles to the entire table
        $sheet->getStyle('B2:B'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getColumnDimension($positionNameColumn)->setAutoSize(true);

        $sheet->getStyle('B2:'  . $lastColumn . '2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B4:' . $lastColumn . '4')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c2d9ff'],
            ],
            'font' => [
                'color' => ['rgb' => '000000'],
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    
        
        // Apply styles to the entire table
        $sheet->getStyle('B2:B'  . $lastRow)->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);


        // Apply styles to the entire table
        $sheet->getStyle('G2:G'  . $lastRow)->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        $sheet->getStyle('B4:B' . $lastRow)->applyFromArray([
            'font' => [
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getStyle('G4:G' . $lastRow)->applyFromArray([
            'font' => [
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $sheet->getStyle('B5:' . $lastColumn . $lastRow)->applyFromArray([
            'font' => [
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        for ($i = 5; $i <= $lastRow; $i++) {
            $sheet->getStyle('B' . $i . ':' . $lastColumn . $i)->getFont()->setBold(false);
        }

        $footerRow = $lastRow + 1;

        $sheet->getRowDimension($footerRow)->setRowHeight(15);

        $sheet->getStyle('B' . $footerRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c2d9ff'],
            ],
            'font' => [
                'color' => ['rgb' => '000000'],
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('G' . $footerRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c2d9ff'],
            ],
            'font' => [
                'color' => ['rgb' => '000000'],
                'bold' => true,
            ],
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

        $sheet->getStyle('B' . $footerRow . ':' . $lastColumn . $footerRow)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'c2d9ff'],
            ],
            'font' => [
                'color' => ['rgb' => '000000'],
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
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
        
        // Apply styles to the entire table for column C
        // $sheet->getStyle('C5:' . $lastColumn . $lastRow)->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
        //             'color' => ['rgb' => '000000'],
        //         ],
        //     ],
        // ]);
        


        return [];
    }
    
    private function applyStyleToCell($sheet, $cell, $backgroundColor, $fontColor)
    {
        $sheet->getStyle($cell)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $backgroundColor],
            ],
            'font' => [
                'color' => ['rgb' => $fontColor],
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }
    
}

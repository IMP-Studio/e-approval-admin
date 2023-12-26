<?php

// DivisionSheet.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Division;

class DivisionSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithCustomStartCell, WithColumnWidths, WithStyles
{
    private $divisions;
    private $iteration = 0;
    private $currentDivision = null;

    public function __construct($divisions)
    {
        $this->divisions = $divisions;
    }

    public function collection()
    {
        $collection = [];

        $this->currentDivision = null;

        foreach ($this->divisions as $division) {
            $isFirstDivision = true;

            foreach ($division->employee as $employee) {
                $this->iteration++;

                if ($this->currentDivision !== $division->name) {
                    // Add the division name only once
                    if (!$isFirstDivision) {
                        $collection[] = [
                            'division' => $division->name,
                        ];
                    }
                    $this->currentDivision = $division->name;
                    $isFirstDivision = false;
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
                    $division->name, // Remove the initial blank entry
                    $employee->user->name,
                    $employee->user->employee->position->name,
                    $role,
                    $gender
                ];
            }
        }

        return collect($collection);
    }
    
    public function title(): string
    {
        // Use a common title for all divisions
        return 'Employee Data';
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function headings(): array
    {
        $monthNamesMapping = [
            'January' => 'JANUARI',
            'February' => 'FEBRUARI',
            'March' => 'MARET',
            'April' => 'APRIL',
            'May' => 'MEI',
            'June' => 'JUNI',
            'July' => 'JULI',
            'August' => 'AGUSTUS',
            'September' => 'SEPTEMBER',
            'October' => 'OKTOBER',
            'November' => 'NOVEMBER',
            'December' => 'DESEMBER',
        ];

        $formattedDate = strtoupper(now()->format('d F Y'));

        foreach ($monthNamesMapping as $englishMonth => $indonesianMonth) {
            $formattedDate = str_replace(' ' . strtoupper($englishMonth) . ' ', ' ' . $indonesianMonth . ' ', $formattedDate);
        }
        
        return [
            ['REKAP PEGAWAI'],
            [$formattedDate],
            [
                'NO',
                'Nama Divisi',
                'Nama Lengkap',
                'Posisi',
                'Role',
                'L / P',
            ],
        ];
    }

    public function map($row): array
    {
        // if (!empty($row['division'])) {
        //     $row['division'] = [
        //         'value' => $row['division'],
        //         'alignment' => ['textRotation' => 90],
        //     ];
        // }
        // $row[5] = ($row[5] == 'male') ? 'L' : 'P';
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

        $divisionNameColumn = 'C';

        // Initialize row counter
        $rowCounter = 5;

        // Merge cells for each group of division data
        foreach ($this->divisions as $division) {
            $startRow = $rowCounter;
            $endRow = $startRow + count($division->employee) - 1;

            if ($endRow > $startRow) {
                $sheet->mergeCells($divisionNameColumn . $startRow . ':' . $divisionNameColumn . $endRow);
            }

            $rowCounter = $endRow + 1;
        }
        

        // Apply styles to the "Nama Divisi" heading
        $sheet->getStyle($divisionNameColumn . '4')->getAlignment()->setTextRotation(0);

        // Additional styling for the "Nama Divisi" column data
        $sheet->getStyle($divisionNameColumn . '5:' . $divisionNameColumn . $lastRow)->getAlignment()->setTextRotation(-90);

        $sheet->getStyle($divisionNameColumn . '5:' . $divisionNameColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF'],
            ],
        ]);

        // Apply styles to the entire table
        $sheet->getStyle($divisionNameColumn . '5:' . $divisionNameColumn . $lastRow)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Additional styling for the division name cells
        $sheet->getStyle($divisionNameColumn . '5:' . $divisionNameColumn . $lastRow)->applyFromArray([
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
    
        // // Remove horizontal borders for column C
        $sheet->getStyle('C5:C' . $lastRow)->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
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
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Apply styles to the entire table
        $sheet->getStyle('C2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Apply styles to the entire table
        $sheet->getStyle('D2:D'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Apply styles to the entire table
        $sheet->getStyle('E2:E'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Apply styles to the entire table
        $sheet->getStyle('F2:F'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Apply styles to the entire table
        $sheet->getStyle('G2:G'  . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
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

        $sheet->getStyle('C5:C' . $lastRow)->applyFromArray([
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

        for ($i = 5; $i <= $lastRow; $i++) {
            $sheet->getStyle('B' . $i . ':' . $lastColumn . $i)->getFont()->setBold(false);
        }

//       // Apply custom styling for division name cells
// foreach ($sheet->getRowIterator() as $row) {
//     $divisionNameCell = 'C' . $row->getRowIndex();
//     $divisionName = $sheet->getCell($divisionNameCell)->getValue();

//     if ($divisionName !== null && $divisionNameCell !== 'C4') { // Skip styles for headers and empty division name cells
//         // Merge cells and center the text
//         $sheet->mergeCells("C{$row->getRowIndex()}:C{$lastColumn}");
//         $sheet->getStyle($divisionNameCell)->getAlignment()->setHorizontal('center');
//         $sheet->getStyle($divisionNameCell)->getAlignment()->setVertical('center');
//         $sheet->getStyle("C{$row->getRowIndex()}:C{$lastColumn}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c2d9ff');

//         // Break out of the loop after the first division name to avoid unnecessary iterations
//         break;
//     }
// }

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
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

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

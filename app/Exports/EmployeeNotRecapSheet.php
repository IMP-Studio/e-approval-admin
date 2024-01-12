<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class EmployeeNotRecapSheet implements FromArray, WithHeadings, ShouldAutoSize, WithCustomStartCell, WithStyles
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->employees as $employee) {
            $data[] = [
                'fullname' => $employee->user->name,
                'email' => $employee->user->email,
                'firstname' => $employee->first_name,
                'lastname' => $employee->last_name,
                'id_number' => $employee->id_number,
                'division' => $employee->division->name,
                'position' => $employee->position->name,
                'gender' => $employee->gender,
                'birth_of_date' => $employee->birth_date,
                'address' => $employee->address,
                'role' => $this->determineRole($employee),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['DATA PEGAWAI'],
            [
                'Full Name',
                'Email',
                'First Name',
                'Last Name',
                'ID Number',
                'Division',
                'Position',
                'Gender',
                'Birth Date',
                'Address',
                'Role'
            ]
        ];
    }

    public function startCell(): string
    {
        return 'B2';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('B2:' . $sheet->getHighestColumn() . '2');

        $sheet->getStyle('B2:' . $sheet->getHighestColumn() . '2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Apply styles for the data rows (B3 to L3)
        $sheet->getStyle('B2:' . $sheet->getHighestColumn() . '3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $lastRow = count($this->employees) + 3;
        $sheet->getStyle('B4:' . $sheet->getHighestColumn() . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B' . ($lastRow + 1) . ':' . $sheet->getHighestColumn() . ($lastRow + 1))->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'c2d9ff'],
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('B' . ($lastRow + 1))->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ],
            ],
        ]);

        $sheet->getStyle('L' . ($lastRow + 1))->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ],
            ],
        ]);
    }


    private function determineRole($employee)
    {
        $permissions = $employee->user->permissions->pluck('id')->toArray();

        if (in_array(39, $permissions) && in_array(43, $permissions)) {
            return 'Human Resource';
        } elseif (in_array(38, $permissions) && in_array(42, $permissions)) {
            return 'Head Of Tribe';
        } else {
            return 'Ordinary Employee';
        }
    }
}

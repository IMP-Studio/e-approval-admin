<?php

namespace App\Exports;

use App\Models\employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\setSize;
use PhpOffice\PhpSpreadsheet\Style\Padding;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// class EmployeeExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping, WithCustomStartCell, WithDrawings, WithEvents, WithStyles
class EmployeeExport implements FromView
{
    private $iteration = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return employee::all();
    // }
    // public function startCell(): string
    // {
    //     return 'B2';
    // }
    // public function styles(Worksheet $sheet)
    // {
    //     return [
    //         2 => [
    //             'font' => ['size' => 12, 'bold' => true],
    //         ],
    //     ];
    // }
    // public function map($employee): array
    // {
    //     $this->iteration++;
    //     return [
    //         $this->iteration,
    //         $employee->user->name,
    //         $employee->img_profile,
    //         $employee->firstname,
    //         $employee->lastname,
    //         $employee->gender,
    //         $employee->staff_id,
    //         $employee->division->division,
    //         $employee->address
    //     ];
    // }
    // public function headings(): array
    // {
    //     return [
    //         'No',
    //         'Username',
    //         'Profile',
    //         'Firstname',
    //         'Lastname',
    //         'Gender',
    //         'Staff ID',
    //         'Divison',
    //         'Address',
    //     ];
    // }
    // public function columnWidths(): array
    // {
    //     return [
    //         'B' =>3,
    //         'C' =>20,
    //         'D' =>20,
    //         'E' =>15,
    //         'F' =>15,
    //         'G' =>10,
    //         'H' =>15,
    //         'I' =>20,
    //         'J' =>60,
    //     ];
    // }

    // public function drawings()
    // {
    //     $drawings = [];
    //     $employees = employee::all();

    //     foreach ($employees as $index => $employee) {
    //         $drawing = new Drawing();
    //         $drawing->setName('Profile' . $employee->id);
    //         $drawing->setPath(public_path('images/' . $employee->img_profile));
    //         $drawing->setHeight(50);
    //         $drawing->setCoordinates('D' . ($index + 3));
    //         $drawings[] = $drawing;
    //     }

    //     return $drawings;
    // }

    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function (AfterSheet $event) {
    //             $event->sheet->getDefaultRowDimension()->setRowHeight(60);
    //         },
    //     ];
    // }
    
    public function view(): View
    {
        return view('employee.export-excel',['employee' => employee::all()]);
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Employee;

class EmployeeNotRecapExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $employees = Employee::with('user')->get();

        $employeeSheet = new EmployeeNotRecapSheet($employees); 

        return [$employeeSheet];
    }
}

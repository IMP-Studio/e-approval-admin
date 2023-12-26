<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Employee;

class EmployeeExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $employees = Employee::with('user.standups')->get();

        $employeeSheet = new EmployeeSheet($employees);

        return [$employeeSheet];
    }
}

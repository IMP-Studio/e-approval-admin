<?php
// DivisionExport.php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Division;

class DivisionExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        // Fetch all divisions
        $divisions = Division::all();

        // Pass all divisions to a single instance of DivisionSheet
        $sheet = new DivisionSheet($divisions);

        return [$sheet];
    }
}

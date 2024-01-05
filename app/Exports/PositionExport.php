<?php

namespace App\Exports;

use App\Models\Position;
use App\Exports\PositionSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PositionExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $positions = Position::all();

        $sheet = new PositionSheet($positions);

        return [$sheet];
    }
}

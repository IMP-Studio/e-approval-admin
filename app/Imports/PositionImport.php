<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PositionImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $division = Division::updateOrCreate(
            ['name' => $row[2]]
        );
        $division->touch();

        $position = Position::updateOrCreate(
            ['division_id' => $division->id],
            ['name' => $row[3]]
        );
        $position->touch();

        return $position;
    }

}

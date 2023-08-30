<?php

namespace App\Imports;

use App\Models\Division;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DivisionImport implements ToModel, WithHeadingRow
{
    private $skipRows = 1;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($this->skipRows > 0) {
            $this->skipRows--;
            return null;
        }
        return new Division([
            'division' => $row[1]
        ]);
    }
}

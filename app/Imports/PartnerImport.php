<?php

namespace App\Imports;

use App\Models\Partner;
use FontLib\Table\Type\name;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PartnerImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
    * @param array<int, mixed> $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Partner([
            'name' => $row[2],
            'description' => $row[3]
        ]);
    }
}

<?php

namespace App\Imports;

use App\Models\Partner;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProjectImport implements ToModel, WithStartRow
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
        $partner = Partner::where('name', $row[2])->first();

        if (!$partner) {
            $partner = Partner::create([
                'name' => $row[2]
            ]);
        }

        $startExcelDate = intval($row[4]);
        $endExcelDate = intval($row[5]);

        $startUnixTimestamp = ($startExcelDate - 25569) * 86400;
        $endUnixTimestamp = ($endExcelDate - 25569) * 86400;

        $formattedStartDate = Carbon::createFromTimestamp($startUnixTimestamp)->format('Y-m-d');
        $formattedEndDate = Carbon::createFromTimestamp($endUnixTimestamp)->format('Y-m-d');

        return new Project([
            'partner_id' => $partner->id,
            'name' => $row[3],
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate
        ]);

    }
}

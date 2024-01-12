<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Partner;

class PartnerExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $partner = Partner::all();

        $partnerSheet = new PartnerSheet($partner);
        
        return [$partnerSheet];
    }
}
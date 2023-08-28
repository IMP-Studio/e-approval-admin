<?php

namespace App\Exports;

use App\Models\division;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class DivisionExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return division::all();
    }

    public function view(): View
    {
        return view('divisi.export-excel',['division' => division::all()]);
    }
}

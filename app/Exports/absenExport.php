<?php

namespace App\Exports;

use App\Models\absen;
use Maatwebsite\Excel\Concerns\FromCollection;

class absenExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return absen::all();
    }
}

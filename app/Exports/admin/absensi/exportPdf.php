<?php

namespace App\Exports\admin\absensi;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;

class exportPdf implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Absensi::all();
    }
}

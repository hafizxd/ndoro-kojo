<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LivestockReportMutationDeadExport implements WithMultipleSheets
{
    use Exportable;

    protected $request;

    function __construct($request) {
        $this->request = $request; 
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new LivestockReportMutationExport($this->request);
        $sheets[] = new LivestockReportDeadExport($this->request);

        return $sheets;
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Livestock;

class LivestockReportExport implements FromView
{
    protected $urlType;
    protected $livestockTypeId;

    function __construct($urlType, $livestockTypeId) {
        $this->urlType = $urlType;
        $this->livestockTypeId = $livestockTypeId;
    }

    public function view(): View
    {
        $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah'])
                ->whereHas('kandang', function ($query) {
                    $query->where('type_id', $this->livestockTypeId);
                });

        if ($this->urlType == 'mati') {
            $q->whereNotNull('dead_year');
        } else {
            if ($this->urlType == 'transaksi-jual-beli') {
                $q->has('livestockBuy');
            } else {
                $q->whereNull('dead_year');
                if ($this->urlType == 'sedang-dijual') {
                    $q->whereNull('dead_year')
                        ->whereNull('sold_deal_price')
                        ->whereNotNull('sold_proposed_price');   
                } else if ($this->urlType == 'lahir') {
                    $q->where('acquired_status', 'LAHIR');
                }
            }
        }

        $livestocks = $q->get();

        return view('exports.livestock-report', compact('livestocks'));
    }
}

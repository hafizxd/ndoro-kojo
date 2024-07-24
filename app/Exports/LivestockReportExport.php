<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Livestock;
use Carbon\Carbon;

class LivestockReportExport implements FromView
{
    protected $urlType;
    protected $livestockTypeId;
    protected $request;

    function __construct($urlType, $livestockTypeId, $request) {
        $this->urlType = $urlType;
        $this->livestockTypeId = $livestockTypeId;
        $this->request = $request; 
    }

    public function view(): View
    {
        $dateStart = null;
        $dateEnd = null;
        if (isset($this->request->daterange)) {
            $dateExp = explode(' to ', $this->request->daterange);
            $dateStart = $dateExp[0];
            $dateEnd = $dateExp[1] ?? Carbon::now()->isoFormat('DD/MM/YYYY');;
        }

        $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah']);
        
        if ($this->livestockTypeId != 'all') {
            $q->whereHas('kandang', function ($query) {
                $query->where('type_id', $this->livestockTypeId);
            });
        }

        if ($this->urlType == 'mati') {
            $q->whereNotNull('dead_year')
                ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                    $query->where('dead_month', '>=', explode('/', $dateStart)[1])
                        ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                        ->where('dead_year', '>=', explode('/', $dateStart)[2])
                        ->where('dead_year', '<=', explode('/', $dateEnd)[2]);
                });
        } else {
            if ($this->urlType == 'transaksi-jual-beli') {
                $q->has('livestockBuy')
                    ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                        $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                            ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                            ->where('sold_year', '>=', explode('/', $dateStart)[2])
                            ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                    });
            } else {
                $q->whereNull('dead_year');

                if ($this->urlType == 'total-ternak') {
                    $q->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                        $query->where(function ($query) use ($dateStart, $dateEnd) {
                                $query->whereNull('sold_deal_price')
                                    ->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                    ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                    ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                            })
                            ->orWhere(function ($query) use ($dateStart, $dateEnd) {
                                $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                                    ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('sold_year', '>=', explode('/', $dateStart)[2])
                                    ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                            });
                    });
                } 
                else {
                    $q->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                        $query->where(function ($query) use ($dateStart, $dateEnd) {
                            $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                        });
                    });

                    if ($this->urlType == 'sedang-dijual') {
                        $q->whereNull('sold_deal_price')
                            ->whereNotNull('sold_proposed_price');   
                    } else if ($this->urlType == 'lahir') {
                        $q->where('acquired_status', 'LAHIR');
                    }
                }
            }
        }

        $livestocks = $q->get();

        return view('exports.livestock-report', compact('livestocks'));
    }
}

<?php

namespace App\Exports;

use App\Models\LivestockType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Livestock;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;

class LivestockReportDeadExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $request;

    function __construct($request) {
        $this->request = $request; 
    }

    public function title(): string
    {
        return 'PENYEBAB KEMATIAN';
    }

    public function view(): View
    {
        $request = $this->request;

        $dateStart = null;
        $dateEnd = Carbon::now()->isoFormat('DD/MM/YYYY');

        if (isset($request->year)) {
            if (isset($request->from_month)) {
                $dateStart = Carbon::createFromFormat('d-m-Y', '01-'.$request->from_month.'-'.$request->year);
            } else {
                $dateStart = Carbon::createFromFormat('d-m-Y', '01-01-'.$request->year);
            }

            if (isset($request->to_month)) {
                $dateEnd = Carbon::createFromFormat('d-m-Y', '01-'.$request->to_month.'-'.$request->year)->endOfMonth();
            } else {
                $dateEnd = Carbon::now();
            }

            $dateStart = $dateStart->isoFormat('DD/MM/YYYY');
            $dateEnd = $dateEnd->isoFormat('DD/MM/YYYY');
        }

        $livestocks = LivestockType::doesntHave('livestockChildren')
            ->when(!empty($request->province_id) || !empty($request->regency_id) || !empty($request->district_id) || !empty($request->village_id), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->doesntHave('kandang')->orWhereHas('kandang', function ($query) use ($request) {
                        $query->when(!empty($request->province_id), function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        })->when(!empty($request->regency_id), function ($query) use ($request) {
                            $query->where('regency_id', $request->regency_id);
                        })->when(!empty($request->district_id), function ($query) use ($request) {
                            $query->where('district_id', $request->district_id);
                        })->when(!empty($request->village_id), function ($query) use ($request) {
                            $query->where('village_id', $request->village_id);
                        });
                    });
                });
            })
            ->with([
                'livestocks' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('dead_year')
                        ->when(isset($dateStart), function ($query) use ($dateStart) {
                            $query->where(function ($query) use ($dateStart) {
                                $query->where('dead_month', '>=', explode('/', $dateStart)[1])
                                    ->where('dead_year', explode('/', $dateStart)[2]);
                            })->orWhere('dead_year', '>', explode('/', $dateStart)[2]);
                        })
                        ->where(function ($query) use ($dateEnd) {
                            $query->where(function ($query) use ($dateEnd) {
                                $query->where('dead_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('dead_year', explode('/', $dateEnd)[2]);
                            })
                            ->orWhere('dead_year', '<', explode('/', $dateEnd)[2]);
                        });
                }
            ])
            ->with('livestockParent')
            ->get();

        $livestocks =  $livestocks->sort(function ($a, $b) {
            $aType = (isset($a->livestockParent) ? $a->livestockParent->livestock_type . ' - ' : '') . $a->livestock_type;
            $bType = (isset($b->livestockParent) ? $b->livestockParent->livestock_type . ' - ' : '') . $b->livestock_type;

            return strcmp($aType, $bType);
        });

        return view('exports.livestock-dead-report', compact('livestocks'));
    }
}

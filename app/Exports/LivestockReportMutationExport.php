<?php

namespace App\Exports;

use App\Models\LivestockType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Livestock;
use Carbon\Carbon;

class LivestockReportMutationExport implements FromView, ShouldAutoSize
{
    protected $request;

    function __construct($request) {
        $this->request = $request; 
    }

    public function view(): View
    {
        $request = $this->request;

        $dateStart = '//';
        $dateEnd = Carbon::now()->isoFormat('DD/MM/YYYY');
        // if (isset($request->daterange)) {
        //     $dateExp = explode(' to ', $request->daterange);
        //     $dateStart = $dateExp[0];
        //     $dateEnd = $dateExp[1];
        // }
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

        $livestockStart = LivestockType::doesntHave('livestockChildren')
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
            ->withCount([
                'livestocks AS total_ternak' => function ($query) use ($dateStart) {
                    $query->has('kandang')
                        ->whereNull('dead_year')
                        ->whereNull('sold_deal_price')
                        ->when(isset($dateStart), function ($query) use ($dateStart) {
                            $query->where(function ($query) use ($dateStart) {
                                $query->where('acquired_month', '<', explode('/', $dateStart)[1])
                                    ->where('acquired_year', explode('/', $dateStart)[2]);
                            })->orWhere('acquired_year', '<', explode('/', $dateStart)[2]);
                        });
                }
            ])
            ->with('livestockParent')
            ->get();

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
            ->withCount([
                'livestocks AS jual_jantan' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('sold_deal_price')
                        ->where('sold_month', '>=', explode('/', $dateStart)[1])
                        ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                        ->where('sold_year', '>=', explode('/', $dateStart)[2])
                        ->where('sold_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'JANTAN');
                }
            ])
            ->withCount([
                'livestocks AS jual_betina' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('sold_deal_price')
                        ->where('sold_month', '>=', explode('/', $dateStart)[1])
                        ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                        ->where('sold_year', '>=', explode('/', $dateStart)[2])
                        ->where('sold_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'BETINA');
                }
            ])
            ->withCount([
                'livestocks AS beli_jantan' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->where('acquired_status', 'BELI')
                        ->where('acquired_month', '>=', explode('/', $dateStart)[1])
                        ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                        ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                        ->where('acquired_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'JANTAN');
                }
            ])
            ->withCount([
                'livestocks AS beli_betina' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->where('acquired_status', 'BELI')
                        ->where('acquired_month', '>=', explode('/', $dateStart)[1])
                        ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                        ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                        ->where('acquired_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'BETINA');
                }
            ])
            ->withCount([
                'livestocks AS lahir_jantan' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->where('acquired_status', 'LAHIR')
                        ->where('acquired_month', '>=', explode('/', $dateStart)[1])
                        ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                        ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                        ->where('acquired_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'JANTAN');
                }
            ])
            ->withCount([
                'livestocks AS lahir_betina' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->where('acquired_status', 'LAHIR')
                        ->where('acquired_month', '>=', explode('/', $dateStart)[1])
                        ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                        ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                        ->where('acquired_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'BETINA');
                }
            ])
            ->withCount([
                'livestocks AS mati_jantan' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('dead_year')
                        ->where('dead_month', '>=', explode('/', $dateStart)[1])
                        ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                        ->where('dead_year', '>=', explode('/', $dateStart)[2])
                        ->where('dead_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'JANTAN');
                }
            ])
            ->withCount([
                'livestocks AS mati_betina' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('dead_year')
                        ->where('dead_month', '>=', explode('/', $dateStart)[1])
                        ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                        ->where('dead_year', '>=', explode('/', $dateStart)[2])
                        ->where('dead_year', '<=', explode('/', $dateEnd)[2])
                        ->where('gender', 'BETINA');
                }
            ])
            ->orderBy('livestock_type')
            ->get();

        $livestockStart =  $livestockStart->sort(function ($a, $b) {
            $aType = (isset($a->livestockParent) ? $a->livestockParent->livestock_type . ' - ' : '') . $a->livestock_type;
            $bType = (isset($b->livestockParent) ? $b->livestockParent->livestock_type . ' - ' : '') . $b->livestock_type;

            return strcmp($aType, $bType);
        });

        return view('exports.livestock-mutation-report', compact('livestockStart', 'livestocks'));
    }
}

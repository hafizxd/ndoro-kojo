<?php

namespace App\Http\Controllers;

use App\Models\LivestockType;
use Illuminate\Http\Request;
use App\Models\Livestock;
use DataTables;

class LivestockController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Livestock::select('*')
                ->has('kandang.livestockType')
                ->with('kandang', function ($query) {
                    $query->with(['farmer', 'livestockType', 'district', 'village']);
                })
                ->with('livestockType');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('farmer', function($row) {
                    return $row->kandang?->farmer?->fullname;
                })
                ->addColumn('kandang', function($row) {
                    return $row->kandang?->name;
                })
                ->addColumn('ras', function($row) {
                    return $row->kandang?->livestockType?->livestock_type . ' ' . $row->livestockType?->livestock_type;
                })
                ->addColumn('district', function($row) {
                    return $row->kandang?->district?->name;
                })
                ->addColumn('village', function($row) {
                    return $row->kandang?->village?->name;
                })
                ->addColumn('action', function($row) {
                    return '<a href="javascript:editData(rowData_'.md5($row->id).')" class="edit btn btn-success btn-sm">Simpan</a>';
                })
                ->rawColumns(['district', 'village', 'action'])
                ->make(true);
        }

        return view('livestocks.index');
    }

    public function report(Request $request) {
        $livestockTypes = LivestockType::where('level', 1)
            ->withCount(['livestocksThroughKandang AS total_ternak' => function($query) {
                $query->has('kandang')
                    ->whereNull('dead_year');
            }])
            ->withCount(['livestocksThroughKandang AS transaksi_jual_beli' => function($query) {
                $query->has('kandang')
                    ->where(function($query) {
                        $query->whereNotNull('sold_deal_price')
                            ->orWhere('acquired_status', 'BELI');
                    });
            }])
            ->withCount(['livestocksThroughKandang AS sedang_dijual' => function($query) {
                $query->has('kandang')
                    ->whereNull('dead_year')
                    ->whereNull('sold_deal_price')
                    ->whereNotNull('sold_proposed_price');
            }])
            ->withCount(['livestocksThroughKandang AS lahir' => function($query) {
                $query->has('kandang')
                    ->where('acquired_status', 'LAHIR');
            }])
            ->withCount(['livestocksThroughKandang AS mati' => function($query) {
                $query->has('kandang')
                    ->whereNotNull('dead_year');
            }])
            ->orderBy('livestock_type')
            ->get();    

        return view('livestocks.reports.livestock-type', compact('livestockTypes'));
    }

    public function reportDetail($urlType, $livestockTypeId, Request $request) {
        $urlArr = ['total-ternak', 'transaksi-jual-beli', 'sedang-dijual', 'lahir', 'mati'];
        if (!in_array($urlType, $urlArr)) 
            abort(404);

        $livestockType = LivestockType::where('level', 1)->findOrFail($livestockTypeId);

        if ($urlArr == 'transaksi-jual-beli') {
            return $this->reportDetailTransaksi($urlArr, $livestockType, $request);
        }

        if ($request->ajax()) {
            $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah'])
                ->whereHas('kandang', function ($query) use ($livestockType) {
                    $query->where('type_id', $livestockType->id);
                });

            if ($urlArr == 'mati') {
                $q->whereNotNull('dead_year');
            } else {
                $q->whereNull('dead_year');

                if ($urlArr == 'sedang-dijual') {
                    $q->whereNull('dead_year')
                        ->whereNull('sold_deal_price')
                        ->whereNotNull('sold_proposed_price');   
                } else if ($urlArr == 'lahir') {
                    $q->where('acquired_status', 'LAHIR');
                }
            }

            return DataTables::of($q)
                    ->addIndexColumn()
                    ->addColumn('farmer', function($row) {
                        return $row->kandang?->farmer?->fullname;
                    })
                    ->addColumn('kandang', function($row) {
                        return $row->kandang?->name;
                    })
                    ->addColumn('pakan', function($row) {
                        return $row->pakan?->jenis_pakan;
                    })
                    ->addColumn('limbah', function($row) {
                        return $row->limbah?->pengolahan_limbah;
                    })
                    ->addColumn('status', function($row) {
                        $res = isset($row->dead_year) ? 'MATI' : $row->acquired_status;
                        return $res;
                    })
                    ->addColumn('month', function($row) {
                        $res = isset($row->dead_year) ? $row->dead_month_name : $row->acquired_month_name;
                        return $res;
                    })
                    ->addColumn('year', function($row) {
                        $res = isset($row->dead_year) ? $row->dead_year : $row->acquired_year;
                        return $res;
                    })
                    ->addColumn('province', function($row) {
                        return $row->kandang?->province?->name;
                    })
                    ->addColumn('regency', function($row) {
                        return $row->kandang?->regency?->name;
                    })
                    ->addColumn('district', function($row) {
                        return $row->kandang?->district?->name;
                    })
                    ->addColumn('village', function($row) {
                        return $row->kandang?->village?->name;
                    })
                    ->rawColumns(['farmer', 'kandang', 'pakan', 'limbah', 'status', 'month', 'year', 'province', 'regency', 'village'])
                    ->make(true);
        }

        return view('livestocks.reports.livestock', compact('urlType', 'livestockType'));   
    }

    function reportDetailTransaksi($urlType, $livestockType, $request) {
        if ($request->ajax()) {
            $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah'])
                ->with(['livestockBuy' => function ($query) {
                    $query->with(['seller', 'buyer']);
                }])
                ->whereHas('kandang', function ($query) use ($livestockType) {
                    $query->where('type_id', $livestockType->id);
                })
                ->whereNotNull('dead_year');

            return DataTables::of($q)
                    ->addIndexColumn()
                    ->addColumn('seller', function($row) {
                        return $row->kandang?->farmer?->fullname;
                    })
                    ->addColumn('buyer', function($row) {
                        return $row->kandang?->farmer?->fullname;
                    })
                    ->addColumn('kandang', function($row) {
                        return $row->kandang?->name;
                    })
                    ->addColumn('pakan', function($row) {
                        return $row->pakan?->jenis_pakan;
                    })
                    ->addColumn('limbah', function($row) {
                        return $row->limbah?->pengolahan_limbah;
                    })
                    ->addColumn('status', function($row) {
                        return $row->limbah?->pengolahan_limbah;
                    })
                    ->addColumn('district', function($row) {
                        return $row->kandang?->district?->name;
                    })
                    ->addColumn('village', function($row) {
                        return $row->kandang?->village?->name;
                    })
                    // ->rawColumns(['far' 'district', 'village'])
                    ->make(true);
        }

        return view('livestocks.reports.livestock-transaction', compact('urlType', 'livestockType'));   
    }
}

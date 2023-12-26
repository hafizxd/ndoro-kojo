<?php

namespace App\Http\Controllers;

use Excel;
use App\Exports\LivestockReportExport;
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
                    $query->with(['farmer', 'livestockType', 'province', 'district', 'district', 'village']);
                })
                ->with(['pakan', 'limbah', 'livestockType']);

            return DataTables::of($data)
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
                    if (isset($row->dead_year)) {
                        $res = 'MATI';
                    } else if (isset($row->sold_deal_price)) {
                        $res = 'JUAL';
                    } else {
                        $res = $row->acquired_status;
                    }

                    $statusArr = ['LAHIR', 'MATI', 'JUAL', 'BELI'];
                    $select = "<select class='form-control' id='status".$row->id."'>";

                    foreach ($statusArr as $value) {
                        $select .= "<option value='".$value."' ".($value == $res ? "selected" : "").">".$value."</option>";
                    }
                    $select .= "</select>";

                    return $select;
                })
                ->addColumn('month', function($row) {
                    $res = isset($row->dead_year) ? $row->dead_month : $row->acquired_month;
                    if (isset($res))
                        $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                    else
                        $res = '';
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
                ->addColumn('action', function($row) {
                    return '<a href="javascript:saveData('.$row->id.')" class="edit btn btn-success btn-sm">Simpan</a>';
                })
                ->rawColumns(['farmer', 'kandang', 'pakan', 'limbah', 'status', 'month', 'year', 'province', 'regency', 'village', 'action'])
                ->make(true);
        }

        return view('livestocks.index');
    }

    public function updateStatus(Request $request) {
        $livestock = Livestock::findOrFail($request->id);

        $year = date('Y');
        $month = date('m');
        $monthName = \Carbon\Carbon::createFromFormat('m', $month)->locale('id')->isoFormat('MMMM');

        if ($request->status == 'MATI') {
            $livestock->update([
                'dead_year' => $year,
                'dead_month' => $month,
                'dead_month_name' => $monthName
            ]);
        } else if ($request->status == 'JUAL') {
            $livestock->update([
                'dead_year' => null,
                'dead_month' => 0,
                'dead_month_name' => null,
                'sold_proposed_price' => 1,
                'sold_deal_price' => 1,
                'sold_year' => $year,
                'sold_month' => $month,
                'sold_month_name' => $monthName
            ]);
        } else {
            $livestock->update([
                'dead_year' => null,
                'dead_month' => 0,
                'dead_month_name' => null,
                'acquired_status' => $request->status,
                'acquired_year' => $year,
                'acquired_month' => $month,
                'acquired_month_name' => $monthName
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil simpan data',
            'payload' => []
        ]);
    }

    public function report(Request $request) {
        $livestockTypes = LivestockType::where('level', 1)
            ->withCount(['livestocksThroughKandang AS total_ternak' => function($query) {
                $query->has('kandang')
                    ->whereNull('dead_year');
            }])
            ->withCount(['livestocksThroughKandang AS transaksi_jual_beli' => function($query) {
                $query->has('kandang')
                    ->has('livestockBuy');
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

        $livestockType = null;
        if ($livestockTypeId != 'all')
            $livestockType = LivestockType::where('level', 1)->findOrFail($livestockTypeId);

        $dateStart = null;
        $dateEnd = null;
        if (isset($request->daterange)) {
            $dateExp = explode(' to ', $request->daterange);
            $dateStart = $dateExp[0];
            $dateEnd = $dateExp[1];
        }

        if ($request->ajax()) {
            $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah']);
        
            if (isset($livestockType)) {
                $q->whereHas('kandang', function ($query) use ($livestockType) {
                    $query->where('type_id', $livestockType->id);
                });
            }

            if ($urlType == 'mati') {
                $q->whereNotNull('dead_year')
                    ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                        $query->where('dead_month', '>=', explode('/', $dateStart)[1])
                            ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                            ->where('dead_year', '>=', explode('/', $dateStart)[2])
                            ->where('dead_year', '<=', explode('/', $dateEnd)[2]);
                    });
            } else {
                if ($urlType == 'transaksi-jual-beli') {
                    $q->has('livestockBuy')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                                ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                                ->where('sold_year', '>=', explode('/', $dateStart)[2])
                                ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                        });
                } else {
                    $q->whereNull('dead_year')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                        });

                    if ($urlType == 'sedang-dijual') {
                        $q->whereNull('sold_deal_price')
                            ->whereNotNull('sold_proposed_price');   
                    } else if ($urlType == 'lahir') {
                        $q->where('acquired_status', 'LAHIR');
                    }
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
                        $res = isset($row->dead_year) ? $row->dead_month : $row->acquired_month;
                        if (isset($res))
                            $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                        else
                            $res = '';
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

        return view('livestocks.reports.livestock', compact('urlType', 'livestockType', 'dateStart', 'dateEnd'));   
    }

    public function reportDetailExport($urlType, $livestockTypeId) {
        return Excel::download(new LivestockReportExport($urlType, $livestockTypeId), 'livestock_report_'.time().'.xlsx');
    }

    // function reportDetailTransaksi($urlType, $livestockType, $request) {
    //     if ($request->ajax()) {
    //         $q = Livestock::with(['kandang.farmer', 'pakan', 'limbah'])
    //             ->with(['livestockBuy' => function ($query) {
    //                 $query->with(['seller', 'buyer']);
    //             }])
    //             ->whereHas('kandang', function ($query) use ($livestockType) {
    //                 $query->where('type_id', $livestockType->id);
    //             })
    //             ->whereNotNull('dead_year');

    //         return DataTables::of($q)
    //                 ->addIndexColumn()
    //                 ->addColumn('seller', function($row) {
    //                     return $row->kandang?->farmer?->fullname;
    //                 })
    //                 ->addColumn('buyer', function($row) {
    //                     return $row->kandang?->farmer?->fullname;
    //                 })
    //                 ->addColumn('kandang', function($row) {
    //                     return $row->kandang?->name;
    //                 })
    //                 ->addColumn('pakan', function($row) {
    //                     return $row->pakan?->jenis_pakan;
    //                 })
    //                 ->addColumn('limbah', function($row) {
    //                     return $row->limbah?->pengolahan_limbah;
    //                 })
    //                 ->addColumn('status', function($row) {
    //                     return $row->limbah?->pengolahan_limbah;
    //                 })
    //                 ->addColumn('district', function($row) {
    //                     return $row->kandang?->district?->name;
    //                 })
    //                 ->addColumn('village', function($row) {
    //                     return $row->kandang?->village?->name;
    //                 })
    //                 // ->rawColumns(['far' 'district', 'village'])
    //                 ->make(true);
    //     }

    //     return view('livestocks.reports.livestock-transaction', compact('urlType', 'livestockType'));   
    // }
}

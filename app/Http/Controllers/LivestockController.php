<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use App\Exports\LivestockReportMutationDeadExport;
use App\Models\District;
use App\Models\Farmer;
use App\Models\Kandang;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Validator;
use App\Exports\LivestockReportExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\LivestockType;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\Limbah;
use DataTables;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        $limbah = Limbah::all();
        $farmers = Farmer::select('id', 'fullname')->withWhereHas('kandangs', function ($query) {
                if (auth('web')->check() && auth('web')->user()->role == UserRole::OPERATOR){
                    $query->where('district_id', auth('web')->user()->district_id);
                }
                $query->orderBy('name');
            })
            ->orderBy('fullname')
            ->get();
        $livestockTypes = LivestockType::doesntHave('livestockChildren')->orWhere('level', 2)->get();
        $livestockTypesParents = LivestockType::where('level', 1)->get();
        $provinces = Province::all();

        if ($request->ajax()) {
            $data = Livestock::select('*');
            
            if (auth('web')->user() && auth('web')->user()->role == UserRole::OPERATOR && isset(auth('web')->user()->district)) {
                $data->withWhereHas('kandang', function ($query) {
                    $query->where('district_id', auth('web')->user()->district_id)
                        ->with(['livestockType', 'farmer', 'livestockType', 'province', 'district', 'district', 'village']);
                });
            } else {
                $data->has('kandang.livestockType')
                    ->with('kandang', function ($query) {
                        $query->with(['farmer', 'livestockType', 'province', 'district', 'district', 'village']);
                    });
            }
                
            $data = $data->with(['limbah', 'livestockType']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('farmer', function ($row) {
                    return $row->kandang?->farmer?->fullname;
                })
                ->addColumn('kandang', function ($row) {
                    return $row->kandang?->name;
                })
                ->addColumn('limbah', function ($row) {
                    return $row->limbah?->pengolahan_limbah;
                })
                ->addColumn('status', function ($row) {
                    if (isset($row->dead_year)) {
                        $res = 'MATI - ';

                        if (isset($row->dead_reason_2)) {
                            $res .= $row->dead_reason_2;
                        } else {
                            $res .= 'PENYAKIT';
                        }
                    } else if (isset($row->sold_deal_price)) {
                        $res = 'JUAL';
                    } else {
                        $res = $row->acquired_status;
                    }

                    $statusArr = ['LAHIR', 'MATI - PENYAKIT', 'MATI - DIPOTONG', 'MATI - BENCANA', 'JUAL', 'BELI'];

                    $select = $res;

                    if (Auth::guard('web')->check()) {
                        $select = "<select class='form-control' id='status" . $row->id . "'>";

                        foreach ($statusArr as $value) {
                            $select .= "<option value='" . $value . "' " . ($value == $res ? "selected" : "") . ">" . $value . "</option>";
                        }
                        $select .= "</select>";
                    }

                    return $select;
                })
                ->addColumn('month', function ($row) {
                    $res = isset($row->dead_year) ? $row->dead_month : $row->acquired_month;
                    if (isset($res))
                        $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                    else
                        $res = '';
                    return $res;
                })
                ->addColumn('year', function ($row) {
                    $res = isset($row->dead_year) ? $row->dead_year : $row->acquired_year;
                    return $res;
                })
                ->addColumn('province', function ($row) {
                    return $row->kandang?->province?->name;
                })
                ->addColumn('regency', function ($row) {
                    return $row->kandang?->regency?->name;
                })
                ->addColumn('district', function ($row) {
                    return $row->kandang?->district?->name;
                })
                ->addColumn('village', function ($row) {
                    return $row->kandang?->village?->name;
                })
                ->addColumn('action', function ($row) {
                    $action = '-';

                    if (Auth::guard('web')->check()) {
                        $month = isset($row->dead_year) ? $row->dead_month : $row->acquired_month;
                        $year = isset($row->dead_year) ? $row->dead_year : $row->acquired_year;

                        $action = '
                            <script type="text/javascript">
                                var rowData_' . md5($row->id) . ' = {
                                    "id" : "' . $row->id . '",
                                    "code" : "' . $row->code . '",
                                    "livestock_type_kandang" : "' . $row->kandang?->livestockType?->livestock_type . '",
                                    "pakan" : "' . $row->pakan . '",
                                    "limbah_id" : "' . $row->limbah_id . '",
                                    "age" : "' . $row->age . '",
                                    "gender" : "' . $row->gender . '",
                                    "month" : "' . $month . '",
                                    "year" : "' . $year . '"
                                };
                            </script>
                        ';

                        if (auth('web')->user()->role == UserRole::OPERATOR) {
                            if ($row->kandang?->district_id == auth('web')->user()->district_id) {
                                $action .= '
                                    <a href="javascript:saveData(' . $row->id . ')" class="edit btn btn-primary btn-sm">Simpan</a>
                                    <a href="javascript:editData(rowData_' . md5($row->id) . ')" class="edit btn btn-success btn-sm">Edit</a>
                                ';    
                            } else {
                                $action .= '-';
                            }
                        } else {
                            $action .= '
                                <a href="javascript:saveData(' . $row->id . ')" class="edit btn btn-primary btn-sm">Simpan</a>
                                <a href="javascript:editData(rowData_' . md5($row->id) . ')" class="edit btn btn-success btn-sm">Edit</a> 
                                <a href="javascript:deleteData(' . $row->id . ')" class="delete btn btn-danger btn-sm">Delete</a>
                            ';
                        }
                    }

                    return $action;
                })
                ->rawColumns(['farmer', 'kandang', 'limbah', 'status', 'month', 'year', 'province', 'regency', 'village', 'action'])
                ->make(true);
        }

        return view('livestocks.index', compact('limbah', 'farmers', 'livestockTypes', 'provinces', 'livestockTypesParents'));
    }

    public function store(Request $request) {
        if (empty($request->with_kandang) || ($request->with_kandang != '0' && $request->with_kandang != '1')) {
            dd('with_kandang empty');
        }

        $rules = [
            'pakan' => 'nullable',
            'limbah_id' => 'required|exists:limbah,id',
            'age' => 'required|in:ANAK,MUDA,DEWASA,BIBIT',
            'type_id' => 'required|exists:livestock_types,id',
            'nominal' => 'required',
            'gender' => 'nullable',
            'acquired_month' => 'required',
            'acquired_year' => 'required|numeric'
        ];

        if ($request->with_kandang == '0') {
            $rules['kandang_id'] = 'required|exists:kandang,id';
        } 
        else if ($request->with_kandang == '1') {
            $rules = array_merge($rules, [
                'farmer_id' => 'required|exists:farmers,id',
                'name' => 'required',
                'panjang' => 'required',
                'lebar' => 'required',
                'jenis' => 'required',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'payload' => [
                    'errors' => $validator->errors()
                ]
            ]);
        }

        $kandangId = null;

        if ($request->with_kandang == '1') {
            $kandang = Kandang::create([
                'farmer_id' => $request->farmer_id,
                'type_id' => $request->kandang_type_id,
                'name' => $request->name,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'luas' => $request->luas,
                'jenis' => $request->jenis,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'address' => $request->address,
                'rt_rw' => $request->rt_rw,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]);

            $kandangId = $kandang->id;
        } 
        else {
            // validate kandang
            $isKandangExists = Kandang::has('farmer')->where('id', $request->kandang_id)->exists();
            if (!$isKandangExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kandang not found',
                    'payload' => []
                ]);
            }

            $kandangId = $request->kandang_id;
        }

        for ($i = 0; $i < $request->nominal; $i++) {
            Livestock::create([
                'kandang_id' => $kandangId,
                'pakan' => $request->pakan,
                'limbah_id' => $request->limbah_id,
                'age' => $request->age,
                'gender' => $request->gender,
                'type_id' => $request->type_id,
                'code' => $this->generateRandomCode('TRK', 'livestocks', 'code'),
                'acquired_status' => 'INPUT',
                'acquired_year' => $request->acquired_year,
                'acquired_month' => $request->acquired_month,
                'acquired_month_name' => strtoupper(Carbon::createFromFormat('m', $request->acquired_month)->locale('id')->isoFormat('MMMM')),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success'
        ]);
    }

    function generateRandomCode($prefix, $table, $column)
    {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);

        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data))
            return generateRandomCode($prefix, $table, $column);

        return $rand;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id',
            'pakan' => 'nullable',
            'limbah' => 'nullable|exists:limbah,id',
            'age' => 'required',
            'gender' => 'required',
            'month' => 'required',
            'year' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $livestock = Livestock::findOrFail($request->id);

        $updateData = [
            'limbah_id' => $request->limbah,
            'age' => $request->age,
            'gender' => $request->gender
        ];
        if (!empty($request->pakan))
            $updateData['pakan'] = $request->pakan;

        if (isset($livestock->dead_year))
            $updateData['dead_month'] = $request->month;
        else
            $updateData['acquired_month'] = $request->month;

        if (isset($livestock->dead_year))
            $updateData['dead_year'] = $request->year;
        else
            $updateData['acquired_year'] = $request->year;

        $livestock->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => []
        ]);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $livestock = Livestock::findOrFail($request->id);
        if (count($livestock->livestockBuyItems()->get()) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ternak tidak bisa dihapus karena sedang dalam negosiasi transaksi pembelian'
            ]);
        }

        $livestock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => []
        ]);
    }

    public function updateStatus(Request $request)
    {
        $livestock = Livestock::findOrFail($request->id);

        $year = date('Y');
        $month = date('m');
        $monthName = \Carbon\Carbon::createFromFormat('m', $month)->locale('id')->isoFormat('MMMM');

        if (substr($request->status, 0, 4) == 'MATI') {
            $statusArr = explode(' - ', $request->status);
            $deadReason2 = $statusArr[1] ?? null;

            $livestock->update([
                'dead_year' => $year,
                'dead_month' => $month,
                'dead_month_name' => $monthName,
                'dead_reason_2' => $deadReason2
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

    public function report(Request $request)
    {
        $provinces = Province::all();

        $selectedRegency = isset($request->regency_id) ? Regency::find($request->regency_id) : (object)[];
        $selectedDistrict = isset($request->district_id) ? District::find($request->district_id) : (object)[];
        $selectedVillage = isset($request->village_id) ? Village::find($request->village_id) : (object)[];

        $dateStart = null;
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

        $livestockTypes = LivestockType::where('level', 1)
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
                'livestocksThroughKandang AS total_ternak' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNull('dead_year')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
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
            ])
            ->withCount([
                'livestocksThroughKandang AS transaksi_jual_beli' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('sold_deal_price')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                                ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                                ->where('sold_year', '>=', explode('/', $dateStart)[2])
                                ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                        });
                }
            ])
            ->withCount([
                'livestocksThroughKandang AS sedang_dijual' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNull('dead_year')
                        ->whereNull('sold_deal_price')
                        ->whereNotNull('sold_proposed_price')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where(function ($query) use ($dateStart, $dateEnd) {
                                $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                    ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                    ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                            });
                        });
                }
            ])
            ->withCount([
                'livestocksThroughKandang AS lahir' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->where('acquired_status', 'LAHIR')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where(function ($query) use ($dateStart, $dateEnd) {
                                $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                    ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                    ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                            });
                        });
                }
            ])
            ->withCount([
                'livestocksThroughKandang AS mati' => function ($query) use ($dateStart, $dateEnd) {
                    $query->has('kandang')
                        ->whereNotNull('dead_year')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where('dead_month', '>=', explode('/', $dateStart)[1])
                                ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                                ->where('dead_year', '>=', explode('/', $dateStart)[2])
                                ->where('dead_year', '<=', explode('/', $dateEnd)[2]);
                        });
                }
            ])
            ->orderBy('livestock_type')
            ->get();

        foreach ($livestockTypes as $key => $value) {
            $livestockTypeChildren = LivestockType::where('level', 2)
                ->withCount([
                    'livestocks AS total_ternak' => function ($query) use ($value) {
                        $query->whereHas('kandang', function ($query) use ($value) {
                            $query->where('type_id', $value->id);
                        })
                            ->whereNull('dead_year');
                    }
                ])
                ->withCount([
                    'livestocks AS transaksi_jual_beli' => function ($query) use ($value) {
                        $query->whereHas('kandang', function ($query) use ($value) {
                            $query->where('type_id', $value->id);
                        })
                            ->whereNotNull('sold_deal_price');
                    }
                ])
                ->withCount([
                    'livestocks AS sedang_dijual' => function ($query) use ($value) {
                        $query->whereHas('kandang', function ($query) use ($value) {
                            $query->where('type_id', $value->id);
                        })
                            ->whereNull('dead_year')
                            ->whereNull('sold_deal_price')
                            ->whereNotNull('sold_proposed_price');
                    }
                ])
                ->withCount([
                    'livestocks AS lahir' => function ($query) use ($value) {
                        $query->whereHas('kandang', function ($query) use ($value) {
                            $query->where('type_id', $value->id);
                        })
                            ->where('acquired_status', 'LAHIR');
                    }
                ])
                ->withCount([
                    'livestocks AS mati' => function ($query) use ($value) {
                        $query->whereHas('kandang', function ($query) use ($value) {
                            $query->where('type_id', $value->id);
                        })
                            ->whereNotNull('dead_year');
                    }
                ])
                ->where('parent_type_id', $value->id)
                ->orderBy('livestock_type')
                ->get();

            $livestockTypes[$key]->children = $livestockTypeChildren;
        }

        return view('livestocks.reports.livestock-type', compact('provinces', 'livestockTypes', 'dateStart', 'dateEnd', 'selectedRegency', 'selectedDistrict', 'selectedVillage'));
    }

    public function reportDetail($urlType, $livestockTypeId, Request $request)
    {
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
            $q = Livestock::with(['kandang.farmer', 'limbah'])
                ->whereHas('kandang', function ($query) use ($request) {
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
                    $q->whereNotNull('sold_deal_price')
                        ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                                ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                                ->where('sold_year', '>=', explode('/', $dateStart)[2])
                                ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                        });
                } else {
                    $q->whereNull('dead_year');

                    if ($urlType == 'total-ternak') {
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
                    } else {
                        $q->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                            $query->where(function ($query) use ($dateStart, $dateEnd) {
                                $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                                    ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                                    ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                                    ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                            });
                        });

                        if ($urlType == 'sedang-dijual') {
                            $q->whereNull('sold_deal_price')
                                ->whereNotNull('sold_proposed_price');
                        } else if ($urlType == 'lahir') {
                            $q->where('acquired_status', 'LAHIR');
                        }
                    }
                }
            }

            return DataTables::of($q)
                ->addIndexColumn()
                ->addColumn('farmer', function ($row) {
                    return $row->kandang?->farmer?->fullname;
                })
                ->addColumn('kandang', function ($row) {
                    return $row->kandang?->name;
                })
                ->addColumn('limbah', function ($row) {
                    return $row->limbah?->pengolahan_limbah;
                })
                ->addColumn('status', function ($row) {
                    if (isset($row->dead_year))
                        $res = 'MATI';
                    else if (isset($row->sold_year))
                        $res = 'JUAL';
                    else
                        $res = $row->acquired_status;
                    return $res;
                })
                ->addColumn('month', function ($row) {
                    if (isset($row->dead_year))
                        $res = $row->dead_month;
                    else if (isset($row->sold_year))
                        $res = $row->sold_month;
                    else
                        $res = $row->acquired_month;

                    if (!empty($res))
                        $res = \Carbon\Carbon::createFromFormat('m', $res)->locale('id')->isoFormat('MMMM');
                    else
                        $res = '';
                    return $res;
                })
                ->addColumn('year', function ($row) {
                    if (isset($row->dead_year))
                        $res = $row->dead_year;
                    else if (isset($row->sold_year))
                        $res = $row->sold_year;
                    else
                        $res = $row->acquired_year;

                    return $res;
                })
                ->addColumn('province', function ($row) {
                    return $row->kandang?->province?->name;
                })
                ->addColumn('regency', function ($row) {
                    return $row->kandang?->regency?->name;
                })
                ->addColumn('district', function ($row) {
                    return $row->kandang?->district?->name;
                })
                ->addColumn('village', function ($row) {
                    return $row->kandang?->village?->name;
                })
                ->rawColumns(['farmer', 'kandang', 'limbah', 'status', 'month', 'year', 'province', 'regency', 'village'])
                ->make(true);
        } else {
            $provinces = Province::all();
        }

        return view('livestocks.reports.livestock', compact('urlType', 'livestockType', 'dateStart', 'dateEnd', 'provinces'));
    }

    public function reportDetailExport($urlType, $livestockTypeId, Request $request)
    {
        return Excel::download(new LivestockReportExport($urlType, $livestockTypeId, $request), 'livestock_report_' . time() . '.xlsx');
    }

    public function reportExportMutation(Request $request) 
    {
        return Excel::download(new LivestockReportMutationDeadExport($request), 'livestock_mutation_report_' . time() . '.xlsx');
    }

    public function reportExportDead(Request $request) 
    {
        return Excel::download(new LivestockReportDeadExport($request), 'livestock_dead_report_' . time() . '.xlsx');
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

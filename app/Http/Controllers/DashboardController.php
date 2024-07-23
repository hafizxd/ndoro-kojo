<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Excel;
use App\Exports\ReportExport;
use Illuminate\Http\Request;
use App\Models\LivestockType;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $isOperator = false;

    public function dashboard(Request $request)
    {
        if (auth('web')->user() && auth('web')->user()->role == UserRole::OPERATOR && isset(auth('web')->user()->district)) {
            $this->isOperator = true;
        }

        $dateStart = null;
        $dateEnd = null;

        if (!empty($request->daterange) && $request->daterange != "#") {
            $dateExp = explode(' to ', $request->daterange);
            $dateStart = $dateExp[0];
            $dateEnd = $dateExp[1];
        }

        $typesHolder = DB::table('livestock_types')
            ->selectRaw('0 as count_jantan, 0 as count_betina, livestock_type')
            ->where('level', 1)
            ->orderBy('livestock_type')
            ->get();

        // Total ternak
        $dataTotalTernak = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type,
                MAX(C.gender) as gender
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->where('level', 1)
            ->whereNull('C.dead_year')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
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
            })
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id', 'C.gender')
            ->orderBy('livestock_type')
            ->get();

        $countTotalTernak = [
            'ALL' => 0,
            'JANTAN' => 0,
            'BETINA' => 0
        ];

        $arrTotalTernak = [];
        foreach ($typesHolder as $key => $value) {
            $arrTotalTernak[$key] = clone $value;
        }

        foreach ($dataTotalTernak as $value) {
            $countTotalTernak['ALL'] += $value->count;

            $keyType = array_search($value->livestock_type, array_column($arrTotalTernak, 'livestock_type'));

            if ($value->gender == 'JANTAN' || !isset($value->gender)) {
                $countTotalTernak['JANTAN'] += $value->count;
                $arrTotalTernak[$keyType]->count_jantan += $value->count;

            } else if ($value->gender == 'BETINA') {
                $countTotalTernak['BETINA'] += $value->count;
                $arrTotalTernak[$keyType]->count_betina += $value->count;
            }
        }

        // Total kandang
        $arrTotalKandang = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->where('level', 1)
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countTotalKandang = 0;
        foreach ($arrTotalKandang as $value) {
            $countTotalKandang += $value->count;
        }


        // Transaksi beli
        $dataBeli = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type,
                MAX(C.gender) as gender
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
                    $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                        ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                        ->where('sold_year', '>=', explode('/', $dateStart)[2])
                        ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                });
            })
            ->whereNotNull('sold_deal_price')
            ->where('level', 1)
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id', 'C.gender')
            ->orderBy('livestock_type')
            ->get();

        $countBeli = [
            'ALL' => 0,
            'JANTAN' => 0,
            'BETINA' => 0
        ];

        $arrBeli = [];
        foreach ($typesHolder as $key => $value) {
            $arrBeli[$key] = clone $value;
        }

        foreach ($dataBeli as $value) {
            $countBeli['ALL'] += $value->count;

            $keyType = array_search($value->livestock_type, array_column($arrBeli, 'livestock_type'));

            if ($value->gender == 'JANTAN' || !isset($value->gender)) {
                $countBeli['JANTAN'] += $value->count;
                $arrBeli[$keyType]->count_jantan += $value->count;

            } else if ($value->gender == 'BETINA') {
                $countBeli['BETINA'] += $value->count;
                $arrBeli[$keyType]->count_betina += $value->count;
            }
        }


        // Sedang dijual
        $dataJual = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type,
                MAX(C.gender) as gender
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->whereNull('C.dead_year')
            ->whereNull('C.sold_deal_price')
            ->whereNotNull('C.sold_proposed_price')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
                    $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                        ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                        ->where('sold_year', '>=', explode('/', $dateStart)[2])
                        ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                });
            })
            ->where('level', 1)
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id', 'C.gender')
            ->orderBy('livestock_type')
            ->get();

        $countJual = [
            'ALL' => 0,
            'JANTAN' => 0,
            'BETINA' => 0
        ];

        $arrJual = [];
        foreach ($typesHolder as $key => $value) {
            $arrJual[$key] = clone $value;
        }

        foreach ($dataJual as $value) {
            $countJual['ALL'] += $value->count;

            $keyType = array_search($value->livestock_type, array_column($arrJual, 'livestock_type'));

            if ($value->gender == 'JANTAN' || !isset($value->gender)) {
                $countJual['JANTAN'] += $value->count;
                $arrJual[$keyType]->count_jantan += $value->count;

            } else if ($value->gender == 'BETINA') {
                $countJual['BETINA'] += $value->count;
                $arrJual[$keyType]->count_betina += $value->count;
            }
        }

        // Lahir
        $dataLahir = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type,
                MAX(C.gender) as gender
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->where('acquired_status', 'LAHIR')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
                    $query->where('acquired_month', '>=', explode('/', $dateStart)[1])
                        ->where('acquired_month', '<=', explode('/', $dateEnd)[1])
                        ->where('acquired_year', '>=', explode('/', $dateStart)[2])
                        ->where('acquired_year', '<=', explode('/', $dateEnd)[2]);
                });
            })
            ->where('level', 1)
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id', 'C.gender')
            ->orderBy('livestock_type')
            ->get();

        $countLahir = [
            'ALL' => 0,
            'JANTAN' => 0,
            'BETINA' => 0
        ];

        $arrLahir = [];
        foreach ($typesHolder as $key => $value) {
            $arrLahir[$key] = clone $value;
        }

        foreach ($dataLahir as $value) {
            $countLahir['ALL'] += $value->count;

            $keyType = array_search($value->livestock_type, array_column($arrLahir, 'livestock_type'));

            if ($value->gender == 'JANTAN' || !isset($value->gender)) {
                $countLahir['JANTAN'] += $value->count;
                $arrLahir[$keyType]->count_jantan += $value->count;

            } else if ($value->gender == 'BETINA') {
                $countLahir['BETINA'] += $value->count;
                $arrLahir[$keyType]->count_betina += $value->count;
            }
        }

        // Mati
        $dataMati = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type,
                MAX(C.gender) as gender
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->whereNotNull('C.dead_year')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
                    $query->where('dead_month', '>=', explode('/', $dateStart)[1])
                        ->where('dead_month', '<=', explode('/', $dateEnd)[1])
                        ->where('dead_year', '>=', explode('/', $dateStart)[2])
                        ->where('dead_year', '<=', explode('/', $dateEnd)[2]);
                });
            })
            ->where('level', 1)
            ->when($this->isOperator, function ($query) {
                $query->where('B.district_id', auth('web')->user()->district_id);
            })
            ->groupBy('A.id', 'C.gender')
            ->orderBy('livestock_type')
            ->get();

        $countMati = [
            'ALL' => 0,
            'JANTAN' => 0,
            'BETINA' => 0
        ];

        $arrMati = [];
        foreach ($typesHolder as $key => $value) {
            $arrMati[$key] = clone $value;
        }

        foreach ($dataMati as $value) {
            $countMati['ALL'] += $value->count;

            $keyType = array_search($value->livestock_type, array_column($arrMati, 'livestock_type'));

            if ($value->gender == 'JANTAN' || !isset($value->gender)) {
                $countMati['JANTAN'] += $value->count;
                $arrMati[$keyType]->count_jantan += $value->count;

            } else if ($value->gender == 'BETINA') {
                $countMati['BETINA'] += $value->count;
                $arrMati[$keyType]->count_betina += $value->count;
            }
        }


        // serve as table


        return view('dashboard.index', compact('dateStart', 'dateEnd', 'arrTotalTernak', 'countTotalTernak', 'arrTotalKandang', 'countTotalKandang', 'arrBeli', 'countBeli', 'arrJual', 'countJual', 'arrLahir', 'countLahir', 'arrMati', 'countMati'));
    }

    public function export()
    {
        return Excel::download(new ReportExport, 'report_' . time() . '.xlsx');
    }
}

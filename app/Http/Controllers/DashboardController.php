<?php

namespace App\Http\Controllers;

use Excel;
use App\Exports\ReportExport;
use Illuminate\Http\Request;
use App\Models\LivestockType;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {
        $dateStart = null;
        $dateEnd = null;

        if (!empty($request->daterange) && $request->daterange != "#") {
            $dateExp = explode(' to ', $request->daterange);
            $dateStart = $dateExp[0];
            $dateEnd = $dateExp[1];
        }

        // Total ternak
        $arrTotalTernak = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
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
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countTotalTernak = 0;
        foreach ($arrTotalTernak as $value) {
            $countTotalTernak += $value->count;
        }

        // Total kandang
        $arrTotalKandang = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->where('level', 1)
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countTotalKandang = 0;
        foreach ($arrTotalKandang as $value) {
            $countTotalKandang += $value->count;
        }


        // Transaksi beli
        $arrBeli = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
            ')
            ->join('kandang as B', 'A.id', '=', 'B.type_id')
            ->join('livestocks as C', 'B.id', '=', 'C.kandang_id')
            ->join('livestock_buys as D', 'C.id', '=', 'D.livestock_id')
            ->when(isset($dateStart) && isset($dateEnd), function ($query) use ($dateStart, $dateEnd) {
                $query->where(function ($query) use ($dateStart, $dateEnd) {
                    $query->where('sold_month', '>=', explode('/', $dateStart)[1])
                        ->where('sold_month', '<=', explode('/', $dateEnd)[1])
                        ->where('sold_year', '>=', explode('/', $dateStart)[2])
                        ->where('sold_year', '<=', explode('/', $dateEnd)[2]);
                });
            })
            ->where('level', 1)
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countBeli = 0;
        foreach ($arrBeli as $value) {
            $countBeli += $value->count;
        }


        // Sedang dijual
        $arrJual = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
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
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countJual = 0;
        foreach ($arrJual as $value) {
            $countJual += $value->count;
        }

        // Lahir
        $arrLahir = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
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
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countLahir = 0;
        foreach ($arrLahir as $value) {
            $countLahir += $value->count;
        }

        // Mati
        $arrMati = DB::table('livestock_types as A')
            ->selectRaw('
                COUNT(A.id) as count,
                MAX(livestock_type) as livestock_type
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
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countMati = 0;
        foreach ($arrMati as $value) {
            $countMati += $value->count;
        }


        // serve as table
        

        return view('dashboard.index', compact('dateStart', 'dateEnd', 'arrTotalTernak', 'countTotalTernak', 'arrTotalKandang', 'countTotalKandang', 'arrBeli', 'countBeli', 'arrJual', 'countJual', 'arrLahir', 'countLahir', 'arrMati', 'countMati'));
    }

    public function export() {
        return Excel::download(new ReportExport, 'report_'.time().'.xlsx');
    }
}

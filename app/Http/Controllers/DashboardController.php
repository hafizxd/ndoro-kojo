<?php

namespace App\Http\Controllers;

use Excel;
use App\Exports\ReportExport;
use Illuminate\Http\Request;
use App\Models\LivestockType;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard() {
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
            ->where(function ($query) {
                $query->whereNotNull('C.sold_deal_price')
                    ->orWhere('acquired_status', 'BELI');
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
            ->where('level', 1)
            ->groupBy('A.id')
            ->orderBy('livestock_type')
            ->get();

        $countMati = 0;
        foreach ($arrMati as $value) {
            $countMati += $value->count;
        }


        // serve as table
        

        return view('dashboard.index', compact('arrTotalTernak', 'countTotalTernak', 'arrTotalKandang', 'countTotalKandang', 'arrBeli', 'countBeli', 'arrJual', 'countJual', 'arrLahir', 'countLahir', 'arrMati', 'countMati'));
    }

    public function export() {
        return Excel::download(new ReportExport, 'report_'.time().'.xlsx');
    }
}

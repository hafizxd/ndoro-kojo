<?php

namespace App\Http\Controllers;

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
                ->rawColumns(['district', 'village'])
                ->make(true);
        }

        return view('livestocks.index');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Kandang;
use App\Models\Livestock;

class LivestockController extends Controller
{
    public function index(Request $request) {
        $livestocks = Livestock::whereHas('kandang', function($query) {
            $query->where('farmer_id', Auth::user()->id);
        })
        ->with('kandang')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestocks
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'kandang.name' => 'required',
            'kandang.type_id' => 'required|exists:livestock_types,id',
            'kandang.panjang' => 'required',
            'kandang.lebar' => 'required',
            'kandang.province_id' => 'required|exists:provinces,id',
            'kandang.regency_id' => 'required|exists:regencies,id',
            'kandang.district_id' => 'required|exists:districts,id',
            'kandang.village_id' => 'required|exists:villages,id',
            'livestock.pakan_id' => 'required|exists:pakan,id',
            'livestock.limbah_id' => 'required|exists:limbah,id',
            'livestock.age' => 'required|in:ANAK,DEWASA',
            'livestock.type_id' => 'required|exists:livestock_types,id',
            'livestock.sensor_status' => 'required|in:TERPASANG,TIDAK TERPASANG'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails.',
                'payload' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $reqArr = $request->toArray();
        $kandangReq = $reqArr['kandang'];
        $kandangReq['farmer_id'] = Auth::user()->id;

        $kandang = Kandang::create($kandangReq);

        $livestockReq = $reqArr['livestock'];
        $livestockReq['kandang_id'] = $kandang->id;
        $livestockReq['acquired_status'] = 'INPUT';
        $livestock = Livestock::create($livestockReq);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => [
                'kandang' => $kandang,
                'livestock' => $livestock
            ]
        ]);
    }
}

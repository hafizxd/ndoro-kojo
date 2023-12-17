<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Kandang;
use App\Models\Livestock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LivestockController extends Controller
{
    public function index(Request $request) {
        $livestocks = Livestock::whereHas('kandang', function($query) use ($request) {
            $query->where('farmer_id', Auth::user()->id)
                ->when(isset($request->kandang_type_id), function($query2) use ($request) {
                    $query2->where('type_id', $request->kandang_type_id);
                });
        })
        ->when(isset($request->kandang_id), function($query) use ($request) {
            $query->where('kandang_id', $request->kandang_id);
        })
        ->when(isset($request->livestock_type_id), function($query) use ($request) {
            $query->where('type_id', $request->livestock_type_id);
        })
        ->whereNull('dead_type')
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
        $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');
        $livestockReq['kandang_id'] = $kandang->id;
        $livestockReq['acquired_status'] = 'INPUT';
        $livestockReq['acquired_year'] = date('Y');
        $livestockReq['acquired_month'] = date('m');
        $livestockReq['acquired_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
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

    function generateRandomCode($prefix, $table, $column) {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);
    
        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data)) 
            return generateRandomCode($prefix, $table, $column);
    
        return $rand;
    }

    public function birthStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'kandang_id' => 'required|exists:kandang,id',
            'pakan_id' => 'required|exists:pakan,id',
            'limbah_id' => 'required|exists:limbah,id',
            'age' => 'required|in:ANAK,DEWASA',
            'type_id' => 'required|exists:livestock_types,id',
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

        // validate kandang
        $isKandangExists = Kandang::where('farmer_id', Auth::user()->id)->where('kandang_id', $request->kandang_id)->exists();
        if (!$isKandangExists) {
            return response()->json([
                'success' => false,
                'message' => 'Kandang not found',
                'payload' => []
            ]);
        }


        $livestockReq = $request->toArray();
        $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');
        $livestockReq['acquired_status'] = "BELI";
        $livestockReq['acquired_year'] = date('Y');
        $livestockReq['acquired_month'] = date('m');
        $livestockReq['acquired_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
        $livestock = Livestock::create($livestockReq);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]);
    }

    public function deadUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id',
            'dead_type' => 'required',
            'dead_reason' => 'required',
            'dead_year' => 'required',
            'dead_month' => 'required'
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

        $livestockReq = $request->toArray();
        unset($livestockReq['livestock_id']);
        $livestockReq['dead_month_name'] = strtoupper(Carbon::createFromFormat('m', $request->dead_month)->locale('id')->isoFormat('MMMM'));

        $livestock = Livestock::where('id', $request->id)
            ->whereHas('kandang', function ($query) {
                $query->where('farmer_id', Auth::user()->id);
            })
            ->whereNull('dead_type')
            ->firstOrFail();
            
        $livestock->update($livestockReq);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]);
    }
}

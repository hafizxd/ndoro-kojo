<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Kandang;
use App\Models\Livestock;
use App\Models\Sensor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LivestockController extends Controller
{
    public function index(Request $request)
    {
        $livestocks = Livestock::whereHas('kandang', function ($query) use ($request) {
            $query->where('farmer_id', Auth::user()->id)
                ->when(isset($request->kandang_type_id), function ($query2) use ($request) {
                    $query2->where('type_id', $request->kandang_type_id);
                });
        })
            ->when(isset($request->kandang_id), function ($query) use ($request) {
                $query->where('kandang_id', $request->kandang_id);
            })
            ->when(isset($request->livestock_type_id), function ($query) use ($request) {
                $query->where('type_id', $request->livestock_type_id);
            })
            ->whereNull('dead_year')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestocks
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kandang.name' => 'required',
            'kandang.type_id' => 'required|exists:livestock_types,id',
            'kandang.panjang' => 'required',
            'kandang.lebar' => 'required',
            'kandang.province_id' => 'required|exists:provinces,id',
            'kandang.regency_id' => 'required|exists:regencies,id',
            'kandang.district_id' => 'required|exists:districts,id',
            'kandang.village_id' => 'required|exists:villages,id',
            'kandang.sensor_status' => 'required|in:TERPASANG,TIDAK TERPASANG',
            'livestock.pakan' => 'nullable',
            'livestock.limbah_id' => 'required|exists:limbah,id',
            'livestock.age' => 'required|in:ANAK,MUDA,DEWASA,BIBIT INDUK,BIBIT PEJANTAN',
            'livestock.type_id' => 'required|exists:livestock_types,id',
            'livestock.gender' => 'nullable|in:JANTAN,BETINA',
            'livestock.nominal' => 'nullable',
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
        unset($kandangReq['sensor']);

        $kandang = Kandang::create($kandangReq);

        $nominal = $request->nominal ?? 1;

        $livestock = null;
        for ($i = 0; $i < $nominal; $i++) {
            $livestockReq = $reqArr['livestock'];
            $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');
            $livestockReq['kandang_id'] = $kandang->id;
            $livestockReq['acquired_status'] = 'INPUT';
            $livestockReq['acquired_year'] = date('Y');
            $livestockReq['acquired_month'] = date('m');
            $livestockReq['acquired_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
            $livestockReq['availability'] = 'TERSEDIA';
            $livestock = Livestock::create($livestockReq);
        }

        $sensorReq = $reqArr['kandang']['sensor'];
        $sensorReq['kandang_id'] = $kandang->id;
        $sensor = Sensor::create($sensorReq);

        $kandang = Kandang::with('sensor')->findOrFail($kandang->id);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => [
                'kandang' => $kandang,
                'livestock' => $livestock
            ]
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

    public function storeFree(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kandang_id' => 'required|exists:kandang,id',
            'status' => 'required|in:JUAL,BELI,LAHIR,INPUT,MATI',
            'pakan' => 'nullable',
            'limbah_id' => 'required|exists:limbah,id',
            'age' => 'required|in:ANAK,MUDA,DEWASA,BIBIT INDUK,BIBIT PEJANTAN',
            'type_id' => 'required|exists:livestock_types,id',
            'nominal' => 'nullable',
            'livestock.gender' => 'nullable|in:JANTAN,BETINA'
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
        $isKandangExists = Kandang::where('farmer_id', Auth::user()->id)->where('id', $request->kandang_id)->exists();
        if (!$isKandangExists) {
            return response()->json([
                'success' => false,
                'message' => 'Kandang not found',
                'payload' => []
            ]);
        }


        $livestockReq = $request->toArray();
        unset($livestockReq['status']);
        $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');
        $livestockReq['acquired_status'] = $request->status == "MATI" ? "INPUT" : $request->status;
        $livestockReq['acquired_year'] = date('Y');
        $livestockReq['acquired_month'] = date('m');
        $livestockReq['acquired_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));

        if ($request->status == 'MATI') {
            $livestockReq['dead_year'] = date('Y');
            $livestockReq['dead_month'] = date('m');
            $livestockReq['dead_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
            $livestockReq['availability'] = 'TIDAK TERSEDIA';
        } else if ($request->status == 'JUAL') {
            $livestockReq['sold_year'] = date('Y');
            $livestockReq['sold_month'] = date('m');
            $livestockReq['sold_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
            $livestockReq['sold_proposed_price'] = 1;
            $livestockReq['sold_deal_price'] = 1;
            $livestockReq['availability'] = 'TIDAK TERSEDIA';
        }

        $nominal = $request->nominal ?? 1;

        $livestocks = [];
        for ($i = 0; $i < $nominal; $i++) {
            $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');

            $livestock = Livestock::create($livestockReq);
            $livestocks[] = Livestock::findOrFail($livestock->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestocks
        ]);
    }

    public function birthStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kandang_id' => 'required|exists:kandang,id',
            'pakan' => 'nullable',
            'limbah_id' => 'required|exists:limbah,id',
            'age' => 'required|in:ANAK,MUDA,DEWASA,BIBIT INDUK,BIBIT PEJANTAN',
            'type_id' => 'required|exists:livestock_types,id',
            'nominal' => 'nullable',
            'livestock.gender' => 'nullable|in:JANTAN,BETINA'
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
        $isKandangExists = Kandang::where('farmer_id', Auth::user()->id)->where('id', $request->kandang_id)->exists();
        if (!$isKandangExists) {
            return response()->json([
                'success' => false,
                'message' => 'Kandang not found',
                'payload' => []
            ]);
        }


        $livestockReq = $request->toArray();
        $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');
        $livestockReq['acquired_status'] = "LAHIR";
        $livestockReq['acquired_year'] = date('Y');
        $livestockReq['acquired_month'] = date('m');
        $livestockReq['acquired_month_name'] = strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM'));
        $livestockReq['availability'] = 'TERSEDIA';

        $nominal = $request->nominal ?? 1;

        $livestocks = [];
        for ($i = 0; $i < $nominal; $i++) {
            $livestockReq['code'] = $this->generateRandomCode('TRK', 'livestocks', 'code');

            $livestocks[] = Livestock::create($livestockReq);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestocks
        ]);
    }

    public function deadUpdate(Request $request)
    {
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
        $livestockReq['availability'] = 'TIDAK TERSEDIA';

        $livestock = Livestock::where('id', $request->id)
            ->whereHas('kandang', function ($query) {
                $query->where('farmer_id', Auth::user()->id);
            })
            ->whereNull('dead_year')
            ->firstOrFail();

        $livestock->update($livestockReq);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id',
            'status' => 'required|in:BELI,JUAL,LAHIR,MATI',
            'month' => 'required',
            'year' => 'required'
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

        $livestock = Livestock::whereHas('kandang', function ($query) {
            $query->where('farmer_id', Auth::user()->id);
        })->findOrFail($request->id);

        $updateData = [];

        if ($request->status == 'BELI' || $request->status == 'LAHIR') {
            $updateData = array_merge($updateData, [
                'acquired_status' => $request->status,
                'acquired_year' => $request->year,
                'acquired_month' => $request->month,
                'acquired_month_name' => strtoupper(Carbon::createFromFormat('m', $request->month)->locale('id')->isoFormat('MMMM'))
            ]);
        } else if ($request->status == 'MATI') {
            $updateData = array_merge($updateData, [
                'dead_year' => $request->year,
                'dead_month' => $request->month,
                'dead_month_name' => strtoupper(Carbon::createFromFormat('m', $request->month)->locale('id')->isoFormat('MMMM'))
            ]);
        } else if ($request->status == 'JUAL') {
            $updateData = array_merge($updateData, [
                'sold_proposed_price' => 1,
                'sold_deal_price' => 1,
                'sold_year' => $request->year,
                'sold_month' => $request->month,
                'sold_month_name' => strtoupper(Carbon::createFromFormat('m', $request->month)->locale('id')->isoFormat('MMMM'))
            ]);
        }

        $livestock->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]);
    }

    public function updateImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id',
            'image' => 'required|image',
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

        $livestock = Livestock::whereHas('kandang', function ($query) {
            $query->where('farmer_id', Auth::user()->id);
        })->findOrFail($request->id);

        $fileName = time() . '_' . $request->image->getClientOriginalName();
        $request->image->storeAs('livestocks', $fileName, 'public');

        $livestock->update(['sold_image' => $fileName]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => []
        ]);
    }
}

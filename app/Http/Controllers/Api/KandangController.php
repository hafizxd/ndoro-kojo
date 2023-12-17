<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Kandang;

class KandangController extends Controller
{
    public function index(Request $request) {
        if (!isset($request->type_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Type id is required',
                'payload' => []
            ], 422);
        }

        $kandangs = Kandang::where('farmer_id', Auth::user()->id)->where('type_id', $request->type_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $kandangs
        ], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type_id' => 'required|exists:livestock_types,id',
            'panjang' => 'required',
            'lebar' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
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

        $kandangReq = $request->toArray();
        $kandangReq['farmer_id'] = Auth::user()->id;

        $kandang = Kandang::create($kandangReq);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $kandang
        ]);
    }
}

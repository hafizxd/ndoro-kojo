<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class ReferenceController extends Controller
{
    public function provinceList() {
        $res = Province::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function regencyList(Request $request) {
        if (!isset($request->province_id))  {
            return response()->json([
                'success' => false,
                'message' => 'Tambahkan province_id',
                'payload' => []
            ]);
        }

        $res = Regency::where('province_id', $request->province_id)->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function districtList(Request $request) {
        if (!isset($request->regency_id))  {
            return response()->json([
                'success' => false,
                'message' => 'Tambahkan regency_id',
                'payload' => []
            ]);
        }

        $res = District::where('regency_id', $request->regency_id)->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function villageList(Request $request) {
        if (!isset($request->district_id))  {
            return response()->json([
                'success' => false,
                'message' => 'Tambahkan district_id',
                'payload' => []
            ]);
        }

        $res = Village::where('district_id', $request->district_id)->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }
}

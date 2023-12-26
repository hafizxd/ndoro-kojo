<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\Pakan;
use App\Models\Limbah;
use App\Models\LivestockType;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Storage;

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

    public function pakanList() {
        $res = Pakan::orderBy('jenis_pakan', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function limbahList() {
        $res = Limbah::orderBy('pengolahan_limbah', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function livestockTypeList() {
        $res = LivestockType::where('level', 1)->orderBy('livestock_type')->with('livestockChildren')->get();

        foreach ($res as $key => $value) {
            if (isset($value->image)) {
                $res[$key]->image = Storage::url($value->image);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }

    public function sliderCategoryList() {
        $res = ArticleCategory::orderBy('article_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }
}

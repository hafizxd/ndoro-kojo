<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\Kandang;

class ReportController extends Controller
{
    public function reportByKandangType(Request $request) {
        $kandangTypeId = $request->kandang_type_id;
        if (!isset($kandangTypeId)) {
            return response()->json([
                'success' => false,
                'message' => 'Kandang Type ID is required',
                'payload' => []
            ]); 
        }

        $kandangs = Kandang::where('farmer_id', Auth::user()->id)->where('type_id', $kandangTypeId)->with('livestocks')->get();

        $grandTotal = 0;
        $grandBeli = 0;
        $grandJual = 0;
        $grandLahir = 0;
        $grandMati = 0;
        $grandAvailable = 0;

        foreach ($kandangs as $key => $kandang) {
            $total = 0;
            $beli = 0;
            $jual = 0;
            $lahir = 0;
            $mati = 0;
            $available = 0;

            foreach ($kandang->livestocks as $livestock) {
                $total++;
                
                if (empty($livestock->dead_year)) {
                    if ($livestock->acquired_status == "BELI") 
                        $beli++;
                    else if ($livestock->acquired_status == "LAHIR")
                        $lahir++;
                    
                    if (isset($livestock->sold_proposed_price))
                        $jual++;
                } else {
                    $mati++;
                }
            }

            $available = $total - $jual - $mati;

            $kandangs[$key]->statistic = (object) [
                'total' => $total,
                'beli' => $beli,
                'jual' => $jual,
                'mati' => $mati,
                'available' => $available
            ];

            $grandTotal += $total;
            $grandBeli += $beli;
            $grandJual += $jual;
            $grandLahir += $lahir;
            $grandMati += $mati;
            $grandAvailable += $available;
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => [
                'statistic' => [
                    'total' => $grandTotal,
                    'beli' => $grandBeli,
                    'jual' => $grandJual,
                    'mati' => $grandMati,
                    'available' => $grandAvailable
                ],
                'kandang' => $kandangs
            ]
        ]);
    }

    public function reportByKandangId(Request $request) {
        $kandangId = $request->kandang_id;
        if (!isset($kandangId)) {
            return response()->json([
                'success' => false,
                'message' => 'Kandang ID is required',
                'payload' => []
            ]); 
        }

        $kandang = Kandang::where('farmer_id', Auth::user()->id)
            ->with('livestocks')
            ->withCount(['livestocks' => function ($query) {
                $query->whereNull('dead_year')
                    ->whereNull('sold_deal_price');
            }])
            ->findOrFail($kandangId);

        
        $total = 0;
        $beli = 0;
        $jual = 0;
        $lahir = 0;
        $mati = 0;
        $available = 0;

        foreach ($kandang->livestocks as $livestock) {
            $total++;
            
            if (empty($livestock->dead_year)) {
                if ($livestock->acquired_status == "BELI") 
                    $beli++;
                else if ($livestock->acquired_status == "LAHIR")
                    $lahir++;
                
                if (isset($livestock->sold_proposed_price))
                    $jual++;
            } else {
                $mati++;
            }
        }

        $available = $total - $jual - $mati;

        $kandang->statistic = (object) [
            'total' => $total,
            'beli' => $beli,
            'jual' => $jual,
            'mati' => $mati,
            'available' => $available
        ];

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $kandang
        ]);
    }

    public function reportByLivestockType(Request $request) {
        $livestockTypeId = $request->livestock_type_id;
        if (!isset($livestockTypeId)) {
            return response()->json([
                'success' => false,
                'message' => 'Livestock Type ID is required',
                'payload' => []
            ]); 
        }

        $livestocks = Livestock::whereHas('kandang', function ($query) {
                $query->where('farmer_id', Auth::user()->id);
            })
            ->where('type_id', $livestockTypeId)
            ->get();

        $total = 0;
        $beli = 0;
        $jual = 0;
        $lahir = 0;
        $mati = 0;
        $available = 0;

        foreach ($livestocks as $key => $livestock) {
            $total++;
                
            if (empty($livestock->dead_year)) {
                if ($livestock->acquired_status == "BELI") 
                    $beli++;
                else if ($livestock->acquired_status == "LAHIR")
                    $lahir++;
                
                if (isset($livestock->sold_proposed_price))
                    $jual++;
            } else {
                $mati++;
            }

            $available = $total - $jual - $mati;
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => [
                'total' => $total,
                'beli' => $beli,
                'jual' => $jual,
                'mati' => $mati,
                'available' => $available
            ]
        ]);
    }
}

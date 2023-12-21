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

        foreach ($kandangs as $key => $kandang) {
            $total = 0;
            $beli = 0;
            $jual = 0;
            $lahir = 0;
            $mati = 0;
            $available = 0;

            foreach ($kandang->livestocks as $livestock) {
                $total++;
                
                if (empty($livestock->dead_month)) {
                    if ($livestock->acquired_status == "BELI") 
                        $beli++;
                    else if ($livestock->acquired_status == "LAHIR")
                        $lahir++;
                    
                    if (isset($livestock->proposed_price))
                        $jual++;
                } else if (isset($livestock->dead_month)) {
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
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $kandangs
        ]);
    }
}

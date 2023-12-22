<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\LivestockBuy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function indexEvent() {
        $livestocks = Livestock::whereNotNull('sold_proposed_price')
            ->whereNull('sold_deal_price')
            ->orderBy('updated_at', 'desc')
            ->with(['kandang.farmer', 'livestockType'])
            ->get();

        foreach ($livestocks as $key => $livestock) {
            if ($livestock->kandang->farmer_id == Auth::user()->id) {
                $livestocks[$key]->is_mine = true;
            } else {
                $livestocks[$key]->is_mine = false;
            }

            if (isset($livestocks[$key]->livestockType->image)) {
                $livestocks[$key]->livestockType->image = Storage::url($livestocks[$key]->livestockType->image);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestocks
        ]);
    }

    public function sell(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:livestocks,id',
            'proposed_price' => 'required'
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

        $livestock = Livestock::whereHas('kandang', function($query) {
                $query->where('farmer_id', Auth::user()->id);
            })
            ->where('id', $request->id)
            ->whereNull('sold_proposed_price')
            ->firstOrFail();

        $livestock->update([
            'sold_proposed_price' => $request->proposed_price
        ]); 

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]);
    }   

    public function buy(Request $request) {
        $validator = Validator::make($request->all(), [
            'livestock_id' => 'required|exists:livestocks,id',
            'kandang_id' => 'required|exists:kandang,id',
            'deal_price' => 'required'
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

        $livestock = Livestock::whereHas('kandang', function($query) {
                $query->where('farmer_id', '!=', Auth::user()->id);
            })
            ->whereNotNull('sold_proposed_price')
            ->whereNull('sold_deal_price')
            ->with('kandang')
            ->first();

        if (!isset($livestock)) {
            return response()->json([
                'success' => false,
                'message' => 'Livestock is not a valid sold item',
                'payload' => []
            ]); 
        }

        LivestockBuy::create([
            'livestock_id' => $request->livestock_id,
            'seller_id' => $livestock->kandang->farmer_id,
            'buyer_id' => Auth::user()->id,
            'price' => $request->deal_price,
            'status' => 'SUDAH TERJUAL',
            'deal_at' => date('Y-m-d H:i:s')
        ]);

        $livestock->update([
            'sold_deal_price' => $request->deal_price,
            'sold_year' => date('Y'),
            'sold_month' => date('m'),
            'sold_month_name' => strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM')),
            'acquired_status' => 'JUAL',
            'availability' => 'TIDAK TERSEDIA'
        ]);

        // copy livestock
        $livestock = Livestock::create([
            'kandang_id' => $request->kandang_id,
            'pakan_id' => $livestock->pakan_id,
            'limbah_id' => $livestock->limbah_id,
            'age' => $livestock->age,
            'type_id' => $livestock->type_id,
            'acquired_status' => 'BELI',
            'acquired_year' => date('Y'),
            'acquired_month' => date('m'),
            'acquired_month_name' => strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM')),
            'code' => $this->generateRandomCode('TRK', 'livestocks', 'code'),
            'availability' => 'TERSEDIA'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestock
        ]); 
    }

    function generateRandomCode($prefix, $table, $column) {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);
    
        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data)) 
            return generateRandomCode($prefix, $table, $column);
    
        return $rand;
    }
}

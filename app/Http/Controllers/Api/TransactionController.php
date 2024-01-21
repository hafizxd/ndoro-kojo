<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\LivestockBuy;
use Carbon\Carbon;
use App\Models\Farmer;
use App\Notifications\NegotiateStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function indexEvent()
    {
        $livestocks = Livestock::whereNotNull('sold_proposed_price')
            ->whereNull('sold_deal_price')
            ->whereNull('dead_year')
            ->orderBy('updated_at', 'desc')
            ->with(['kandang.farmer', 'livestockType'])
            ->get();

        $result = [];

        foreach ($livestocks as $key => $livestock) {
            if ($livestock->kandang->farmer_id == Auth::user()->id) {
                $livestock->is_mine = true;
            } else {
                $livestock->is_mine = false;
            }

            if (isset($livestock->livestockType->image)) {
                $livestock->livestockType->image = Storage::url($livestock->livestockType->image);
            }

            if (isset($livestock->sold_image)) {
                $livestock->sold_image = Storage::url('livestocks/' . $livestock->sold_image);
            }

            $farmerId = $livestock->kandang->farmer_id;
            $kandandLivestockTypeId = $livestock->kandang->livestockType->id;
            $currentResultKey = $farmerId . '_' . $kandandLivestockTypeId;

            if (isset($result[$currentResultKey])) {
                $result[$currentResultKey]->price_total += $livestock->sold_proposed_price;
                $result[$currentResultKey]->count_total++;

                $countSubTypeKey = array_search($livestock->livestockType->livestock_type, array_column($result[$currentResultKey]->count_per_subtype, 'livestock_type'));
                if (isset($countSubTypeKey)) {
                    $result[$currentResultKey]->count_per_subtype[$countSubTypeKey]['count'] += 1;
                } else {
                    $result[$currentResultKey]->count_per_subtype[] = [
                        'livestock_type' => $livestock->livestockType->livestock_type,
                        'count' => 1
                    ];
                }

                if (isset($livestock->sold_image)) {
                    $result[$currentResultKey]->livestock_images[] = $livestock->sold_image;
                }

                $result[$currentResultKey]->items[] = $livestock;
            } else {
                $sellGroup = [
                    'is_mine' => $livestock->is_mine,
                    'image' => isset($livestock->kandang?->livestockType) ? Storage::url($livestock->kandang?->livestockType->image) : $livestock->kandang?->livestockType->image,
                    'livestock_type' => $livestock->kandang?->livestockType?->livestock_type,
                    'price_total' => $livestock->sold_proposed_price,
                    'count_total' => 1,
                    'count_per_subtype' => [
                        [
                            'livestock_type' => $livestock->livestockType->livestock_type,
                            'count' => 1
                        ]
                    ],
                    'livestock_images' => [],
                    'seller' => $livestock->kandang?->farmer,
                    'items' => [$livestock]
                ];

                if (isset($livestock->sold_image)) {
                    $sellGroup['livestock_images'][] = $livestock->sold_image;
                }

                $result[$currentResultKey] = (object) $sellGroup;
            }
        }

        $result = array_values($result);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $result
        ]);
    }

    public function sell(Request $request)
    {
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

        $livestock = Livestock::whereHas('kandang', function ($query) {
            $query->where('farmer_id', Auth::user()->id);
        })
            ->where('id', $request->id)
            ->whereNull('sold_proposed_price')
            ->whereNull('dead_year')
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

    public function buy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kandang_id' => 'required|exists:kandang,id',
            'deal_price' => 'required',
            'livestocks' => 'required|array',
            'livestocks.*.livestock_id' => 'required'
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

        $buyItems = [];

        foreach ($request->livestocks as $value) {
            $livestock = Livestock::whereHas('kandang', function ($query) {
                $query->where('farmer_id', '!=', Auth::user()->id);
            })
                ->whereNotNull('sold_proposed_price')
                ->whereNull('sold_deal_price')
                ->whereNull('dead_year')
                ->where('id', $value['livestock_id'])
                ->first();

            if (!isset($livestock)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livestock is not a valid sold item',
                    'payload' => []
                ], 400);
            }

            $buyItems[] = ['livestock_id' => $livestock->id];
        }

        $livestockBuy = LivestockBuy::create([
            'kandang_id' => $request->kandang_id,
            'seller_id' => $livestock->kandang->farmer_id,
            'buyer_id' => Auth::user()->id,
            'price' => $request->deal_price
        ]);

        $livestockBuy->items()->createMany($buyItems);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => []
        ]);
    }

    public function indexProposal()
    {
        $livestockBuys = LivestockBuy::where('seller_id', Auth::user()->id)
            ->with(['buyer', 'items.livestock'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $livestockBuys
        ]);
    }

    public function updateProposal(Request $request, $id)
    {
        $livestockBuy = LivestockBuy::with('items.livestock')->where('seller_id', Auth::user()->id)->findOrFail($id);
        if ($livestockBuy->status !== "MENUNGGU") {
            return response()->json([
                'success' => false,
                'message' => 'Status penawaran sudah pernah diupdate',
                'payload' => []
            ], 400);
        }

        $buyer = Farmer::findOrFail($livestockBuy->buyer_id);

        DB::beginTransaction();

        try {
            if ($request->status == 'DISETUJUI') {
                $perPrice = count($livestockBuy->items) == 0 ? 0 : ($livestockBuy->price / count($livestockBuy->items));

                foreach ($livestockBuy->items as $item) {
                    $livestock = $item->livestock;

                    if (!empty($livestock->sold_deal_price) || !empty($livestock->sold_year)) {
                        DB::rollback();

                        return response()->json([
                            'success' => false,
                            'message' => 'Ternak #' . $livestock->code . ' sudah terjual ke pembeli lain.',
                            'payload' => []
                        ]);
                    }

                    LivestockBuy::whereHas('items', function ($query) use ($item) {
                        $query->where('livestock_id', $item->livestock_id);
                    })
                        ->where('buyer_id', '!=', $livestockBuy->buyer_id)
                        ->where('seller_id', $livestockBuy->seller_id)
                        ->where('status', 'MENUNGGU')
                        ->update(['status' => 'DITOLAK']);

                    $item->livestock()->update([
                        'sold_deal_price' => $perPrice,
                        'sold_year' => date('Y'),
                        'sold_month' => date('m'),
                        'sold_month_name' => strtoupper(Carbon::now()->locale('id')->isoFormat('MMMM')),
                        'acquired_status' => 'JUAL',
                        'availability' => 'TIDAK TERSEDIA'
                    ]);

                    // copy livestock
                    $livestock = Livestock::create([
                        'kandang_id' => $livestockBuy->kandang_id,
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
                }
            }

            $buyer->notify(new NegotiateStatus($request, $livestockBuy->items, Auth::user()));

            $livestockBuy->update([
                'status' => $request->status,
                'status_updated_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'payload' => [
                    'error' => $th->getMessage()
                ]
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => []
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
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\Kandang;
use App\Models\Article;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = '';
        if (!isset($request->search)) {
            return response()->json([
                'succes' => true,
                'message' => 'Success',
                'payload' => ''
            ]);
        }
        $search = $request->search;

        // EVENT SELL
        $livestocks = Livestock::whereNotNull('sold_proposed_price')
            ->whereNull('sold_deal_price')
            ->whereNull('dead_year')
            ->orderBy('updated_at', 'desc')
            ->with(['kandang.farmer', 'livestockType'])
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('livestockType', function ($query) use ($search) {
                        $query->where('livestock_type', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('kandang', function ($query) use ($search) {
                        $query
                            ->whereHas('livestockType', function ($query) use ($search) {
                                $query->where('livestock_type', 'LIKE', '%' . $search . '%')->where('level', 1);
                            })
                            ->orWhereHas('farmer', function ($query) use ($search) {
                                $query->where('fullname', 'LIKE', '%' . $search . '%');
                            });
                    });
            })
            ->get();

        $resultLivestock = [];

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

            if (isset($resultLivestock[$currentResultKey])) {
                $resultLivestock[$currentResultKey]->price_total += $livestock->sold_proposed_price;
                $resultLivestock[$currentResultKey]->count_total++;

                $countSubTypeKey = array_search($livestock->livestockType->livestock_type, array_column($resultLivestock[$currentResultKey]->count_per_subtype, 'livestock_type'));
                if (isset($countSubTypeKey)) {
                    $resultLivestock[$currentResultKey]->count_per_subtype[$countSubTypeKey]['count'] += 1;
                } else {
                    $resultLivestock[$currentResultKey]->count_per_subtype[] = [
                        'livestock_type' => $livestock->livestockType->livestock_type,
                        'count' => 1
                    ];
                }

                if (isset($livestock->sold_image)) {
                    $resultLivestock[$currentResultKey]->livestock_images[] = $livestock->sold_image;
                }

                $resultLivestock[$currentResultKey]->items[] = $livestock;
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

                $resultLivestock[$currentResultKey] = (object) $sellGroup;
            }
        }

        $resultLivestock = array_values($resultLivestock);


        // REPORT PER KANDANG
        $kandangs = Kandang::select('kandang.id', 'kandang.name', 'kandang.type_id')
            ->where('farmer_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('livestockType', function ($query) use ($search) {
                        $query->where('livestock_type', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('livestocks', function ($query) use ($search) {
                        $query->whereHas('livestockType', function ($query) use ($search) {
                            $query->where('livestock_type', 'LIKE', '%' . $search . '%');
                        });
                    });
            })
            ->with('livestockType')
            ->with('livestocks', function ($query) {
                $query->select('id', 'dead_year', 'acquired_status', 'sold_proposed_price')
                    ->with('livestockType');
            })
            ->orderBy('type_id')
            ->get();

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
        }


        // SLIDERS 
        // brebes today
        $articles = Article::where('title', 'LIKE', '%' . $search . '%')
            ->with('articleCategory')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($articles as $keyArt => $article) {
            if (isset($article->thumbnail)) {
                $articles[$keyArt]->thumbnail = Storage::url('sliders/' . $article->thumbnail);
            }
        }


        $res = (object) [
            'event' => $resultLivestock,
            'kandang' => $kandangs,
            'sliders' => $articles,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }
}

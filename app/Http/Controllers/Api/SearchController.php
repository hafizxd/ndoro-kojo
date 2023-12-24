<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Livestock;
use App\Models\Kandang;
use App\Models\Article;

class SearchController extends Controller
{
    public function index(Request $request) {
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
            ->with(['livestockType', 'kandang.farmer'])
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('livestockType', function ($query) use ($search) {
                        $query->where('livestock_type', 'LIKE', '%'.$search.'%');
                    })
                    ->orWhereHas('kandang', function ($query) use ($search) {
                        $query
                            ->whereHas('livestockType', function ($query) use ($search) {
                                $query->where('livestock_type', 'LIKE', '%'.$search.'%')->where('level', 1);
                            })
                            ->orWhereHas('farmer', function ($query) use ($search) {
                                $query->where('fullname', 'LIKE', '%'.$search.'%');
                            });
                    });
            })
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


        // REPORT PER KANDANG
        $kandangs = Kandang::select('kandang.id', 'kandang.name', 'kandang.type_id')
            ->where('farmer_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhereHas('livestockType', function ($query) use ($search) {
                        $query->where('livestock_type', 'LIKE', '%'.$search.'%');
                    })
                    ->orWhereHas('livestocks', function ($query) use ($search) {
                        $query->whereHas('livestockType', function ($query) use ($search) {
                            $query->where('livestock_type', 'LIKE', '%'.$search.'%');
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
        $articleTodays = Article::select('id', 'title', 'thumbnail', 'created_at', 'updated_at')
            ->where('title', 'LIKE', '%'.$search.'%')
            ->where('type', 'TODAY')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($articleTodays as $key => $value) {
            if (isset($value->thumbnail)) {
                $articleTodays[$key]->thumbnail = Storage::url('sliders/' . $value->thumbnail);
            }
        }

        // finance digital
        $articleFinances = Article::select('id', 'title', 'thumbnail', 'created_at', 'updated_at')
            ->where('title', 'LIKE', '%'.$search.'%')
            ->where('type', 'FINANCE')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($articleFinances as $key => $value) {
            if (isset($value->thumbnail)) {
                $articleFinances[$key]->thumbnail = Storage::url('sliders/' . $value->thumbnail);
            }
        }

        $res = (object) [
            'event' => $livestocks,
            'kandang' => $kandangs,
            'today' => $articleTodays,
            'finance' => $articleFinances
        ];

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $res
        ]);
    }
}

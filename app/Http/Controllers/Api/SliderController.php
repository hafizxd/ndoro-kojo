<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index($type)
    {
        $articleCategory = ArticleCategory::where("slug", $type)->firstOrFail();

        $articles = $articleCategory->articles()
            ->select('id', 'title', 'thumbnail', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($articles as $key => $value) {
            if (isset($value->thumbnail)) {
                $articles[$key]->thumbnail = Storage::url('sliders/' . $value->thumbnail);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $articles
        ]);
    }

    public function show($type, $id)
    {
        $articleCategory = ArticleCategory::where("slug", $type)->firstOrFail();

        $article = $articleCategory->articles()->findOrFail($id);

        if (isset($article->thumbnail)) {
            $article->thumbnail = Storage::url('sliders/' . $article->thumbnail);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => $article
        ]);
    }
}

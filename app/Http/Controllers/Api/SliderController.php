<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index($type) {
        if (!in_array(strtoupper($type), ['EVENT', 'TODAY', 'FINANCE']))
            abort(404);

        $articles = Article::select('id', 'title', 'thumbnail', 'created_at', 'updated_at')->where('type', $type)->orderBy('created_at', 'desc')->get();

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

    public function show($type, $id) {
        if (!in_array(strtoupper($type), ['EVENT', 'TODAY', 'FINANCE']))
            abort(404);

        $article = Article::where('type', $type)->findOrFail($id);

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

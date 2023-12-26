<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ArticleCategory;

class ArticleCategoryController extends Controller
{
    public function index(Request $request) {
        $categories = ArticleCategory::all();

        return view('sliders.categories.index', compact('categories'));
    }

    public function store(Request $request) {
        ArticleCategory::create([
            'title' => $request->title,
            'slug' => strtolower(Str::slug($request->title)),
        ]);

        return redirect()->back();
    }

    public function update($id, Request $request) {
        ArticleCategory::findOrFail($id)->update([
            'title' => $request->title,
            'slug' => strtolower(Str::slug($request->title)),
        ]);

        return redirect()->back();
    }

    public function delete($id, Request $request) {
        ArticleCategory::findOrFail($id)->delete();

        return redirect()->back();
    }
}

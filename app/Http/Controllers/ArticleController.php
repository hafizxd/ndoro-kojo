<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Article;
use DataTables;

class ArticleController extends Controller
{
    public function indexToday(Request $request) {
        if ($request->ajax()) {
            $data = Article::select('*')->where('type', 'TODAY');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('thumbnail', function($row){
                    $str = '<img style="max-width: 100px;" src="'.asset('storage/sliders/'.$row->thumbnail).'" alt="thumbnail" />';
                    return $str;
                })
                ->addColumn('content', function($row){
                    $str = html_entity_decode($row->content);
                    return $str;
                })
                ->addColumn('action', function($row){
                    $action = '
                        <script type="text/javascript">
                            var rowData_' . md5($row->id) . ' = {
                                "id" : "' . $row->id . '",
                                "title" : "' . $row->title . '",
                                "content" : "' . preg_replace("/\r\n|\r|\n/", '<br>', $row->content) . '"
                            };
                        </script>
                    ';

                    $action .= '
                        <a href="javascript:editData(rowData_'.md5($row->id).')" class="edit btn btn-success btn-sm">Edit</a> 
                        <a href="javascript:deleteData('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</a>
                    ';
                    return $action;
                })
                ->rawColumns(['thumbnail', 'content', 'action'])
                ->make(true);
        }

        return view('sliders.today');
    }

    public function indexFinance(Request $request) {
        if ($request->ajax()) {
            $data = Article::select('*')->where('type', 'FINANCE');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('thumbnail', function($row){
                    $str = '<img style="max-width: 100px;" src="'.asset('storage/sliders/'.$row->thumbnail).'" alt="thumbnail" />';
                    return $str;
                })
                ->addColumn('content', function($row){
                    $str = html_entity_decode($row->content);
                    return $str;
                })
                ->addColumn('action', function($row){
                    $action = '
                        <script type="text/javascript">
                            var rowData_' . md5($row->id) . ' = {
                                "id" : "' . $row->id . '",
                                "title" : "' . $row->title . '",
                                "content" : "' . preg_replace("/\r\n|\r|\n/", '<br>', $row->content) . '"
                            };
                        </script>
                    ';

                    $action .= '
                        <a href="javascript:editData(rowData_'.md5($row->id).')" class="edit btn btn-success btn-sm">Edit</a> 
                        <a href="javascript:deleteData('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</a>
                    ';
                    return $action;
                })
                ->rawColumns(['thumbnail', 'content', 'action'])
                ->make(true);
        }

        return view('sliders.finance');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:EVENT,TODAY,FINANCE',
            'title' => 'required',
            'thumbnail' => 'required|image',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $fileName = time() . '_' . $request->thumbnail->getClientOriginalName();
        $filePath = $request->thumbnail->storeAs('sliders', $fileName, 'public');

        Article::create([
            'type' => strtoupper($request->type),
            'title' => $request->title,
            'thumbnail' => $fileName,
            'content' => $request->content,
            'author_id' => Auth::guard('web')->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil input data',
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:articles',
            'type' => 'required|in:EVENT,TODAY,FINANCE',
            'title' => 'required',
            'thumbnail' => 'nullable',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $article = Article::where('type', $request->type)->findOrFail($request->id);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content
        ];

        if (isset($request->thumbnail)) {
            $fileName = time() . '_' . $request->thumbnail->getClientOriginalName();
            $filePath = $request->thumbnail->storeAs('sliders', $fileName, 'public');

            $updateData['thumbnail'] = $fileName;
        }

        $article->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil update data',
        ]);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:articles',
            'type' => 'required|in:EVENT,TODAY,FINANCE'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $article = Article::where('type', $request->type)->findOrFail($request->id);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil delete data',
        ]);
    }
}

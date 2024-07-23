<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Constants\UserRole;
use App\Models\User;
use DataTables;

class OperatorController extends Controller
{
    public function index(Request $request) {
        $provinces = Province::orderBy('name')->get();

        if ($request->ajax()) {
            $data = User::select('*')
                ->where('role', UserRole::OPERATOR)
                ->with('district.regencies.province');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function($row) {
                    return $row->name;
                })
                ->addColumn('username', function($row) {
                    return $row->username;
                })
                ->addColumn('province', function($row) {
                    return $row->district?->regencies?->province?->name;
                })
                ->addColumn('regency', function($row) {
                    return $row->district?->regencies?->name;
                })
                ->addColumn('district', function($row) {
                    return $row->district?->name;
                })
                ->addColumn('action', function($row){
                    $action = '-';
                    
                    $action = '
                        <script type="text/javascript">
                            var rowData_' . md5($row->id) . ' = {
                                "id" : "' . $row->id . '",
                                "name" : "' . $row->name . '",
                                "username" : "' . $row->username . '",
                                "province_id" : "' . $row->district?->regencies?->province?->id . '",
                                "regency_id" : "' . $row->district?->regencies?->id . '",
                                "district_id" : "' . $row->district?->id . '",
                            };
                        </script>
                    ';
                
                    // <a href="javascript:editData(rowData_'.md5($row->id).')" class="edit btn btn-success btn-sm">Edit</a> 
                    $action .= '
                        <a href="javascript:deleteData('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</a>
                    ';
                    
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('operators.index', compact('provinces'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'district_id' => 'required|exists:districts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'district_id' => $request->district_id,
            'role' => UserRole::OPERATOR
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil input data',
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users',
            'name' => 'required',
            'username' => [
                'required',
                Rule::unique('users')->ignore($request->id)
            ],
            'password' => 'nullable',
            'district_id' => 'required|exists:districts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $user = User::findOrFail($request->id);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'district_id' => $request->district_id
        ];

        if (isset($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil update data',
        ]);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $user = User::findOrFail($request->id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus data',
        ]);
    }
}

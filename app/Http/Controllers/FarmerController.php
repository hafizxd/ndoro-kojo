<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Farmer;
use DataTables;

class FarmerController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Farmer::select('*')
                ->with(['province', 'regency', 'district', 'village']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function($row) {
                    return $row->district?->name;
                })
                ->addColumn('village', function($row) {
                    return $row->village?->name;
                })
                ->addColumn('action', function($row){
                    $action = '-';
                    
                    if (Auth::guard('web')->check()) {
                        $action = '
                            <script type="text/javascript">
                                var rowData_' . md5($row->id) . ' = {
                                    "id" : "' . $row->id . '",
                                    "fullname" : "' . $row->fullname . '",
                                    "username" : "' . $row->username . '",
                                    "email" : "' . $row->email . '",
                                    "phone" : "' . $row->phone . '",
                                    "address" : "' . $row->address . '",
                                    "occupation" : "' . $row->occupation . '",
                                    "gender" : "' . $row->gender . '",
                                    "age" : "' . $row->age . '",
                                    "kelompok_ternak" : "' . $row->kelompok_ternak . '",
                                    "province_id" : "' . $row->province_id . '",
                                    "province_name" : "' . $row->province?->name . '",
                                    "regency_id" : "' . $row->regency_id . '",
                                    "regency_name" : "' . $row->regency?->name . '",
                                    "district_id" : "' . $row->district_id . '",
                                    "district_name" : "' . $row->district?->name . '",
                                    "village_id" : "' . $row->village_id . '",
                                    "village_name" : "' . $row->village?->name . '",
                                };
                            </script>
                        ';
                    
                        $action .= '
                            <a href="javascript:editData(rowData_'.md5($row->id).')" class="edit btn btn-success btn-sm">Edit</a> 
                            <a href="javascript:deleteData('.$row->id.')" class="delete btn btn-danger btn-sm">Delete</a>
                        ';
                    } 
                    
                    return $action;
                })
                ->rawColumns(['district', 'village', 'action'])
                ->make(true);
        }

        return view('farmers.index');
    }

    function generateRandomCode($prefix, $table, $column) {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);
    
        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data)) 
            return generateRandomCode($prefix, $table, $column);
    
        return $rand;
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'username' => 'required|unique:farmers',
            'email' => 'required|unique:farmers',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        Farmer::create([
            'code' => $this->generateRandomCode('USR', 'farmers', 'code'),
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'occupation' => $request->occupation,
            'gender' => $request->gender,
            'age' => $request->age,
            'kelompok_ternak' => $request->kelompok_ternak
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil input data',
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:farmers',
            'fullname' => 'required',
            'username' => [
                'required',
                Rule::unique('farmers')->ignore($request->id)
            ],
            'email' => [
                'required',
                Rule::unique('farmers')->ignore($request->id)
            ],
            'password' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $farmer = Farmer::findOrFail($request->id);

        $updateData = [
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'occupation' => $request->occupation,
            'gender' => $request->gender,
            'age' => $request->age,
            'kelompok_ternak' => $request->kelompok_ternak
        ];

        if (isset($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $farmer->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil update data',
        ]);
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:farmers'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode('<br>', $validator->errors()->all())
            ]);
        }

        $farmer = Farmer::findOrFail($request->id);
        $farmer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus data',
        ]);
    }
}

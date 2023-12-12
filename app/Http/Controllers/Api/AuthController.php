<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Transformers\FarmerCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Farmer;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('username', 'password');
        $token = Auth::attempt($credentials);
        
        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:farmers',
            'email' => 'required|string|email|max:255|unique:farmers',
            'password' => 'required|string|min:6'
        ]);

        $farmer = Farmer::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'fullname' => $request->fullname,
            'email' => $request->email,
            'code' => $this->generateRandomCode('USR', 'farmers', 'code')
        ]);

        $token = auth()->login($farmer);

        return $this->respondWithToken($token);
    }

    function generateRandomCode($prefix, $table, $column) {
        $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);
    
        $data = DB::table($table)->select('id')->where($column, $rand)->first();
        if (isset($data)) 
            return generateRandomCode($prefix, $table, $column);
    
        return $rand;
    }

    public function logout()
    {
        Auth::logout();
        
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'farmer' => new FarmerCollection(auth()->user()),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => new FarmerCollection(Auth::user())
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('farmers')->ignore(Auth::user()->id)
            ],  
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('farmers')->ignore(Auth::user()->id)
            ],
            'occupation' => [
                'nullable',
                Rule::in(['PETERNAK', 'PEDAGANG TERNAK'])   
            ],
            'gender' => [
                'nullable',
                Rule::in(['LAKI-LAKI', 'PEREMPUAN'])
            ],
            'age' => 'nullable|numeric',
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

        Auth::user()->update([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'occupation' => $request->occupation,
            'gender' => $request->gender,
            'age' => $request->age,
            'kelompok_ternak' => $request->kelompok_ternak,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'payload' => new FarmerCollection(Auth::user())
        ]);
    }
}

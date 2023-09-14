<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->messages()
            ],403);
        }
        $checkEmail = User::where(['email' => $request->email])->first();
        if ($checkEmail) {
            if (Hash::check($request->password, $checkEmail->password)) {
                $success['token'] =  $checkEmail->createToken('MyAuthApp')->plainTextToken;
                $success['name'] =  $checkEmail->name;
                return response()->json([
                    'type' => 'success',
                    'msg' => 'Login successfully',
                    'data'=>$success
                ],200);
            }
        }
        return response()->json([
            'type' => 'error',
            'msg' => 'The provided credentials do not match our records'
        ],401);
    }
}

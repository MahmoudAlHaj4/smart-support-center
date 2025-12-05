<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Mime\Message;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password'=> 'required|string|min:8|confirmed',
            'role' => 'customer',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
            'role'=> 'customer'
        ]);
        if (!$user) {
            return response()->json(['error' => 'User creation failed'], 500);
        }
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message'=>'User registered successfully',
            'user' => $user,
            'token'=>$token
        ], 201);
        }catch (\Exception $e) {
        return response()->json([
            'message' => 'Registration failed',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function login (Request $request) 
    {
        try{
            $validate = Validator::make($request->all(),[
                'email' =>'required|string|email',
                'password'=>'required|string'
            ]);
            if($validate->fails()){
                return response()->json($validate->errors(),422);
            }
            $user = User::where('email', $request->email)->first();

            if(!$user){
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }

            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'message'=> 'Invalid Credentials',
                ]);

            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message'=>'Login success',
                'user' =>$user,
                'token'=>$token
            ]);
        }catch (\Exception $e) {
        return response()->json([
            'message' => 'Login failed',
            'error' => $e->getMessage()
        ], 500);
    }
    }
}

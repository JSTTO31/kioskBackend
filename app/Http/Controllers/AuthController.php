<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $utils = new Utilities();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('example-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(LoginRequest $request){

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return [
                'message' => 'Sorry, the credentials is incorrect!',
            ];
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('example-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response(null, 204);
    }
}

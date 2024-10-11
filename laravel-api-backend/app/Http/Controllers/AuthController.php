<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($fields);

        // 2|yrUGgbeulfYK8m6F0wiUZ73he8NAL7X5xFRmk6Fifb0c71d2 -- register token
        // 3|XIzPP5Tn8iaTmUQB1so3I2740CidxYnbYRH8osL94ebc0981
        // 4|1rI3mhwQxj59D4kHfH7DWSCkNok7hxgAnoaSMBo906ead848

        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];

        // $plainTextToken = $token->plainTextToken;
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['message' => 'The provided creadentials are incorrect.'];
        }

        $token = $user->createToken($user->name);
        
        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out.'
        ];
    }
}

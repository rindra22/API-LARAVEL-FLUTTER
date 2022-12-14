<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Register User
    public function register(){

        // validate request
        $validated = request()->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // create token
        $token = $user->createToken('secret')->plainTextToken;

        // response
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // Login User

    public function login(){

        // validate request
        $validated = request()->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to login
        if(!auth()->attempt($validated)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 403);
        }

        // create token
        $token = auth()->user()->createToken('secret')->plainTextToken;

        // response
        return response()->json([
            'user' => auth()->user(),
            'token' => $token
        ], 200);
    }

    // Logout User

    public function logout(){

        // Revoke token
        auth()->user()->tokens()->delete();

        // response
        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }

    // Get User

    public function user(){

        // response
        return response()->json([
            'user' => auth()->user()
        ], 200);
    }

    // Update profile

    public function update(Request $request){

        $validated = $request->validate([
            'name' => 'required'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $validated['name'],
            'image' => $image
        ]);

        return response()->json([
            'message' => 'User updated',
            'user' => auth()->user()
        ], 200);
    }

}

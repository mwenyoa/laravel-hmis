<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;

class AuthController extends Controller
{
   use HttpResponses;

// Login User 
   public function login(UserLoginRequest $request){
    $request->validated($request->all());
    if(!Auth::attempt($request->only(['email', 'password']))){
        return $this->error('', 'Invalid login credentials', 401);
    }
    $user = User::where('email', $request->email)->first();

    return $this->success([
        'user' => $user,
        'token' => $user->createToken('Api Token of '.$user->name)->plainTextToken
    ], 'Logged In Successfully');

}

// Register New User
   public function register(StoreUserRequest $request){
    $request->validated($request->all());

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'gender' => $request->gender,
        'marital_status' => $request->marital_status,
        'password' => Hash::make($request->password)
    ]);
    
    return $this->success([
        'user' => $user,
        'token' => $user->createToken('API token of '. $user->name)->plainTextToken
    ],'User registered successfully', 200);
   }

   protected function logout(){
    
    return response()->json([

    ]);
   }
}

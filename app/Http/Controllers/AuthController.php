<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersResource;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;

class AuthController extends Controller
{
    use HttpResponses;

    // Login User
    public function login(UserLoginRequest $request)
    {

        $validatedData = $request->validated();
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Invalid login credentials', 401);
        }
        if(auth()->id() === Auth::user()){}
        $user = Auth::user();
        $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;
        $userResource = UsersResource::make($user);
        // check if user is already logged in
        if (auth()->check()) {
            return redirect()->route(ENV('APP_FRONTEND_URL'))->with([
                'user' => $userResource,
                'token' => $token,
                'message' => 'You are already logged in'
            ]);
        }

        return $this->success([
            'user' => $userResource,
            'token' => $token,
        ], 'Logged In Successfully', 200);
    }

    // Register New User
    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([

            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phoneno' => $request->phoneno,
            'photo_url' => $request->photo_url,
            'email' => $request->email,
            'gender' => $request->gender,
            'age' => $request->age,
            'marital_status' => $request->marital_status,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of ' . $user->name)->plainTextToken,
        ], 'User registered successfully', 201);
    }

    protected function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success('', "Logged Out Successfully!", 204);
    }
}

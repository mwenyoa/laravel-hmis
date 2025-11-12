<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UsersResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use HttpResponses;

    // Login User
    public function login(UserLoginRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if (! Auth::attempt($request->only(['email', 'password']))) {
                return $this->error('Invalid login credentials', 401);
            }

            $user = Auth::user();

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                return $this->error('Please verify your email address before login in.', 403);
            }

            $token = $user->createToken('Api Token of '.$user->name)->plainTextToken;
            $userResource = UsersResource::make($user);
            //  \Log::info('About to fire user resource');
            //  \Log::info($userResource->toArray(request()));
            return $this->success([
                'user' => $userResource,
                'token' => $token,
            ], 'Logged In Successfully');

        } catch (ValidationException $e) {
            // Validation errors are already handled by the FormRequest
            throw $e; // Let the Exception Handler process it
        } catch (Exception $e) {
            // Let the Exception Handler handle all other errors
            throw $e;
        }
    }

    // Register New User
  public function register(StoreUserRequest $request)
{
    try {
        // This will automatically throw ValidationException if validation fails
        $validatedData = $request->validated();
        
        $avatar = '';

        if ($request->hasFile('photo_url')) {
            $avatar = Storage::disk('public')->put('/avatars', $request->file('photo_url'));
        }
    
        $user = User::create([  
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'phoneno' => $validatedData['phoneno'],
            'photo_url' => $avatar,
            'email' => $validatedData['email'],
            'gender' => $validatedData['gender'],
            'age' => $validatedData['age'],
            'marital_status' => $validatedData['marital_status'],
            'password' => Hash::make($validatedData['password']),
        ]);

        \Log::info('User created successfully: '.$user->email);

        // Fire the registered event to trigger email verification
        \Log::info('About to fire Registered event');
        event(new Registered($user));
        \Log::info('Registered event fired successfully');

        $token = $user->createToken('API token of '.$user->name)->plainTextToken;

        $userResource = new UsersResource($user);

        return $this->success([
            'user' => $userResource,
            'token' => $token,
            'message' => 'User registered successfully. Please check your email for account verification.',
        ], 'User registered successfully', 201);

    } catch (ValidationException $e) {
        // Return formatted validation errors using our trait
        return $this->validationError($e->errors(), $e->getMessage());
    } catch (QueryException $e) {
        // Database errors
        \Log::error('Database error during registration: '.$e->getMessage());
        
        return $this->error('Database error occurred during registration.', 500);
    } catch (Exception $e) {
        \Log::error('Registration error: '.$e->getMessage());

        // If user was created but something else failed
        if (isset($user) && $user->exists) {
            try {
                $token = $user->createToken('API token of '.$user->name)->plainTextToken;

                return $this->success([
                    'user' => new UsersResource($user),
                    'token' => $token,
                    'warning' => 'Registration completed, but email verification may be delayed.',
                ], 'User registered with warning', 201);
            } catch (Exception $tokenError) {
                \Log::error('Token creation error: '.$tokenError->getMessage());

                // If token creation fails, still return success but without token
                return $this->success([
                    'user' => new UsersResource($user),
                    'warning' => 'Registration completed. Please login to verify your account.',
                ], 'User registered successfully', 201);
            }
        }

        return $this->error('An unexpected error occurred during registration.', 500);
    }
}

    // Logout User
    public function logout()
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            return $this->success([], 'Logged Out Successfully!', 200);
        } catch (Exception $e) {
            // Let the Exception Handler handle the error
            throw $e;
        }
    }

    // Reset Password - Fixed method signature and implementation
    public function resetPassword(PasswordResetRequest $request)
    {
        try {
            $request->validated();

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return $this->error('User not found', 404);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Optionally, invalidate all existing tokens
            $user->tokens()->delete();

            return $this->success([], 'Password reset successfully', 200);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Verify Email
    public function verifyEmail($id, $hash)
    {
        try {
            $user = User::findOrFail($id);

            if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
                return $this->error('Invalid verification link', 400);
            }

            if ($user->hasVerifiedEmail()) {
                return $this->success([], 'Email already verified');
            }

            $user->markEmailAsVerified();

            return $this->success([], 'Email verified successfully');

        } catch (Exception $e) {
            throw $e;
        }
    }

    // Resend Verification Email
    public function resendVerificationEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user->hasVerifiedEmail()) {
                return $this->error('Email already verified', 400);
            }

            $user->sendEmailVerificationNotification();

            return $this->success([], 'Verification email sent successfully');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Get Current User
    public function user(Request $request)
    {
        try {
            $userResource = new UsersResource($request->user());

            return $this->success([
                'user' => $userResource,
            ], 'User retrieved successfully');

        } catch (Exception $e) {
            throw $e;
        }
    }
}

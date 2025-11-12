<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\Auth\EmailVerificationController; // Import the custom controller
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\SpecialtiesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (No authentication required)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// password routes
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/password/change', [PasswordController::class, 'change']);
    Route::post('/password/validate-current', [PasswordController::class, 'validateCurrent']);
});

Route::post('/password/forgot', [PasswordController::class, 'forgot']);
Route::post('/password/reset', [PasswordController::class, 'reset']);

// EMAIL ROUTES

// Email verification endpoint (Public, protected by the 'signed' middleware)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Protected Routes (Authentication required)
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::delete('/logout', [AuthController::class, 'logout']);

    // Route for users to check their verification status and get the resend URL
    Route::get('/email/verify-status', function (Request $request) {
        return response()->json([
            'message' => $request->user()->hasVerifiedEmail() ? 'Email already verified' : 'Email verification required',
            'verified' => $request->user()->hasVerifiedEmail(),
            'resend_url' => '/api/email/verification-notification'
        ]);
    })->name('verification.status');

    // Resend verification email
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent to your email']);
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Protected API resourceful routes
    Route::apiResources([
        "/doctors" => DoctorsController::class,
        "/patients" => PatientsController::class,
        "/appointments" => AppointmentsController::class,
        "/feedbacks" => FeedbacksController::class,
        "/users" => UsersController::class,
        '/specialties' => SpecialtiesController::class,
    ]);
});

// Route to handle expired verification links (Public)
Route::post('/email/verification/expired', [EmailVerificationController::class, 'handleExpiredLink'])
    ->name('verification.expired');

// Any other routes  protected by the `verified` middleware

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    // Routes only accessible to authenticated and verified users
    Route::get('/verified-only', function () {
        return response()->json(['message' => 'You have access!']);
    });
});


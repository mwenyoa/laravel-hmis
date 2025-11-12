<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class PasswordController extends Controller
{
    use HttpResponses;

    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Password change validation failed.');
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Optional: Invalidate other sessions for security
        // auth()->logoutOtherDevices($request->current_password);

        return $this->success(
            null,
            'Password changed successfully',
            'For security reasons, you may want to log out of other devices.'
        );
    }

    public function validateCurrent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Password validation failed.');
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        return $this->success(
            ['valid' => true],
            'Current password is correct'
        );
    }

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Email validation failed.');
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(
                null,
                __($status),
                'If you do not receive the email within a few minutes, check your spam folder.'
            );
        }

        return $this->error(__($status), 422);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Password reset validation failed.');
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(
                null,
                __($status),
                'You can now log in with your new password.'
            );
        }

        return $this->error(__($status), 422);
    }
}

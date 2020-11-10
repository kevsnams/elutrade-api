<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use RandomLib\Factory;

class SignupController extends Controller
{
    public function email(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'unique:users,email', 'email', 'max:255'
            ],

            'password' => [
                'required'
            ],

            'password_confirm' => [
                'required', 'same:password'
            ],

            'first_name' => [
                'required', 'string', 'max:255'
            ],

            'middle_name' => [
                'sometimes', 'nullable', 'required', 'string', 'max:255'
            ],

            'last_name' => [
                'required', 'string', 'max:255'
            ]
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->first_name = $request->first_name;
        $user->middle_name = $request->input('middle_name', null);
        $user->last_name = $request->last_name;
        $user->email_verified_at = config('mail.enableSignupVerification') ? null : now();
        $user->save();

        if (config('mail.enableSignupVerification')) {
            $randomLibFactory = new Factory();
            $generator = $randomLibFactory->getMediumStrengthGenerator();
            $verificationCode = $generator->generateString(6, '0123456789');

            $user->email_verification_code = $verificationCode;
            $user->save();

            Mail::to($user)->send(new EmailVerification($user, $verificationCode));
        }

        return ['success' => true];
    }

    // TODO add to docs
    public function emailVerify(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'string', 'email', 'exists:App\Models\User,email'
            ],

            'code' => [
                'required', 'string', 'numeric', 'size:6'
            ]
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => ['Verification code is incorrect']
            ]);
        }

        $user->markEmailAsVerified();

        return [
            'success' => true
        ];
    }

    public function emailVerificationResend(Request $request)
    {
        $request->validate([
            'email' => [
                'required', 'string', 'email', 'exists:App\Models\User,email'
            ]
        ]);

        $user = User::where('email', $request->email)->first();

        $randomLibFactory = new Factory();
        $generator = $randomLibFactory->getMediumStrengthGenerator();
        $verificationCode = $generator->generateString(6, '0123456789');

        $user->email_verification_code = $verificationCode;
        $user->save();

        Mail::to($user)->send(new EmailVerification($user, $verificationCode));

        return [
            'success' => true
        ];
    }


    public function facebook(Request $request)
    {

    }

    public function google(Request $request)
    {

    }
}

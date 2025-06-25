<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): View
    {
        if ($request->user()->hasVerifiedEmail()) {
            //return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
            return view('auth.verification-success');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        //return redirect()->intended(route('dashboard', absolute: false).'?verified=1');

        return view('auth.verification-success');
    }
}

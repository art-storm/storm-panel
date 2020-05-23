<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if (auth()->check() && $user->two_factor_state && $user->two_factor_code) {
            if ($user->two_factor_expires_at->lt(now())) {
                $user->resetTwoFactorCode();
                auth()->logout();
                return redirect()->route('login')->with('status', __('auth.two_factor_code_expired'));
            }
            if (!$request->is('2fa*')) {
                return redirect()->route('2fa.form');
            }
        }

        return $next($request);
    }
}

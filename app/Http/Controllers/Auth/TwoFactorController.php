<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\Auth\TwoFactorCodeEmail;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'twofactor']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        return view('auth.two_factor');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function check(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|integer',
        ]);

        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();
            return redirect()->route('users.profile');
        }

        return redirect()->back()
            ->withErrors(['two_factor_code' => __('auth.two_factor_code_mismatch')]);
    }

    /**
     * @return mixed
     */
    public function resend()
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCodeEmail());
        return redirect()->back()->withMessage(__('auth.two_factor_code_resend'));
    }
}

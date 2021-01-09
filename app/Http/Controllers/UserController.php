<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcomePage()
    {
        return view('users.welcome');
    }

    /**
     * Show the user profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', ['user' => $user]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function profileUpdate(Request $request)
    {
        $twoFactorMethods = config('auth.two_factor_methods');

        $this->validate($request, [
            'name' => 'required|string|max:30',
            'two_factor_method' => [
                'nullable',
                Rule::in($twoFactorMethods),
            ],
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        if ($request->two_factor_state && $request->two_factor_method) {
            $user->two_factor_state = true;
            $user->two_factor_method = $request->two_factor_method;
        } else {
            $user->two_factor_state = false;
            $user->two_factor_method = null;
            $user->two_factor_code = null;
        }

        // See UserObserver updating event for details
        $user->save();

        $flash_status = __('profile.success.change');
        return redirect()->route('users.profile')->with('status', $flash_status);
    }
}

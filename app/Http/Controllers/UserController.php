<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:30',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        $flash_success = __('profile.success.change');
        return redirect()->route('users_profile')->with('success', $flash_success);
    }
}

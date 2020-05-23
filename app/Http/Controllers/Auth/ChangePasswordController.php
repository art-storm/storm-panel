<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Change Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password change requests.
    |
    */

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
    public function passwordChangeForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * @param UserPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function passwordChange(Request $request)
    {
        $this->validator($request->all())->validate();

        $flash_success = '';
        $user = Auth::user();
        $user->password = bcrypt($request->password);

        if ($user->save()) {
            $flash_success = __('profile.success.change_password');
        }

        return redirect()->route('users.profile')->with('success', $flash_success);
    }

    /**
     * Get a validator for an incoming password change request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'password_current' => ['required', 'password_current'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);
    }
}

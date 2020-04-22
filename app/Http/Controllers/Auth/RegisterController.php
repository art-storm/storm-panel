<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\Auth\EmailConfirm;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after activate registration.
     *
     * @var string
     */
    protected $redirectToWelcome = '/users/welcome';

    /**
     * Days, after users will be deleted if not activate
     *
     * @var int
     */
    protected $activateDays = 3;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'activation_code' => Str::random(30) . time(),
        ]);
    }

    /**
     * User registration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function register(Request $request)
    {
        $this->validator($request->all())->validate();

        try {
            $user = $this->create($request->all());
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('message', __('registration.unable_create_user'));
        }
        $user->notify(new EmailConfirm($user));
        return redirect(route('register_verify'))->with('success', 'register_verify');
    }

    /**
     * Register verify page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function registerVerify()
    {
        if (session()->has('success')) {
            return view('auth.register_verify');
        } else {
            abort(404);
        }
    }

    /**
     * Activate the user with given activation code.
     * @param string $activationCode
     * @return string
     */
    public function activateUser(string $activationCode)
    {
        $user = User::where('activation_code', $activationCode)->first();
        if (!$user) {
            return abort(404);
        }

        $user->is_activate = 1;
        $user->activation_code = null;
        $user->email_verified_at = Carbon::now()->toDateTimeString();

        try {
            $user->save();
        } catch (\Exception $exception) {
            logger()->error($exception);
            return abort(500);
        }

        $this->guard()->login($user);
        $user->updated_by = $user->id;
        $user->save();

        return redirect($this->redirectToWelcome);
    }

    /**
     * Delete non activated users
     */
    public function deleteNonActivated()
    {
        $users = User::where('created_at', '<', Carbon::now()->subDays($this->activateDays))
            ->where('is_activate', '=', 0)
            ->delete();

        echo 'Delete ' . $users . ' non activated users';
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailChanges;
use App\Notifications\EmailChange;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChangeEmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Change Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email change requests.
    |
    */

    /**
     * Hours, after email changes queries will be deleted
     *
     * @var int
     */
    protected $emailChangeQueriesHours = 24;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('confirmEmailChange');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function emailChangeForm()
    {
        return view('users.email_change');
    }

    /**
     * @param UserChangeEmailRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sendEmailChangeLink(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = Auth::user();

        $token = Str::random(30) . time();

        $emailChangesParameters = array(
            'email' => $user->email,
            'change_code' => $token,
            'email_new' => $request->email,
        );

        $emailChanges = new EmailChanges();
        $emailChanges->where('email', '=', $user->email)->delete();
        $emailChanges->create($emailChangesParameters);

        $user->email = $request->email;
        $user->notify(new EmailChange($token));

        return redirect(route('email_change_notify'))->with('success', $request->email);
    }

    /**
     * Get a validator for an incoming password change request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $users = new User();
        $tableUsers = $users->getTable();

        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . $tableUsers],
            'password' => ['required', 'password_current'],
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notifyEmailChangeLink()
    {
        if (session()->has('success')) {
            $email_new = session('success');
            return view('users.email_change_notify', ['email_new' => $email_new]);
        } else {
            abort(404);
        }
    }

    /**
     * Email change on confirmation link
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmEmailChange(Request $request)
    {
        $flash_success = '';
        $emailChanges = EmailChanges::where('change_code', '=', $request->token)->firstOrFail();

        $user = User::where('email', '=', $emailChanges->email)->firstOrFail();

        $user->email = $emailChanges->email_new;

        if ($user->save()) {
            EmailChanges::where('email', '=', $emailChanges->email)->delete();

            $flash_success = __('profile.success.change_email');
        }

        if (!Auth::check()) {
            return redirect(route('login'))->with('success', $flash_success);
        }

        return redirect(route('users_profile'))->with('success', $flash_success);
    }

    /**
     * Delete old email change queries
     */
    public function deleteEmailChangeQuery()
    {
        $queries = EmailChanges::where('created_at', '<', Carbon::now()->subHour($this->emailChangeQueriesHours))
            ->delete();

        echo 'Delete ' . $queries . ' query(s) for email change';
    }

    /**
     * Get the guard to be used during email change.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}

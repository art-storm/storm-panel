<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/users/profile';

    /**
     * Hours, after passwords reset queries will be deleted
     *
     * @var int
     */
    protected $resetQueriesHours = 3;

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
     * Delete old reset password query
     */
    public function deletePasswordResetQuery()
    {
        $queries = DB::table('password_resets')
            ->where('created_at', '<', Carbon::now()->subHour($this->resetQueriesHours))
            ->delete();

        echo 'Delete ' . $queries . ' query(s) for password reset';
    }
}

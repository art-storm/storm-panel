<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;

class AdminController extends Controller
{
    /**
     * Where to redirect admin after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * Show admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardAdmin()
    {
        $users_count = User::where('is_activate', 1)->count();
        $roles_count = Role::count();

        return view('admin.dashboard', [
            'users_count' => $users_count,
            'roles_count' => $roles_count,
        ]);
    }
}

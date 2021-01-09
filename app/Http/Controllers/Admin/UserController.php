<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserAddRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Jobs\Admin\CreateUserAddition;
use App\Models\Role;
use App\Models\UserRole;
use App\Notifications\Admin\UserAdd;
use App\Traits\AdminCommon;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AdminCommon;

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
     * Show user list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($this->user()->cant('viewAny', User::class)) {
            return $this->adminNoAccess();
        }

        $sortArray = ['email' => 'Email', 'name' => 'Name'];

        $emailSearch = preg_replace('/\*/', '%', $request->email);

        $sortBy = (!$request->sort_by || !array_key_exists($request->sort_by, $sortArray))
            ? 'email'
            : $request->sort_by;

        $roles = Role::orderBy('role_display', 'asc')->get();

        $data = [
            'sorting' => $sortArray,
            'roles' => $roles,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'sort_by' => $request->sort_by,
        ];

        $users = $this->getUserListFilter([
            'emailSearch' => $emailSearch,
            'roleSearch' => $request->role_id,
            'sortBy' => $sortBy,
        ]);

        return view('admin.users', ['users' => $users, 'data' => $data]);
    }

    /**
     * Show user
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if ($this->user()->cant('viewAny', User::class)) {
            return $this->adminNoAccess();
        }

        $user_id = (int) $request->user_id;
        $user = User::with('role', 'additionalRoles')->findOrFail($user_id);
        return view('admin.users_show', ['user' => $user]);
    }

    /**
     * Create user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        if ($this->user()->cant('create', User::class)) {
            return $this->adminNoAccess();
        }

        $roles = Role::orderBy('role_display', 'asc')->get();
        return view('admin.users_create', ['roles' => $roles]);
    }

    /**
     * Store user
     *
     * @param UserAddRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(UserAddRequest $request)
    {
        if ($this->user()->cant('create', User::class)) {
            return $this->adminNoAccess();
        }

        try {
            // See UserObserver creating events for details
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_activate' => 1,
                'role_id' => $request->role_id,
            ]);

            $rolesAdditional = is_null($request->roles_additional) ? [] : $request->roles_additional;

            $this->saveUserRolesAdditional($user, $rolesAdditional);

            $flash_status = __('common.flash_status.user_add');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.user_not_add');
        }

        return redirect()->route('admin.users.index')->with('status', $flash_status);
    }

    /**
     * Edit user
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($this->user()->cant('update', User::class)) {
            return $this->adminNoAccess();
        }

        $user_id = (int) $request->user_id;
        $user = User::findOrFail($user_id);
        $roles = Role::orderBy('role_display', 'asc')->get();
        $userRole = UserRole::where('user_id', $user_id)->pluck('role_id')->toArray();
        return view('admin.users_edit', ['user' => $user, 'roles' => $roles, 'user_role' => $userRole]);
    }

    /**
     * Update user
     * @param UserUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request)
    {
        if ($this->user()->cant('update', User::class)) {
            return $this->adminNoAccess();
        }

        $user_id = (int) $request->user_id;
        $user = User::findOrFail($user_id);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->role_id = $request->role_id;
        $rolesAdditional = is_null($request->roles_additional) ? [] : $request->roles_additional;

        try {
            // See UserObserver updating event for details
            $user->save();

            $userRole = new UserRole();
            $userRole->where('user_id', $user->id)->delete();

            $this->saveUserRolesAdditional($user, $rolesAdditional);

            $flash_status = __('profile.success.updated', ['name' => $user->name]);
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.user_not_update');
        }

        return redirect()->route('admin.users.index')->with('status', $flash_status);
    }

    /**
     * Destroy user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($this->user()->cant('delete', User::class)) {
            return $this->adminNoAccess();
        }

        $user_id = (int) $request->user_id;
        $user = User::findOrFail($user_id);

        try {
            // See UserObserver deleting event for details
            $user->delete();
            $flash_status = __('profile.success.deleted', ['name' => $user->name]);
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.user_not_delete');
        }

        return redirect()->route('admin.users.index', $request->query())->with('status', $flash_status);
    }

    /**
     * Get user list with filter params
     * @param array $data
     * @return mixed
     */
    private function getUserListFilter(array $data)
    {
        $emailSearch = array_key_exists('emailSearch', $data) ? $data['emailSearch'] : '' ;
        $roleSearch = array_key_exists('roleSearch', $data) ? $data['roleSearch'] : '' ;
        $sortBy = array_key_exists('sortBy', $data) ? $data['sortBy'] : '' ;

        $users = User::with('role', 'additionalRoles')->orderBy($sortBy, 'asc');

        if ($emailSearch) {
            $users = $users->emailFilter($emailSearch);
        }

        if ($roleSearch) {
            $users = $users->roleFilter($roleSearch);
        }

        $return = $users->paginate();

        if ($return->currentPage() > $return->lastPage()) {
            $lastPage = $return->lastPage();
            Paginator::currentPageResolver(function () use ($lastPage) {
                return $lastPage;
            });

            $return = $users->paginate();
        }

        return $return;
    }

    /**
     * Save additional roles for user
     * @param User $user
     * @param array $roles
     */
    private function saveUserRolesAdditional(User $user, array $roles)
    {
        if ($roles) {
            $rolesAdditional = [];

            foreach ($roles as $role_id) {
                $rolesAdditional[] = ['user_id' => $user->id, 'role_id' => $role_id];
            }

            $userRole = new UserRole();
            $userRole->insert($rolesAdditional);
        }
    }
}

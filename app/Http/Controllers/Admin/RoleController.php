<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleAddRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Traits\AdminCommon;
use Illuminate\Http\Request;

class RoleController extends Controller
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
     * Show roles list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($this->user()->cant('viewAny', Role::class)) {
            return $this->adminNoAccess();
        }

        $roles = Role::orderBy('role_name', 'asc')->get();

        return view('admin.roles', ['roles' => $roles]);
    }

    /**
     * Show role
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if ($this->user()->cant('viewAny', Role::class)) {
            return $this->adminNoAccess();
        }

        $request->role_id = (int) $request->role_id;

        $role = Role::findOrFail($request->role_id);

        return view('admin.roles_show', [
            'role' => $role,
            'permissions' => $this->getPermissions(),
            'permission_role' => $this->getRolePermissions($role),
        ]);
    }

    /**
     * Create role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        if ($this->user()->cant('create', Role::class)) {
            return $this->adminNoAccess();
        }

        $permissions = Permission::where('parent_id', '=', 0)->orderBy('order_id', 'asc')->get();
        $permissions->load(['children' => function ($query) {
            $query->orderBy('order_id', 'asc');
        }]);

        return view('admin.roles_create', ['permissions' => $permissions]);
    }

    /**
     * Store role
     *
     * @param RoleAddRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(RoleAddRequest $request)
    {
        if ($this->user()->cant('create', Role::class)) {
            return $this->adminNoAccess();
        }

        try {
            // See RoleObserver creating and created events for details
            $role = Role::create([
                'role_name' => $request->role_name,
                'role_display' => $request->role_display,
            ]);

            $this->saveRolePermissions($role, $request->permissions);

            $flash_status = __('common.flash_status.role_add');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.role_not_add');
        }

        return redirect()->route('admin.roles.index')->with('status', $flash_status);
    }

    /**
     * Edit role
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($this->user()->cant('update', Role::class)) {
            return $this->adminNoAccess();
        }

        $request->role_id = (int) $request->role_id;

        if (self::denyEditDeleteRole($request->role_id)) {
            abort(404);
        }

        $role = Role::findOrFail($request->role_id);

        return view('admin.roles_edit', [
            'role' => $role,
            'permissions' => $this->getPermissions(),
            'permission_role' => $this->getRolePermissions($role),
        ]);
    }

    /**
     * Update role
     *
     * @param RoleUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request)
    {
        if ($this->user()->cant('update', Role::class)) {
            return $this->adminNoAccess();
        }

        $request->role_id = (int) $request->role_id;

        if (self::denyEditDeleteRole($request->role_id)) {
            abort(404);
        }

        $role = Role::findOrFail($request->role_id);

        $role->role_name = $request->role_name;
        $role->role_display = $request->role_display;

        try {
            // See RoleObserver updating event for details
            $role->save();

            $permissionRole = new PermissionRole();
            $permissionRole->where('role_id', '=', $role->id)->delete();

            $this->saveRolePermissions($role, $request->permissions);

            $flash_status = __('common.flash_status.role_update');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.role_not_update');
        }

        return redirect()->route('admin.roles.index')->with('status', $flash_status);
    }

    /**
     * Destroy role
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($this->user()->cant('delete', Role::class)) {
            return $this->adminNoAccess();
        }

        $request->role_id = (int) $request->role_id;

        if (self::denyEditDeleteRole($request->role_id)) {
            abort(404);
        }

        $role = Role::findOrFail($request->role_id);

        try {
            // See RoleObserver deleting event for details
            $role->delete();
            $flash_status = __('common.flash_status.role_delete');
        } catch (\Exception $e) {
            report($e);
            $flash_status = __('common.flash_status.role_not_delete');
        }

        return redirect()->route('admin.roles.index', $request->query())->with('status', $flash_status);
    }

    /**
     * Close for edit and delete default roles 'admin_super' and 'user_registered'
     * @param $role_id
     * @return bool
     */
    public static function denyEditDeleteRole($role_id)
    {
        if ($role_id <= 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get permissions for role
     * @param Role $role
     * @return mixed
     */
    private function getRolePermissions(Role $role)
    {
        if ($role->role_name == 'admin_super') {  // Permissions for damin_super
            $permission_role = Permission::where('parent_id', '!=', 0)->pluck('id')->toArray();
        } elseif ($role->role_name == 'user_registered') {  // Permissions for user_registered
            $permission_role = [];
        } else {
            $permission_role = PermissionRole::where('role_id', '=', $role->id)->pluck('permission_id')->toArray();
        }

        return $permission_role;
    }

    /**
     * Get permissions list
     * @return mixed
     */
    private function getPermissions()
    {
        $permissions = Permission::where('parent_id', '=', 0)->orderBy('order_id', 'asc')->get();

        $permissions->load(['children' => function ($query) {
            $query->orderBy('order_id', 'asc');
        }]);

        return $permissions;
    }

    /**
     * Save permissions for role
     * @param Role $role
     * @param array $permissions
     */
    private function saveRolePermissions(Role $role, array $permissions)
    {
        $rolePermissions = [];
        foreach ($permissions as $permission_id) {
            $rolePermissions[] = ['permission_id' => $permission_id, 'role_id' => $role->id];
        }

        $permissionRole = new PermissionRole();
        $permissionRole->insert($rolePermissions);
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $roles = new Role();
        $tableRoles = $roles->getTable();

        $permissions = new Permission();
        $tablePermissions = $permissions->getTable();

        return [
            'role_name' => 'unique:App\Models\Role,role_name,' . $request->role_id . '|required|string|max:30|
                regex:#^[aA-zZ0-9\-_\s]+$#',
            'role_display' => 'unique:App\Models\Role,role_display,' . $request->role_id . '|required|string|max:30|
                regex:#^[aA-zZ0-9\-_\s]+$#',
            'permissions' => 'required',
            'permissions.*' => 'numeric|exists:' . $tablePermissions . ',id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\User;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserAddRequest extends FormRequest
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
        $users = new User();
        $tableUsers = $users->getTable();

        $roles = new Role();
        $tableRoles = $roles->getTable();

        return [
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:100|unique:' . $tableUsers,
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:' . $tableRoles . ',id',
            'roles_additional.*' => 'sometimes|numeric|exists:' . $tableRoles . ',id',
        ];
    }
}

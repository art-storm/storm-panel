<?php

namespace App\Traits;

use App\Models\Role;

trait UserAuthorize
{
    /**
     * Return all user roles, merging the default and additional roles.
     */
    public function getRolesAll()
    {
        $this->loadRolesRelations();

        return collect([$this->role])->merge($this->additionalRoles);
    }

    /**
     * Return all user permissions.
     */
    public function getPermissionsAll()
    {
        if (!$this->permissions) {
            $roles_id = $this->getRolesAll()->pluck('id')->unique()->toArray();

            $this->permissions = Role::with('permissions')->whereIn('id', $roles_id)
                ->get()->pluck('permissions')->flatten();
        }

        return $this->permissions;
    }

    public function hasPermission($name)
    {
        $permissions = $this->getPermissionsAll();
        $permissions = $permissions->pluck('key')->unique()->toArray();

        return in_array($name, $permissions);
    }

    private function loadRolesRelations()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->relationLoaded('additionalRoles')) {
            $this->load('additionalRoles');
        }
    }
}

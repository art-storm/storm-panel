<?php

namespace App\Traits\Tests;

use App\Models\Role;
use Illuminate\Support\Facades\Event;

trait RoleTrait
{
    /**
     * Create role for tests
     * @param array $permissions
     * @return callable
     */
    private function createRole(array $permissions = [])
    {
        $role = Event::fakeFor(function () use ($permissions) {
            $role = factory(Role::class, 1)
                ->create()
                ->each(function ($role) use ($permissions) {
                    $role->permissions()->attach(array_merge([$this->permission_id], $permissions));
                });
            return $role[0];
        });

        return $role;
    }
}

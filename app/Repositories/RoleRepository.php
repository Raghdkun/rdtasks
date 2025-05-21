<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class RoleRepository.
 */
class RoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Role::class;
    }

    /**
     * @return mixed
     */
    public function getRolesList()
    {
        return Role::toBase()->where('display_name', '!=', 'Client')->orWhere('display_name',
            null)->orderBy('name')->pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return Role
     */
    public function store($input)
    {
        /** @var Role $roles */
        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        DB::beginTransaction();
        try {
            $input['display_name'] = $input['name'];
            $role = Role::create($input);
            $this->attachPermissions($role, $input);
            activity()
                ->causedBy(getLoggedInUser())
                ->withProperties(['modal' => Role::class, 'data' => ''])
                ->performedOn($role)
                ->useLog('New Role created.')
                ->log('New Role '.$role->name.' created.');

            DB::commit();

            return $role->fresh();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return Role
     */
    public function update($input, $id)
    {
        $role = Role::findOrFail($id);
        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        $role->update($input);

        $this->attachPermissions($role, $input);

        return $role->fresh();
    }

    /**
     * @param  Role  $role
     * @param  array  $input
     * @return bool
     */
    public function attachPermissions($role, $input)
    {
        if (isset($input['id']) && ! empty($input['id'])) {
            $oldPermissionsId = DB::table('role_has_permissions')->where('role_id',
                $input['id'])->pluck('permission_id');

            foreach ($oldPermissionsId as $permission) {
                $permissionName = Permission::find($permission)->name;
                $role->revokePermissionTo($permissionName);
            }
        }

        if (isset($input['permissions']) && ! empty($input['permissions'])) {
            $role->givePermissionTo($input['permissions']);
        }

        return true;
    }
}

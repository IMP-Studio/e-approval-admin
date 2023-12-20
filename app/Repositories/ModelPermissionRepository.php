<?php

namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Model;

class ModelPermissionRepository extends BaseRepository
{
    /**
     * @param Permission $model
     */
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    /**
     * Get permissions associated with a given model (e.g., User, Employee).
     *
     * @param string $modelType
     * @param int $modelId
     * @return Collection<int, Model>
     */
    public function getModelPermissions(string $modelType, int $modelId): Collection
    {
        return DB::table('model_has_permissions')
            ->select('permissions.name')
            ->join('permissions', 'model_has_permissions.permission_id', 'permissions.id')
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->get()
            ->pluck('name');
    }

     /**
     * @param Role $role
     * @return Collection<int, Model>
    */
    public function roleHasPermissions(Role $role): Collection
    {
        // @phpstan-ignore-next-line
        return DB::table('role_has_permissions')
            ->select('permissions.name')
            ->join('permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->where('role_id', $role->id)
            ->get()
            ->pluck('name');    
    }
}
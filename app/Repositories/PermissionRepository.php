<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission; 
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Support\Collection;
use App\Models\Model;

class PermissionRepository extends BaseRepository
{
    /**
     * PermissionRepository constructor.
     *
     * @param SpatiePermission $model
     */
    public function __construct()
    {
        parent::__construct(new Permission()); // Use the Spatie Permission model
    }


     /**
     * Set permissions for a role.
     *
     * @param string $role
     * @param array $permissions Array of permission names
     * @return SpatieRole
     */

     public function getAllPermissions(): Collection
     {
         return $this->model->all();
     }
}
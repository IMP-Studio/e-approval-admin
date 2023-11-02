<?php

namespace App\Repositories;

// use App\Models\Role;

use App\Models\User;
use App\Models\Model;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class ModelRepository extends BaseRepository
{
    /**
     * @param Role $model
     */
    public function __construct(SpatieRole $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array<mixed> $attributes
     * @return Collection<int, Model>
     */
    public function all($attributes = []): Collection
    {
        return $this->model
            ->when(isset($attributes['name']) && $attributes['name'] != "", function ($query) use ($attributes) {
                $query->where('name', $attributes['name']);
            })
            ->get();
    }

    /**
     * @param string $name
     * @param string|null $guardName
     * @return SpatieRole
     */
    public function findByName(string $name, $guardName = null): SpatieRole
    {
        // @phpstan-ignore-next-line
        return SpatieRole::findByName($name, $guardName);
    }

    /**
     * @param string $role
     * @param array|mixed $permissions
     * @return SpatieRole
     */
    public function setRolePermissions(string $role, $permissions): SpatieRole
    {
        $role = $this->findByName($role);
        $role->syncPermissions($permissions);

        return $role;
    }

    /**
     *
     * @param  string  $role
     *
     * @return SpatieRole
     */
    public function getUsersByRole($role = null)
    {
        // @phpstan-ignore-next-line
        return SpatieRole::when(isset($role) && $role != "", function ($query) use ($role) {
                $query->whereIn('name', $role);
            })
            ->with('users')
            ->get();
    }
    
    /**
     * Find the model by its ID based on the given model type.
     *
     * @param string $modelType
     * @param int $modelId
     * @return Model|null
     */
    public function findModelById($type, $id) {
        // Replace 'App\Models\YourModel' with the actual namespace and class of your model
        $modelClass = 'App\Models\\' . $type;  // Changed from $modelType to $type
    
        switch ($modelClass) {
            case 'App\Models\User':
                return User::find($id);  // Changed from $modelId to $id
            case 'App\Models\Employee':
                return Employee::find($id);  // Changed from $modelId to $id
            default:
                return null;
        }
    }
}

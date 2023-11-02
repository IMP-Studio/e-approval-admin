<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * @var string
     */
    public $defaultLevel = '';

    /**
     * @var int
     */
    protected $paginate = 25;

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param array<mixed> $attributes
     * @return mixed
     */
    public function all($attributes = [])
    {
        return $this->model->query()
            ->when(auth()->user()->role == 'pusat', function ($query) {
                $query->whereNotIn('role', ['admin', 'pusat']);
            })
            ->when(isset($attributes['role']) && $attributes['role'] != "", function ($query) use ($attributes) {
                $query->whereHas('roles', fn ($query) => $query->where('name', $attributes['role']));
            })
            ->orderBy('role');
    }

    /**
     * @param array<mixed> $attributes
     * @return mixed
     */
    public function getUserHasRole($attributes = [])
    {
        return $this->model
            ->whereHas('roles')
            ->get();
    }

    /**
     * Register new user
     *
     * @param array<mixed> $attributes
     * @return User
     */
    public function register(array $attributes): User
    {
        $user = $this->model->create($attributes);
        $user->assignRole($attributes['level']);

        return $user;
    }

    /**
     * @param string|array<mixed> $roles
     * @param int $userId
     * @return User
     */
    public function syncRoles($roles, $userId): User
    {
        $user = $this->model->find($userId);
        $user->syncRoles($roles);

        return $user;
    }

    /**
     * @param array<mixed> $attributes
     * @return User
     */
    public function create(array $attributes): User
    {
        return $this->model->create($attributes);
    }

    /**
     * @param int $id
     * @return null|User
     */
    public function find($id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return null|User
     */
    public function findBy($attribute, $value): ?User
    {
        return $this->model->where($attribute, $value)->first();
    }

    /**
     * @param array<mixed> $attributes
     * @param int $id
     * @return null|User
     */
    public function update($attributes, $id): ?User
    {
        $model = $this->find($id);
        $model->update($attributes);

        return $model;
    }

    /**
     * @param array<mixed> $attributes
     * @param array<mixed> $values
     * @return null|User
     */
    public function updateOrCreate($attributes, $values): ?User
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete($id): ?bool
    {
        return $this->find($id)->delete();
    }

    /**
     * @param array<mixed> $attr
     * @param array<mixed> $select
     * 
     * @return Collection<int, User>
     */
    public function getWhere(array $attr = [
        ['column' => '', 'operator' => '', 'value' => '']
    ], array $select = []): Collection
    {
        $data = $this->model
            ->select(
                'users.*'
            );

        foreach ($attr as $item) {
            $data->when($item['value'] != '', function ($w) use ($item) {
                $w->where($item['column'], $item['operator'], $item['value']);
            });
        }
        return $data->get();
    }

    /**
     * @param Array<mixed> $attr
     * 
     * @return User|null
     */
    public function firstWhere(array $attr = [
        ['column' => '', 'operator' => '', 'value' => '']
    ])
    {
        $data = $this->model
            ->select(
                'users.*'
            );

        foreach ($attr as $item) {
            $data->when($item['value'] != '', function ($w) use ($item) {
                $w->where($item['column'], $item['operator'], $item['value']);
            });
        }
        return $data->first();
    }
}

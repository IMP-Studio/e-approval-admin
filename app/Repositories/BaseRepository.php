<?php

namespace App\Repositories;

use App\Models\Model;
use Illuminate\Support\Collection;
use App\Interfaces\EloquentRepositoryInterface;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct($model)
{
    $this->model = $model;
}

    /**
     * @param array<mixed> $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param int $id
     * @return Model
     */
    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param string $attribute
     * @return Model
     */
    public function findBy($attribute, $value): ?Model
    {
        return $this->model->where($attribute, $value)->first();
    }

    /**
     * @param array<mixed> $attributes
     * @param int $id
     * @return Model
     */
    public function update($attributes, $id): ?Model
    {
        $model = $this->find($id);
        $model->update($attributes);

        return $model;
    }

    /**
     * @param array<mixed> $attributes
     * @param array<mixed> $values
     * @return Model
     */
    public function updateOrCreate($attributes, $values): ?Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): ?bool
    {
        return $this->find($id)->delete();
    }

    /**
     * @param array<mixed> $attribute
     * @param array<mixed> $value
     * 
     * @return bool
     */
    public function deleteWhere($attribute, $value): ?bool
    {
        return $this->model->where($attribute, $value)->delete();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }
    
}
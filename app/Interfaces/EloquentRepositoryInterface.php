<?php

namespace App\Interfaces;

use App\Models\Model;

interface EloquentRepositoryInterface
{
    /**
     * @param array<mixed> $attributes
     * @return Model
    */
    public function create(array $attributes): \Illuminate\Database\Eloquent\Model;

    /**
     * @param int $id
     * @return Model
    */
    public function find($id): ?Model;

    /**
     * @param string $attribute
     * @param array<mixed> $value
     * 
     * @return Model
    */
    public function findBy($attribute, $value): ?Model;

    /**
     * @param array<mixed> $attributes
     * @param int $id
     * @return Model
    */
    public function update($attributes, $id): ?Model;

    /**
     * @param array<mixed> $attributes
     * @param array<mixed> $values
     * @return Model
    */
    public function updateOrCreate($attributes, $values): ?Model;

    /**
     * @param int $id
     * @return bool
    */
    public function delete($id): ?bool;
}
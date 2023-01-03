<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Repository
{
    private $model;

    public function __construct($model)
    {
        $this->model = app($model);
    }

    public function getOneById($id): ?Model
    {
        return $this->model->find($id);
    }

    /** @return Collection|array<Model> */
    public function getByIds(array $ids): Collection
    {
        return $this->model->find($ids);
    }

    /** @return Collection|array<Model> */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getFirstWhere(...$params): ?Model
    {
        return $this->model->firstWhere(...$params);
    }
}
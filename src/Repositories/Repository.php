<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Repositories;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombCore\Contracts\RepositoryContract;
use InteractiveSolutions\HoneycombCore\Models\HCModel;
use InteractiveSolutions\HoneycombCore\Models\HCUuidModel;

/**
 * Class Repository
 * @package InteractiveSolutions\HoneycombCore\Repositories
 */
abstract class Repository implements RepositoryContract
{
    /**
     *
     */
    const DEFAULT_PER_PAGE = 50;

    /**
     *
     */
    const DEFAULT_ATTRIBUTES_FIELD = 'id';

    /**
     * @return string
     */
    abstract public function model(): string;

    /**
     * @param array $columns
     * @return Collection
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->makeQuery()->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginate(int $perPage = self::DEFAULT_PER_PAGE, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->makeQuery()->paginate($perPage, $columns);
    }

    //todo: since laravel 5.4 make create function

    /**
     * @param array $data
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function insert(array $data = []): bool
    {
        return $this->makeQuery()->insert($data);
    }

    /**
     * @param array $data
     * @param $attributeValue
     * @param string $attributeField
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(array $data, $attributeValue, string $attributeField = self::DEFAULT_ATTRIBUTES_FIELD): int
    {
        array_forget($data, [
            '_method',
            '_token',
        ]);

        return $this->makeQuery()->where($attributeField, $attributeValue)->update($data);
    }

    /**
     * @param array $data
     * @param $attributeValues
     * @param string $attributeField
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateWhereIn(
        array $data,
        $attributeValues,
        string $attributeField = self::DEFAULT_ATTRIBUTES_FIELD
    ): int {
        return $this->makeQuery()->whereIn($attributeField, $attributeValues)->update($data);
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return Model
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->makeQuery()->updateOrCreate($attributes, $values);
    }

    /**
     * @param array $criteria
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete(array $criteria = [])
    {
        return $this->makeQuery()->where($criteria)->delete();
    }

    /**
     * @param $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->makeQuery()->find($id, $columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed|static
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findAndLock($id, array $columns = ['*'])
    {
        return $this->makeQuery()->lockForUpdate()->find($id, $columns);
    }

    /**
     * @param array $criteria
     * @param array $columns
     * @return Model|null|static
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findOneBy(array $criteria = [], array $columns = ['*'])
    {
        return $this->makeQuery()->where($criteria)->first($columns);
    }

    /**
     * @param array $criteria
     * @param array $columns
     * @return array|null|\stdClass
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findOneByAndLock(array $criteria = [], array $columns = ['*'])
    {
        return $this->makeQuery()->where($criteria)->lockForUpdate()->first($columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|Model
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        return $this->makeQuery()->findOrFail($id, $columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findAndLockOrFail($id, array $columns = ['*'])
    {
        return $this->makeQuery()->lockForUpdate()->findOrFail($id, $columns);
    }

    /**
     * @param array $criteria
     * @param array $columns
     * @return Collection
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findAllBy(array $criteria = [], array $columns = ['*']): Collection
    {
        return $this->makeQuery()->select($columns)->where($criteria)->get();
    }

    /**
     * @param array $data
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function insertGetId(array $data): int
    {
        return $this->makeQuery()->insertGetId($data);
    }

    /**
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }

    /**
     * @return HCUuidModel|HCModel|Model
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    final protected function makeModel(): Model
    {
        $model = app($this->model());

        if (!$model instanceof HCModel) {
            throw new \RuntimeException('Class ' . $this->model() . ' must be en instance of InteractiveSolutions\\HoneycombCore\\Models\\HCModel');
        }

        return $model;
    }

    /**
     * @return Builder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    final public function makeQuery(): Builder
    {
        return $this->makeModel()->newQuery();
    }
}
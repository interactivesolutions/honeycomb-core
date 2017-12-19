<?php
/**
 * @copyright 2017 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InteractiveSolutions:
 * E-mail: hello@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */


namespace InteractiveSolutions\HoneycombNewCore\Repositories;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use InteractiveSolutions\HoneycombNewCore\DTO\HCUserDTO;
use InteractiveSolutions\HoneycombNewCore\Models\HCUsers;
use InteractiveSolutions\HoneycombNewCore\Repositories\Traits\HCQueryBuilderTrait;

/**
 * Class HCUserRepository
 * @package InteractiveSolutions\HoneycombNewCore\Repositories
 */
class HCUserRepository extends HCRepository
{

    use HCQueryBuilderTrait;

    /**
     * @return string
     */
    public function model(): string
    {
        return HCUsers::class;
    }

    /**
     * @param string $userId
     * @return HCUsers|Model|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getById(string $userId): ? HCUsers
    {
        return $this->makeQuery()->find($userId);
    }

    /**
     * @param string $userId
     * @return HCUsers|Model|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getByIdWithPersonal(string $userId): ? HCUsers
    {
        return $this->makeQuery()->with('personal')->where('id', '=', $userId)->firstOrFail();
    }

    public function deleteSoft()
    {

    }

    public function deleteForce()
    {

    }

    /**
     * @param string $userId
     * @return HCUserDTO
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getRecordById(string $userId): HCUserDTO
    {
        $record = $this->getById($userId);

        $record->load([
            'roles' => function($query) {
                $query->select('id', 'name as label');
            },
        ]);

        return (new HCUserDTO(
            $record->id,
            $record->email,
            $record->activated_at,
            $record->last_login,
            $record->last_visited,
            $record->last_activity,
            $record->roles
        ))->jsonData();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function getList(Request $request): Collection
    {
        return $this->createQuery($request)->get();
    }

    /**
     * @param Request $request
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function getListPaginate(
        Request $request,
        int $perPage = self::DEFAULT_PER_PAGE,
        array $columns = ['*']
    ): LengthAwarePaginator {
        return $this->createQuery($request)->paginate($perPage, $columns)->appends($request->all());
    }

    /**
     * Creating data query
     *
     * @param Request $request
     * @param array $availableFields
     * @return Builder
     */
    protected function createQuery(Request $request, array $availableFields = null): Builder
    {
        $with = [];

        if ($availableFields == null) {
            $availableFields = $this->model()::getFillableFields();
        }

        $builder = $this->model()::with($with)->select($availableFields)
            ->where(function(Builder $query) use ($request, $availableFields) {
                // add request filtering
                $this->filterByRequestParameters($query, $request, $availableFields);
            });

        // check if soft deleted records must be shown
        $builder = $this->checkForDeleted($builder, $request);

        // search through items
        $builder = $this->search($builder, $request);

        // set order
        $builder = $this->orderData($builder, $request, $availableFields);

        return $builder;
    }
}
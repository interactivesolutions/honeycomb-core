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

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Acl;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\Roles;
use InteractiveSolutions\HoneycombNewCore\Validators\Acl\RolesValidator;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCBaseController;

/**
 * Class RolesController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Acl
 */
class RolesController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return View
     */
    public function adminIndex(): View
    {
        $config = [
            'title' => trans('HCACL::acl_roles.page_title'),
            'listURL' => route('admin.api.acl.roles'),
            'newFormUrl' => route('admin.api.form-manager', ['acl-roles-new']),
            'editFormUrl' => route('admin.api.form-manager', ['acl-roles-edit']),
            'headers' => $this->getAdminListHeader(),
        ];

        $config['actions'][] = 'search';

        return hcview('HCCoreUI::admin.content.list', ['config' => $config]);
    }

    /**
     * Creating Admin List Header based on Main Table
     *
     * @return array
     */
    public function getAdminListHeader(): array
    {
        return [
            'name' => [
                "type" => "text",
                "label" => trans('HCACL::acl_roles.name'),
            ],
            'slug' => [
                "type" => "text",
                "label" => trans('HCACL::acl_roles.slug'),
            ],
        ];
    }

    /**
     * Creating data query
     *
     * @param array $select
     * @return mixed
     */
    protected function createQuery(array $select = null)
    {
        $with = [];

        if ($select == null) {
            $select = Roles::getFillableFields();
        }

        $list = Roles::with($with)->select($select)
            // add filters
            ->where(function($query) use ($select) {
                $query = $this->getRequestParameters($query, $select);
            });

        // enabling check for deleted
        $list = $this->checkForDeleted($list);

        // add search items
        $list = $this->search($list);

        // ordering data
        $list = $this->orderData($list, $select);

        return $list;
    }

    /**
     * List search elements
     * @param Builder $query
     * @param string $phrase
     * @return Builder
     */
    protected function searchQuery(Builder $query, string $phrase): Builder
    {
        return $query->where(function(Builder $query) use ($phrase) {
            $query->where('name', 'LIKE', '%' . $phrase . '%')
                ->orWhere('slug', 'LIKE', '%' . $phrase . '%');
        });
    }

    /**
     * Getting user data on POST call
     * @return array
     * @throws \Exception
     */
    protected function getInputData(): array
    {
        (new RolesValidator())->validateForm();

        $data = [];
        $_data = request()->all();

        array_set($data, 'record.name', array_get($_data, 'name'));
        array_set($data, 'record.slug', array_get($_data, 'slug'));

        return $data;
    }

    /**
     * Getting single record
     *
     * @param $id
     * @return mixed
     */
    public function apiShow(string $id)
    {
        $with = [];

        $select = Roles::getFillableFields();

        $record = Roles::with($with)
            ->select($select)
            ->where('id', $id)
            ->firstOrFail();

        return $record;
    }
}

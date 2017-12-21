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
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCAclPermission;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCBaseController;

/**
 * Class PermissionsController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Acl
 */
class PermissionsController extends HCBaseController
{

    /**
     * Returning configured admin view
     *
     * @return View
     */
    public function adminIndex(): View
    {
        $config = [
            'title' => trans('HCACL::acl_permissions.page_title'),
            'listURL' => route('admin.api.acl.permissions'),
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
                "label" => trans('HCACL::acl_permissions.name'),
            ],
            'controller' => [
                "type" => "text",
                "label" => trans('HCACL::acl_permissions.controller'),
            ],
            'action' => [
                "type" => "text",
                "label" => trans('HCACL::acl_permissions.action'),
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
            $select = HCAclPermission::getFillableFields();
        }

        $list = HCAclPermission::with($with)->select($select)
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
                ->orWhere('controller', 'LIKE', '%' . $phrase . '%')
                ->orWhere('action', 'LIKE', '%' . $phrase . '%');
        });
    }
}

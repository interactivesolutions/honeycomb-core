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

namespace InteractiveSolutions\HoneycombNewCore\Http\Middleware;

use Artisan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HCAclAdminMenu
{
    /**
     * @var array
     */
    private $itemsWithoutParent = [];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if ($request->segment(1) == config('hc.admin_url') && $request->segment(2) != 'api') {
                if (!Cache::has('hc-admin-menu')) {
                    Artisan::call('hc:admin-menu');
                }

                // get menu items from cache
                $menu = Cache::get('hc-admin-menu');

                // get accessible menu items
                $menuAccessible = $this->getAccessibleMenuItems($menu);

                // format set menu items which have parent path to their parent as child
                $menu = $this->buildMenuTree($menuAccessible, '', true);

                // sort menu
                $menu = $this->sortByWeight(
                    array_merge($menu, $this->buildMenuWithoutExistingParent($menuAccessible))
                );

                // add admin menu as global variable in blades
                view()->share('adminMenu', $menu);
            }
        }

        return $next($request);
    }

    /**
     * Sort admin menu by given priority DESC
     *
     * @param array $adminMenu
     * @return array
     */
    private function sortByWeight(array $adminMenu): array
    {
        usort($adminMenu, function ($a, $b) {
            if (!array_key_exists('priority', $a)) {
                $a['priority'] = 0;
            }

            if (!array_key_exists('priority', $b)) {
                $b['priority'] = 0;
            }

            return $b['priority'] <=> $a['priority'];
        });

        foreach ($adminMenu as &$item) {
            if (array_key_exists('children', $item)) {
                $item['children'] = $this->sortByWeight($item['children']);
            }
        }

        return $adminMenu;
    }

    /**
     * Build menu tree
     *
     * @param array $elements
     * @param string $parentRoute
     * @param bool $fillIncorrect - add to array incorrect menu items (items which has parent but parent doesnt exist)
     * @return array
     */
    private function buildMenuTree(array $elements, $parentRoute = '', $fillIncorrect = true): array
    {
        $branch = [];

        foreach ($elements as $element) {
            $parent = array_get($element, 'parent');

            if ($parent == $parentRoute) {
                $children = $this->buildMenuTree($elements, $element['route'], $fillIncorrect);

                if ($children) {
                    $element['children'] = $children;
                }

                $branch[] = $element;
            } else {
                if (!is_null($parent) && $fillIncorrect) {
                    $value = array_first($elements, function ($value, $key) use ($parent) {
                        return $value['route'] == $parent;
                    }, false);

                    if (!$value) {
                        $this->itemsWithoutParent[] = $element;
                    }
                }
            }
        }

        return $branch;
    }

    /**
     * Filter acl permissions
     *
     * @param $menus
     * @return array
     */
    private function getAccessibleMenuItems($menus): array
    {
        foreach ($menus as $key => $menu) {
            if (!array_key_exists('aclPermission', $menu) || auth()->user()->cannot($menu['aclPermission'])) {
                unset($menus[$key]);
            }
        }

        return array_values($menus);
    }

    /**
     * Check if exists in array
     *
     * @param $items
     * @param $routeName
     * @return bool
     */
    private function existInArray($items, $routeName): bool
    {
        foreach ($items as $item) {

            if ($item['route'] == $routeName) {
                return true;
            }

            if (array_key_exists('children', $item)) {
                $found = $this->existInArray($item['children'], $routeName);

                if ($found) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Add child menu items to their parents. Only two levels
     *
     * @param $menuAccessible
     * @return array
     */
    private function buildMenuWithoutExistingParent($menuAccessible): array
    {
        $withoutParent = collect($this->itemsWithoutParent)->unique('route')->all();

        foreach ($withoutParent as &$item) {
            $children = $this->buildMenuTree($menuAccessible, $item['route']);

            if ($children) {
                $item['children'] = $children;
            }
        }

        return $withoutParent;
    }
}

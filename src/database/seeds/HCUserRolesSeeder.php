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

namespace InteractiveSolutions\HoneycombCore\Database\Seeds;

use Illuminate\Database\Seeder;
use InteractiveSolutions\HoneycombCore\Models\Acl\HCAclRole;
use InteractiveSolutions\HoneycombCore\Repositories\Acl\RolesRepository;

/**
 * Class HCUserRolesSeeder
 * @package InteractiveSolutions\HoneycombCore\Database\Seeds
 */
class HCUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        // http://stackoverflow.com/q/1598411
        $list = [
            ["name" => "Super Admin", "slug" => RolesRepository::ROLE_SA], // Manage everything
            ["name" => "Project Admin", "slug" => RolesRepository::ROLE_PA], // Manage most aspects of the site
            ["name" => "User", "slug" => RolesRepository::ROLE_U], // Average Joe
        ];

        foreach ($list as $roleData) {
            $role = HCAclRole::where('slug', $roleData['slug'])->first();

            if (!$role) {
                HCAclRole::create($roleData);
            }
        }
    }
}

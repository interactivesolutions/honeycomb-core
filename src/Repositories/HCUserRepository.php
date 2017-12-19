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


use Illuminate\Database\Eloquent\Model;
use InteractiveSolutions\HoneycombNewCore\Models\HCUsers;
use InteractiveSolutions\HoneycombCore\Repositories\Repository;

/**
 * Class HCUserRepository
 * @package InteractiveSolutions\HoneycombNewCore\Repositories
 */
class HCUserRepository extends Repository
{

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
}
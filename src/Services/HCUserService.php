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

namespace InteractiveSolutions\HoneycombNewCore\Services;

use InteractiveSolutions\HoneycombNewCore\Models\HCUser;
use InteractiveSolutions\HoneycombNewCore\Repositories\HCPersonalInfoRepository;
use InteractiveSolutions\HoneycombNewCore\Repositories\HCUserRepository;

/**
 * Class HCUserService
 * @package InteractiveSolutions\HoneycombNewCore\Services
 */
class HCUserService
{
    /**
     * @var HCUserRepository
     */
    private $userRepository;

    /**
     * @var HCPersonalInfoRepository
     */
    private $personalInfoRepository;

    /**
     * HCUserService constructor.
     * @param HCUserRepository $userRepository
     * @param HCPersonalInfoRepository $personalRepository
     */
    public function __construct(HCUserRepository $userRepository, HCPersonalInfoRepository $personalRepository)
    {
        $this->userRepository = $userRepository;
        $this->personalInfoRepository = $personalRepository;
    }

    /**
     * @return HCUserRepository
     */
    public function getRepository(): HCUserRepository
    {
        return $this->userRepository;
    }

    /**
     * @param array $userData
     * @param string $userId
     * @param array $personalData
     * @return HCUser
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateUser(array $userData, string $userId, array $personalData = []): HCUser
    {
        /** @var HCUser $record */
        $record = $this->userRepository->updateOrCreate(['id' => $userId], $userData);
        $this->personalInfoRepository->updateOrCreate(['user_id' => $userId], $personalData);

        return $record;
    }

    /**
     * @param array $userData
     * @param array $personalData
     * @return HCUser
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createUser(array $userData, array $personalData = []): HCUser
    {
        /** @var HCUser $record */
        $record = $this->userRepository->create($userData);
        $personalData['user_id'] = $record->id;

        $this->personalInfoRepository->updateOrCreate(['user_id' => $record->id], $personalData);

        return $record;
    }
}

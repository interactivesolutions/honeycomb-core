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

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin;

use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCController;
use InteractiveSolutions\HoneycombNewCore\Http\Requests\HCUserRequest;
use InteractiveSolutions\HoneycombNewCore\Services\HCUserService;

class HCUserController extends HCController
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var HCUserService
     */
    private $service;

    /**
     * HCUsersController constructor.
     * @param Connection $connection
     * @param HCUserService $service
     */
    public function __construct(Connection $connection, HCUserService $service)
    {
        $this->connection = $connection;
        $this->service = $service;
    }

    /**
     * Admin view
     *
     * @return View
     */
    public function index(): View
    {
        //TODO renew configuration
        $config = [
            'title' => trans('HCNewCore::users.page_title'),
            'listURL' => route('admin.api.user'),
            'newFormUrl' => route('admin.api.form-manager', ['users-new']),
            'editFormUrl' => route('admin.api.form-manager', ['users-edit']),
            'headers' => $this->getIndexHeaders(),
        ];

        $config['actions'] = $this->getActions('interactivesolutions_honeycomb_acl_users_');

        return view('HCNewCore::admin.service.index', ['config' => $config]);
    }

    /**
     * Creating Admin List Header based on Main Table
     *
     * @return array
     */
    public function getIndexHeaders(): array
    {
        return [
            'email' => [
                "type" => "text",
                "label" => trans('HCNewCore::users.email'),
            ],
            'last_login' => [
                "type" => "text",
                "label" => trans('HCNewCore::users.last_login'),
            ],
            'last_activity' => [
                "type" => "text",
                "label" => trans('HCNewCore::users.last_activity'),
            ],
            'activated_at' => [
                "type" => "text",
                "label" => trans('HCNewCore::users.activation.activated_at'),
            ],
        ];
    }

    /**
     * Create record
     * @param HCUserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(HCUserRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->createUser($request->getInputData());

            $this->connection->commit();

        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            return \HCResponse::error('USER-001', $exception->getMessage());
        }

        return $this->getById($record->id);
    }

    /**
     * @param HCUserRequest $request
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function update(HCUserRequest $request, string $id)
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->updateUser($id, $request->getUserData());

            $this->connection->commit();

        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            return \HCResponse::error('USER-002', $exception->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $record,
        ]);
    }

    /**
     * Getting single record
     *
     * @param string $recordId
     * todo: return DTO
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getById(string $recordId): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getRecordById($recordId)
        );
    }


    /**
     * @param HCUserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteSoft(HCUserRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $list = $this->service->getRepository()->deleteSoft($request->getDestroyIds());

            $this->connection->commit();
        } catch (\Exception $exception) {
            $this->connection->rollBack();

            return \HCResponse::error('USER-002', $exception->getMessage());
        }

        return response()->json(['success' => true, 'list' => $list]);
    }

    /**
     * @param HCUserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteForce(HCUserRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $list = $this->service->getRepository()->deleteForce($request->getDestroyIds());

            $this->connection->commit();
        } catch (\Exception $exception) {
            $this->connection->rollBack();

            return \HCResponse::error('USER-002', $exception->getMessage());
        }

        return response()->json(['success' => true, 'list' => $list]);
    }

    /**
     * @param HCUserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function restore(HCUserRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $list = $this->service->getRepository()->restore($request->getInputData());

            $this->connection->commit();
        } catch (\Exception $exception) {
            $this->connection->rollBack();

            return \HCResponse::error('USER-002', $exception->getMessage());
        }

        return response()->json(['success' => true, 'list' => $list]);
    }

    /**
     * Getting a list records for API call
     *
     * @param HCUserRequest $request
     * @return JsonResponse
     */
    public function getList(HCUserRequest $request): JsonResponse
    {
        return $this->service->getRepository()->getList($request);
    }

    /**
     * Creating data list
     * @param HCUserRequest $request
     * @return JsonResponse
     */
    public function getListPaginate(HCUserRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getListPaginate($request)
        );
    }

}

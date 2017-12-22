<?php
/**
 * @copyright 2017 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the 'Software'), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
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

namespace InteractiveSolutions\HoneycombCore\Http\Controllers\Admin;

use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use InteractiveSolutions\HoneycombCore\Helpers\HCFrontendResponse;
use InteractiveSolutions\HoneycombCore\Http\Controllers\HCBaseController;
use InteractiveSolutions\HoneycombCore\Http\Requests\HCUserRequest;
use InteractiveSolutions\HoneycombCore\Services\HCUserService;

class HCUserController extends HCBaseController
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
     * @var HCFrontendResponse
     */
    private $response;

    /**
     * HCUsersController constructor.
     * @param Connection $connection
     * @param HCUserService $service
     * @param HCFrontendResponse $response
     */
    public function __construct(Connection $connection, HCUserService $service, HCFrontendResponse $response)
    {
        $this->connection = $connection;
        $this->service = $service;
        $this->response = $response;
    }

    /**
     * Admin panel page view
     *
     * @return View
     */
    public function index(): View
    {
        //TODO renew configuration
        $config = [
            'title' => trans('HCCore::user.page_title'),
            'listURL' => route('admin.api.user'),
            'newFormUrl' => route('admin.api.form-manager', ['user-new']),
            'editFormUrl' => route('admin.api.form-manager', ['user-edit']),
            'headers' => $this->getTableColumns(),
        ];

        $config['actions'] = $this->getActions('interactivesolutions_honeycomb_acl_users_');

        return view('HCCore::admin.service.index', ['config' => $config]);
    }

    /**
     * Get admin page table columns settings
     *
     * @return array
     */
    public function getTableColumns(): array
    {
        $columns = [
            'email' => [
                'type' => 'text',
                'label' => trans('HCCore::user.email'),
            ],
            'last_login' => [
                'type' => 'text',
                'label' => trans('HCCore::user.last_login'),
            ],
            'last_activity' => [
                'type' => 'text',
                'label' => trans('HCCore::user.last_activity'),
            ],
            'activated_at' => [
                'type' => 'text',
                'label' => trans('HCCore::user.activation.activated_at'),
            ],
        ];

        return $columns;
    }

    /**
     * Getting a list records for API call
     *
     * @param HCUserRequest $request
     * @return JsonResponse
     */
    public function getList(HCUserRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getList($request)
        );
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

    /**
     * Store record
     *
     * @param HCUserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(HCUserRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->createUser(
                $request->getUserInput(),
                $request->getRoles(),
                $request->getPersonalData(),
                request()->filled('send_welcome_email'),
                request()->filled('send_password')
            );

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Created', $record);
    }

    /**
     * Update record
     *
     * @param HCUserRequest $request
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function update(HCUserRequest $request, string $id): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->updateUser(
                $id,
                $request->getUserInput(),
                $request->getRoles(),
                $request->getPersonalData()
            );

            if ($request->wantToActivate()) {
                $this->service->activateUser($record->id);
            }

            $this->connection->commit();

        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Updated', $record);
    }

    /**
     * Getting single record
     *
     * @param string $recordId
     * @return JsonResponse
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

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Successfully deleted', $list);
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

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Successfully deleted', $list);
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
            $list = $this->service->getRepository()->restore($request->getUserInput());

            $this->connection->commit();
        } catch (\Exception $exception) {
            $this->connection->rollBack();

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Successfully deleted', $list);
    }
}

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

namespace InteractiveSolutions\HoneycombCore\Http\Controllers\Frontend;

use DB;
use HCLog;
use Illuminate\Database\Connection;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use InteractiveSolutions\HoneycombCore\Services\HCUserActivationService;
use InteractiveSolutions\HoneycombCore\Http\Controllers\HCBaseController;

/**
 * Class HCAuthController
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers
 */
class HCAuthController extends HCBaseController
{
    use AuthenticatesUsers;

    /**
     * Max login attempts
     *
     * @var int
     */
    protected $maxLoginAttempts = 5;

    /**
     * The number of minutes to delay further login attempts.
     *
     * @var int
     */
    protected $lockoutTime = 1;

    /**
     * Redirect url
     *
     * @var
     */
    protected $redirectUrl;

    /**
     * @var HCUserActivationService
     */
    private $activation;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * AuthController constructor.
     * @param Connection $connection
     * @param HCUserActivationService $activation
     */
    public function __construct(Connection $connection, HCUserActivationService $activation)
    {
        $this->connection = $connection;
        $this->activation = $activation;
    }


    /**
     * Displays users login form
     *
     * @return View
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function showLoginForm(): View
    {
        $config = [];

        return view('HCCore::auth.login', $config);
    }

    /**
     * Function which login users
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Container\EntryNotFoundException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        if (!$this->attemptLogin($request)) {
            return HCLog::info('AUTH-002', trans('HCCore::users.errors.login'));
        }

        // check if user is not activated
        if (auth()->user()->isNotActivated()) {
            $user = auth()->user();

            $this->logout($request);

            $response = $this->activation->sendActivationMail($user);

            return HCLog::info('AUTH-003', $response);
        }

        //TODO update providers?

        auth()->user()->updateLastLogin();

        //redirect to intended url
        return response(['success' => true, 'redirectURL' => session()->pull('url.intended', url('/'))]);
    }

    /**
     * Display users register form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function showRegister()
    {
        if (!config('hc.allow_registration')) {
            return redirect('/');
        }

        return view('HCCore::auth.register');
    }

    /**
     * User registration
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function register()
    {
        if (!config('hc.allow_registration')) {
            throw new \Exception();
        }

        $userController = new HCUsersController();

        $this->connection->beginTransaction();

        try {
            $response = $userController->apiStore();

            if (get_class($response) == 'Illuminate\Http\JsonResponse') {
                return $response;
            }

        } catch (\Exception $e) {
            $this->connection->rollback();

            return response(['success' => false, 'message' => 'AUTH-004 - ' . $e->getMessage()]);
        }

        $this->connection->commit();

        session(['activation_message' => trans('HCCore::users.activation.activate_account')]);

        if ($this->redirectUrl) {
            return response(['success' => true, 'redirectURL' => $this->redirectUrl]);
        } else {
            return response(['success' => true, 'redirectURL' => route('auth.login')]);
        }
    }

    /**
     * Get input data
     *
     * @param $data
     * @return mixed
     */
    protected function getData(array $data)
    {
        /* // get nickname from first part of email and add timestamp after it
         $nickname = head (explode ('@', array_get ($data, 'userData.email'))) . '_' . Carbon::now ()->timestamp;

         array_set ($data, 'userPersonalData.nickname', $nickname);

         $basicRole = ACLRole::whereSlug ('basic')->firstOrFail ();

         array_set ($data, 'roles', [$basicRole->id]);

         return $data;*/
    }

    /**
     * Logout function
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/')
            ->with('flash_notice', trans('HCCore::users.success.logout'));
    }

    /**
     * Update user providers during login
     */
    protected function updateProviders()
    {
        /*$user = auth ()->user ();

        $provider = 'LOCAL';

        $user->update (['provider' => $provider]);

        $user->providers ()->sync ([$provider], false);*/
    }

    /**
     * Show activation page
     *
     * @param string $token
     * @return View
     */
    public function showActivation(string $token): View
    {
        $message = null;

        $tokenRecord = DB::table('hc_users_activations')->where('token', $token)->first();

        if (is_null($tokenRecord)) {
            $message = trans('HCCore::users.activation.token_not_exists');
        } else {
            if (strtotime($tokenRecord->created_at) + 60 * 60 * 24 < time()) {
                $message = trans('HCCore::users.activation.token_expired');
            }
        }

        return view('HCCore::auth.activation', ['token' => $token, 'message' => $message]);
    }

    /**
     * Active user account
     * @return $this|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function activate()
    {
        $this->connection->beginTransaction();

        try {
            $this->activation->activateUser(
                request()->input('token')
            );
        } catch (\Exception $e) {
            $this->connection->rollback();

            return redirect()->back()->withErrors($e->getMessage());
        }

        $this->connection->commit();

        return redirect()->intended();
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $this->maxLoginAttempts, $this->lockoutTime
        );
    }


    /**
     * Redirect the user after determining they are locked out.
     *
     * @param Request $request
     * @return mixed
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return HCLog::info('AUTH-005', trans('auth.throttle', ['seconds' => $seconds]));
    }
}

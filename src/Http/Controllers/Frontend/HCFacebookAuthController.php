<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers\Frontend;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InteractiveSolutions\HoneycombCore\Services\HCUserService;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

/**
 * Class HCFacebookAuthController
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers\Frontend
 */
class HCFacebookAuthController extends Controller
{
    /**
     * @var HCUserService
     */
    private $service;

    /**
     * HCFacebookAuthController constructor.
     * @param HCUserService $service
     */
    public function __construct(HCUserService $service)
    {
        $this->service = $service;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function handleProviderCallback(Request $request): RedirectResponse
    {
        if (!$request->filled('code')) {
            logger()->info('Facebook: ' . $request->input('error') . ' : ' . $request->input('error_reason'));

            return redirect()->route('auth.login');
        }

        /** @var User $user */
        $user = Socialite::driver('facebook')->user();

        if (is_null($user->email)) {
            return $this->deAuthorize($user);
        }

        auth()->login(
            $this->service->createOrUpdateFacebookUser($user)
        );

        return redirect(session('url.intended', url('/')));
    }

    /**
     * DeAuthorize user from app
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function deAuthorize(User $user): RedirectResponse
    {
        $client = new Client;

        $client->delete("https://graph.facebook.com/{$user->id}/permissions",
            [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => [
                    'access_token' => $user->token,
                ],
            ]
        );

        return redirect()->route('auth.login');
    }
}

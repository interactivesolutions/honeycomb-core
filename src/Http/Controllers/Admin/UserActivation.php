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

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Connection;
use InteractiveSolutions\HoneycombNewCore\Models\HCUsers;
use Mail;

/**
 * Class UserActivation
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers
 */
class UserActivation
{
    /**
     * Activations table
     *
     * @var string
     */
    protected $table = 'hc_users_activations';

    /**
     * Number of hours that needs to pass before we send a now activation email but only if user request it
     *
     * @var int
     */
    protected $resendAfter = 24;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Mail message
     *
     * @var
     */
    protected $mailMessage;

    /**
     * UserActivation constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Send activation mail
     *
     * @param $user
     * @return string
     * @throws \Exception
     */
    public function sendActivationMail($user): string
    {
        if (!$this->shouldSend($user)) {
            return trans('HCACL::users.activation.check_email');
        }

        $this->connection->beginTransaction();

        try {
            $token = $this->createActivation($user);

            $user->sendActivationLinkNotification($token);
        } catch (\Exception $e) {
            $this->connection->rollback();

            throw new \Exception('Activation code or mail sending failed');
        }

        $this->connection->commit();

        return trans('HCACL::users.activation.resent_activation');
    }

    /**
     * Activate user
     *
     * @param $token
     * @throws \Exception
     */
    public function activateUser($token): void
    {
        $activation = $this->getActivationByToken($token);

        if ($activation === null) {
            throw new \Exception(trans('HCACL::users.activation.bad_token'));
        }

        $user = $this->getUser($activation);

        if (is_null($user)) {
            throw new \Exception(trans('HCACL::users.activation.user_not_found'));
        }

        // activate user
        $user->activate();

        // delete activation code
        $this->deleteActivation($token);

        // login user to the site
        auth()->login($user);
    }

    /**
     * Check if activation mail should be resent
     *
     * @param $user
     * @return bool
     */
    protected function shouldSend($user): bool
    {
        $activation = $this->getActivation($user);

        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

    /**
     * Get token
     *
     * @return string
     */
    protected function getToken(): string
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    /**
     * Create activation
     *
     * @param $user
     * @return string
     */
    public function createActivation($user): string
    {
        $activation = $this->getActivation($user);

        if (!$activation) {
            return $this->createToken($user);
        }

        return $this->regenerateToken($user);
    }

    /**
     * Regenerate token
     *
     * @param $user
     * @return string
     */
    protected function regenerateToken($user): string
    {
        $token = $this->getToken();

        DB::table($this->table)->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        return $token;
    }

    /**
     * Create token
     *
     * @param $user
     * @return string
     */
    protected function createToken($user): string
    {
        $token = $this->getToken();

        DB::table($this->table)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        return $token;
    }

    /**
     * Get activation
     *
     * @param $user
     * @return mixed|static
     */
    public function getActivation($user)
    {
        return DB::table($this->table)->where('user_id', $user->id)->first();
    }

    /**
     * Get activation by token
     *
     * @param $token
     * @return mixed|static
     */
    public function getActivationByToken($token)
    {
        return DB::table($this->table)->where('token', $token)->first();
    }

    /**
     * Delete activation
     *
     * @param $token
     */
    public function deleteActivation($token): void
    {
        DB::table($this->table)->where('token', $token)->delete();
    }

    /**
     * Get user by activate
     *
     * @param $activation
     * @return mixed
     */
    private function getUser($activation)
    {
        return HCUsers::findOrFail($activation->user_id);
    }
}

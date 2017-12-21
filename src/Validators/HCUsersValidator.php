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

namespace InteractiveSolutions\HoneycombNewCore\Validators;

use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCCoreFormValidator;


/**
 * Class HCUsersValidator
 * @package InteractiveSolutions\HoneycombNewCore\Validators
 */
class HCUsersValidator extends HCCoreFormValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array`
     */
    protected function rules(): array
    {
        switch ($this->methodType()) {
            case 'POST':
                return $this->createRules();

            case 'PUT':
                return $this->updateRules();

            default:
                return ['no_rules' => 'required'];
        }
    }

    /**
     * Rules for item creation
     *
     * @return array
     */
    private function createRules(): array
    {
        return [
            'email' => 'required|email|unique:hc_users,email|min:5',
            'password' => 'required|min:5',
        ];
    }

    /**
     * Rules when item is updating
     *
     * @return array
     */
    private function updateRules(): array
    {
        return [
            'email' => 'required|email|min:5|unique:hc_users,email,' . $this->id,
            'roles' => 'required',
            'password' => 'nullable|min:5|confirmed',
            'password_confirmation' => 'required_with:password|nullable|min:5',
        ];
    }

    /**
     * Custom messages
     *
     * @return array
     */
    protected function messages(): array
    {
        // field.condition => translation
        return [
            'nickname.required' => trans('HCNewCore::users.validator.nickname.required'),
            'nickname.unique' => trans('HCNewCore::users.validator.nickname.unique'),
            'nickname.min' => trans('HCNewCore::users.validator.nickname.min', ['count' => 3]),
            'email.required' => trans('HCNewCore::users.validator.email.required'),
            'email.unique' => trans('HCNewCore::users.validator.email.unique'),
            'email.min' => trans('HCNewCore::users.validator.email.min', ['count' => 5]),
            'password.required' => trans('HCNewCore::users.validator.password.required'),
            'password.min' => trans('HCNewCore::users.validator.password.min', ['count' => 5]),
            'password.confirmed' => trans('HCNewCore::users.validator.password.confirmed'),
            'roles.required' => trans('HCNewCore::users.validator.roles.required'),
        ];
    }
}

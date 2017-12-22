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

namespace InteractiveSolutions\HoneycombCore\Forms;

/**
 * Class HCPasswordResetForm
 * @package InteractiveSolutions\HoneycombCore\Forms
 */
class HCPasswordResetForm extends HCBaseForm
{
    /**
     * Creating form
     *
     * @param bool $edit
     * @return array
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function createForm(bool $edit = false): array
    {
        $form = [
            "storageURL" => route('users.password.reset.post'),
            "buttons" => [
                [
                    "class" => "col-centered",
                    "label" => trans('HCCore::user.passwords.reset_button'),
                    "type" => "submit",
                ],
            ],
            "structure" => [
                [
                    "type" => "email",
                    "fieldID" => "email",
                    "label" => trans('HCCore::user.login.email'),
                    "required" => 1,
                    "requiredVisible" => 1,
                    "maxLength" => "197",
                ],
                [
                    "type" => "password",
                    "fieldID" => "password",
                    "label" => trans('HCCore::user.passwords.new'),
                    "required" => 1,
                    "requiredVisible" => 1,
                    "maxLength" => "197",
                ],
                [
                    "type" => "password",
                    "fieldID" => "password_confirmation",
                    "label" => trans('HCCore::user.passwords.new_again'),
                    "required" => 1,
                    "requiredVisible" => 1,
                    "maxLength" => "197",
                ],
                [
                    "type" => "singleLine",
                    "fieldID" => "token",
                    "hidden" => 1,
                    "required" => 1,
                    "requiredVisible" => 1,
                    "maxLength" => "255",
                    "value" => request()->input('token'),
                ],
            ],
        ];

        if ($this->multiLanguage) {
            $form['availableLanguages'] = getHCContentLanguages();
        }

        if (!$edit) {
            return $form;
        }

        return $form;
    }
}

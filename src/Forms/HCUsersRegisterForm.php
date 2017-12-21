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

namespace InteractiveSolutions\HoneycombNewCore\Forms;

/**
 * Class HCUsersRegisterForm
 * @package InteractiveSolutions\HoneycombNewCore\Forms
 */
class HCUsersRegisterForm
{
    /**
     * Name of the form
     *
     * @var string
     */
    protected $formID = 'users-register';

    /**
     * Is form multi language
     *
     * @var int
     */
    protected $multiLanguage = 0;

    /**
     * Creating form
     *
     * @param bool $edit
     * @return array
     */
    public function createForm(bool $edit = false): array
    {
        $form = [
            'storageURL' => route('auth.register'),
            'buttons' => [
                [
                    "class" => "col-centered",
                    "label" => trans('HCTranslations::core.buttons.register'),
                    "type" => "submit",
                ],
            ],
            'structure' => [
                [
                    "type" => "singleLine",
                    "fieldID" => "email",
                    "label" => trans("HCACL::users.email"),
                    "required" => 1,
                    "requiredVisible" => 1,
                ],
                [
                    "type" => "password",
                    "fieldID" => "password",
                    "label" => trans("HCACL::users.register.password"),
                    "required" => 1,
                    "requiredVisible" => 1,
                ],
            ],
        ];

        return $form;
    }
}

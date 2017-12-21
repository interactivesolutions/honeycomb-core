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

use InteractiveSolutions\HoneycombCore\Models\Acl\HCAclRole;
use InteractiveSolutions\HoneycombCore\Models\HCUser;

/**
 * Class HCUserForm
 * @package InteractiveSolutions\HoneycombCore\Forms
 */
class HCUserForm extends HCBaseForm
{
    /**
     * Creating form
     *
     * @param bool $edit
     * @return array
     */
    public function createForm(bool $edit = false): array
    {
        $rolesStructure = [
            "type" => 'checkBoxList',
            "fieldID" => 'roles',
            "tabID" => trans('HCCore::users.roles'),
            "label" => trans("HCCore::users.role_groups"),
            "required" => 1,
            "requiredVisible" => 1,
            "options" => $this->getRolesForUserCreation(),
        ];

        $form = [
            'storageURL' => route('admin.api.user'),
            'buttons' => [
                [
                    "class" => "col-centered",
                    "label" => trans('HCTranslations::core.buttons.submit'),
                    "type" => "submit",
                ],
            ],
            'structure' => [
                [
                    "type" => "email",
                    "fieldID" => "email",
                    "tabID" => trans('HCTranslations::core.general'),
                    "label" => trans("HCCore::users.email"),
                    "required" => 1,
                    "requiredVisible" => 1,
                ],
                [
                    "type" => "password",
                    "fieldID" => "password",
                    "tabID" => trans('HCTranslations::core.general'),
                    "label" => trans("HCCore::users.register.password"),
                    "required" => 1,
                    "requiredVisible" => 1,
                ],
//                formManagerYesNo('checkBoxList', 'is_active', trans("HCCore::users.active"), 0, 0,
//                    trans('HCTranslations::core.general'), false),
//                formManagerYesNo('checkBoxList', 'send_welcome_email', trans("HCCore::users.send_welcome_email"), 0, 0,
//                    trans('HCTranslations::core.general'), false),
//                formManagerYesNo('checkBoxList', 'send_password', trans("HCCore::users.send_password"), 0, 0,
//                    trans('HCTranslations::core.general'), false),
                $rolesStructure,
            ],
        ];

        if ($this->multiLanguage) {
            $form['availableLanguages'] = [];
        } //TOTO implement honeycomb-languages package

        if (!$edit) {
            return $form;
        }

        //Make changes to edit form if needed

        $form['structure'] = [];

        $form['structure'] = array_merge($form['structure'], [
            [
                "type" => "singleLine",
                "fieldID" => "first_name",
                "tabID" => trans('HCTranslations::core.general'),
                "label" => trans("HCCore::users.firstname"),
                "required" => 0,
                "requiredVisible" => 0,
                "readonly" => 0,
            ],
            [
                "type" => "singleLine",
                "fieldID" => "last_name",
                "tabID" => trans('HCTranslations::core.general'),
                "label" => trans("HCCore::users.lastname"),
                "required" => 0,
                "requiredVisible" => 0,
                "readonly" => 0,
            ],
            [
                "type" => "email",
                "fieldID" => "email",
                "tabID" => trans('HCTranslations::core.general'),
                "label" => trans("HCCore::users.email"),
                "required" => 1,
                "requiredVisible" => 1,
            ],
            [
                "type" => "password",
                "fieldID" => "password",
                "tabID" => trans('HCTranslations::core.general'),
                "label" => trans('HCCore::users.passwords.new'),
                "editType" => 0,
                "required" => 0,
                "requiredVisible" => 0,
                "properties" => [
                    "strength" => "1" // case 0: much, case 1: 4 symbols, case 2: 6 symbols
                ],
            ],
            [
                "type" => "password",
                "fieldID" => "password_confirmation",
                "tabID" => trans('HCTranslations::core.general'),
                "label" => trans('HCCore::users.passwords.new_again'),
                "editType" => 0,
                "required" => 0,
                "requiredVisible" => 0,
                "properties" => [
                    "strength" => "1" // case 0: much, case 1: 4 symbols, case 2: 6 symbols
                ],
            ],
//            formManagerYesNo('checkBoxList', 'is_active', trans("HCCore::users.active"), 0, 0,
//                trans('HCTranslations::core.general'), false),
            $rolesStructure,
            [
                "type" => "singleLine",
                "fieldID" => "last_login",
                "tabID" => trans('HCCore::users.activity'),
                "label" => trans("HCCore::users.last_login"),
                "required" => 0,
                "requiredVisible" => 0,
                "readonly" => 1,
            ],
            [
                "type" => "singleLine",
                "fieldID" => "last_activity",
                "tabID" => trans('HCCore::users.activity'),
                "label" => trans("HCCore::users.last_activity"),
                "required" => 0,
                "requiredVisible" => 0,
                "readonly" => 1,
            ],
            [
                "type" => "singleLine",
                "fieldID" => "activated_at",
                "tabID" => trans('HCCore::users.activity'),
                "label" => trans("HCCore::users.activation.activated_at"),
                "required" => 0,
                "requiredVisible" => 0,
                "readonly" => 1,
            ],
        ]);

        return $form;
    }

    /**
     * Get roles for user creation. User can give roles that he owns
     *
     * @return array
     */
    public function getRolesForUserCreation(): array
    {
        $rolesList = [];

        /** @var HCUser $user */
        $user = auth()->user();

        if (auth()->check()) {
            if ($user->isSuperAdmin()) {
                $rolesList = HCAclRole::select('id', 'name as label')->orderBy('name')->get()->toArray();
            } else {
                foreach ($user->roles as $role) {
                    $rolesList[] = [
                        'id' => $role->id,
                        'label' => $role->name,
                    ];
                }
            }
        }

        return $rolesList;
    }
}

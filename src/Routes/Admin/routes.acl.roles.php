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

Route::group([
    'prefix' => config('hc.admin_url'),
    'namespace' => 'Admin',
    'middleware' => ['web', 'auth'],
], function () {
    Route::get('users/roles', [
        'as' => 'admin.acl.role.index',
        'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_list'],
        'uses' => 'HCRoleController@adminIndex',
    ]);

    Route::group(['prefix' => 'api/users/roles'], function () {
        Route::get('/', [
            'as' => 'admin.api.acl.role',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_list'],
            'uses' => 'HCRoleController@getListPaginate',
        ]);

        Route::post('/', [
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_create'],
            'uses' => 'HCRoleController@store',
        ]);

        Route::get('list', [
            'as' => 'admin.api.acl.role.list',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_list'],
            'uses' => 'HCRoleController@getList',
        ]);

        Route::delete('/', [
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_delete'],
            'uses' => 'HCRoleController@deleteSoft',
        ]);

        Route::post('restore', [
            'as' => 'admin.api.acl.role.restore',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_update'],
            'uses' => 'HCRoleController@restore',
        ]);

        Route::delete('force', [
            'as' => 'admin.api.acl.role.force.multi',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_force_delete'],
            'uses' => 'HCRoleController@deleteForce',
        ]);

        Route::group(['prefix' => '{id}'], function () {

            Route::get('/', [
                'as' => 'admin.api.acl.role.single',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_list'],
                'uses' => 'HCRoleController@apiShow',
            ]);

            Route::put('/', [
                'as' => 'admin.api.role.update',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_update'],
                'uses' => 'HCRoleController@apiUpdate',
            ]);

            Route::patch('strict', [
                'as' => 'admin.api.acl.role.patch',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_acl_role_update'],
                'uses' => 'HCRoleController@patch',
            ]);
        });
    });
});

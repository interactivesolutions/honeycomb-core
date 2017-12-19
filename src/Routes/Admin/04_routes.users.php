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

Route::group(['prefix' => config('hc.admin_url'), 'namespace' => 'Admin', 'middleware' => ['web', 'auth']], function() {

    Route::get('userss', [
        'as' => 'admin.user.index',
        'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_list'],
        'uses' => 'HCUserController@index',
    ]);

    Route::group(['prefix' => 'api/user'], function() {

        Route::get('/', [
            'as' => 'admin.api.user',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_list'],
            'uses' => 'HCUserController@getListPaginate',
        ]);

        Route::post('/', [
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_create'],
            'uses' => 'HCUserController@store',
        ]);

        Route::get('list', [
            'as' => 'admin.api.user.list',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_list'],
            'uses' => 'HCUserController@getList',
        ]);

        Route::delete('/', [
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_delete'],
            'uses' => 'HCUserController@deleteSoft',
        ]);

        Route::post('restore', [
            'as' => 'admin.api.user.restore',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_update'],
            'uses' => 'HCUserController@restore',
        ]);

        Route::delete('force', [
            'as' => 'admin.api.user.destroy.force',
            'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_force_delete'],
            'uses' => 'HCUserController@deleteForce',
        ]);

        Route::group(['prefix' => '{id}'], function() {

            Route::get('/', [
                'as' => 'admin.api.user.single',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_list'],
                'uses' => 'HCUserController@getById',
            ]);

            Route::put('/', [
                'as' => 'admin.api.user.update',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_update'],
                'uses' => 'HCUserController@update',
            ]);

            Route::patch('strict', [
                'as' => 'admin.api.user.patch',
                'middleware' => ['acl:interactivesolutions_honeycomb_acl_user_update'],
                'uses' => 'HCUserController@patch',
            ]);

        });
    });
});

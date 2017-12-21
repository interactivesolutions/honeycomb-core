@extends('HCCore::admin.layout')

@if(isset( $config['title'] ) &&  ! empty($config['title']))
    @section('content-header',  $config['title'] )
@endif

@section('css')
    <style>
        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            padding: 0px 5px 0 5px;
        }

        table > tbody > tr > th:first-child {
            width: 30%;
            min-width: 500px;
        }

        table > tbody > tr > td:not(:first-child), table > tbody > tr > th:not(:first-child) {
            text-align: center;
        }

        tr:first-child {
            max-width: 640px;
        }

    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-3">
            <div class="input-group" style="padding-bottom: 20px;">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input id="searchInput" type="text" class="form-control" placeholder="Type To Filter">
            </div>
        </div>

        @if($config['permissions'] == '[]')
            {{ trans('HCCore::acl_access.no_roles') }}
        @else
            <div class="col-md-12">
                <div id="roleList"></div>
            </div>
        @endif
    </div>


@endsection

@section('scripts')
    <script>
        var roles = {!! $config['roles'] !!};
        var updateUrl = "{{ $config['updateUrl'] }}";
        var permissions = {!!  $config['permissions'] !!};
        var currentRolesArray = {!! json_encode(auth()->user()->currentRolesArray()) !!};
        var permissionsHeader = '{{ trans('HCCore::acl_access.permissions') }}'

        jQuery(function () {

            var boxes = [];

            $.each(permissions, function (permissionName, permissionsArray) {

                var headerHtml = getHeaderPart(permissionName);
                var tableHeaderHtml = getThPart(roles);
                var tableBodyHtml = getTdPart(permissionsArray, roles);
                var footerHtml = getFooterPart();

                boxes.push(headerHtml + tableHeaderHtml + tableBodyHtml + footerHtml);
            });

            $.each(boxes, function (i, d) {
                $('#roleList').append(d);
            });


            /**
             * search for roles
             */
            $("#searchInput").on('keyup', filterPermissionsList);
        });

        function updateRole(permissionId, roleId, updateUrl, item) {
            $.ajax({
                url: updateUrl,
                type: 'PUT',
                data: {'permission_id': permissionId, 'role_id': roleId},
                beforeSend: function () {
                    item.attr('disabled', true);
                },
                complete: function () {
                    item.attr('disabled', false);
                },
                success: function (value) {
                    if (value.success) {
                        if (value.message == 'created') {
                            item.prop('checked', true);
                            item.parent().css('background', 'rgba(51, 153, 88, 0.52)').delay(1000).animate({backgroundColor: 'transparent'}, 'slow');
                        }
                        else if (value.message == 'deleted') {
                            item.prop('checked', false);
                            item.parent().css('background', 'rgba(255, 165, 0, 0.52)').delay(1000).animate({backgroundColor: 'transparent'}, 'slow');
                        }
                    } else if (value.success === false) {
                        item.parent().css('background', 'rgba(255, 0, 0, 0.52)').delay(1000).animate({backgroundColor: 'transparent'}, 'slow');
                    }
                }
            });
        }

        function getHeaderPart(permissionName) {

            return '<div class="box">' +
                '<div class="box-header">' +
                '<h3 class="box-title">' + permissionName + '</h3>' +
                '<div class="box-tools pull-right">' +
                '<button type="button" class="btn btn-box-tool" data-widget="collapse">' +
                '<i class="fa fa-minus"></i>' +
                '</button>' +
                '</div>' +
                '</div>' +
                '<div class="box-body table-responsive no-padding">' +
                '<table class="table table-striped table-hover">' +
                '<tbody>';
        }

        function getFooterPart() {
            return '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';
        }

        function getThPart(roles) {

            var html = '<tr>' +
                '<th>' + permissionsHeader + '</th>';

            $.each(roles, function (i, v) {
                html += '<th class="to-the-right">' + v.role + '</th>';
            });

            return html + '</tr>';
        }

        function getTdPart(permissionsArray, roles) {

            var html = '';

            $.each(permissionsArray, function (key, permission) {

                var actionName = permission.action.indexOf('_list') !== -1 ? '<td style="text-decoration: underline">' + permission.action + '</td>' : '<td>' + permission.action + '</td>';

                html += '<tr>' +
                    actionName;

                $.each(roles, function (roleKey, roles) {
                    var checked = "";

                    $.each(roles.permissions, function (key2, rPermissionId) {
                        if (permission.id === rPermissionId) {
                            checked = "checked";
                        }
                    });

                    if (roles.slug === 'project-admin' && jQuery.inArray(roles.slug, currentRolesArray) > -1) {

                        html += '<td class="to-the-right">' +
                            '<input type="checkbox" onclick="updateRole(\'' + permission.id + '\',\'' + roles.id + '\',\'' + updateUrl + '\', jQuery(this))" id="ck' + permission.id + '"' + checked + ' disabled/>' +
                            '</td>';
                    } else {
                        html += '<td class="to-the-right">' +
                            '<input type="checkbox" onclick="updateRole(\'' + permission.id + '\',\'' + roles.id + '\',\'' + updateUrl + '\', jQuery(this))" id="ck' + permission.id + '"' + checked + '/>' +
                            '</td>';
                    }
                });

                html += '</tr>'
            });

            return html;
        }

        function filterPermissionsList() {
            //split the current value of searchInput
            var data = this.value.split(" ");

            //create a jquery object of the rows
            var box = $("#roleList").find(".box");

            if (this.value == "") {
                box.show();
                return;
            }

            //hide all the boxes
            box.hide();

            //Recusively filter the jquery object to get results.
            box.filter(function (i, v) {
                var $t = $(this);
                for (var d = 0; d < data.length; ++d) {
                    if ($t.is(":contains('" + data[d] + "')")) {
                        return true;
                    }
                }
                return false;
            }).show();

        };


    </script>
@endsection

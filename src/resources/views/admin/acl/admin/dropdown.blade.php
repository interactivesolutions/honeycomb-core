{{--@if(app()->environment() == 'new')--}}

{{--<!-- User Account Menu -->--}}
{{--<li class="dropdown user user-menu">--}}
{{--<!-- Menu Toggle Button -->--}}
{{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
{{--<!-- The user image in the navbar-->--}}
{{--<img src="{{ auth()->user()->photo() }}" class="user-image" alt="User Image"/>--}}
{{--<!-- hidden-xs hides the username on small devices so only the image appears. -->--}}
{{--<span class="hidden-xs">{{ auth()->user()->name() }}</span>--}}
{{--</a>--}}
{{--<ul class="dropdown-menu">--}}
{{--<!-- The user image in the menu -->--}}
{{--<li class="user-header">--}}
{{--<img src="{{ auth()->user()->photo() }}" class="img-circle" alt="User Image"/>--}}
{{--<p>--}}
{{--{{ auth()->user()->personal->full_name }}--}}
{{--<small>{{ auth()->user()->memberSince() }}</small>--}}
{{--</p>--}}
{{--</li>--}}

{{--<!-- Menu Footer-->--}}
{{--<li class="user-footer">--}}
{{--<div class="pull-left">--}}
{{--<a href="#" class="btn btn-default btn-flat">{{ trans('HCACL::users.admin.menu.profile') }}</a>--}}
{{--</div>--}}
{{--<div class="pull-right">--}}
{{--<a href="{{ route('auth.logout') }}"--}}
{{--class="btn btn-default btn-flat">{{ trans('HCACL::users.admin.menu.logout') }}</a>--}}
{{--</div>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</li>--}}

{{--@else--}}

<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
    </a>
    <ul class="dropdown-menu dropdown-user">
        <li>
            <a href="{{ route('auth.logout') }}">
                <i class="fa fa-sign-out fa-fw"></i> {{ trans('HCACL::users.admin.menu.logout') }}
            </a>
        </li>
    </ul>
</li>

{{--@endif--}}
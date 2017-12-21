@extends('HCACL::auth.layout')

@section('content')

    <div class="login-page" style="position: fixed; top:0; left: 0; bottom: 0; right: 0;">

        <div class="login-box">
            <div class="login-logo">
                <b>{{ config('app.name') }}</b>
            </div>

            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">{{ trans('HCACL::users.login.title') }}</p>

                <div id="login-form"></div>

                {{--<div class="social-auth-links text-center"></div>--}}

                <a href="{{ route('users.password.remind')}}">{{ trans('HCACL::users.passwords.forgot_password') }}</a><br>

                @if( isset($config['registration_enabled']))
                    <a href="{{ route('auth.register') }}"
                       class="text-center">{{ trans('HCACL::users.register.title') }}</a>
                @endif

            </div>
        </div>

    </div>

@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            HCService.FormManager.initialize();
            var form = HCService.FormManager.createForm({
                'structureURL': '{{  route('public.api.form-manager', 'users-login-new')}}',
                'divID': '#login-form'
            });
        });
    </script>
@endsection

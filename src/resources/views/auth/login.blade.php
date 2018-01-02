@extends('HCCore::auth.master')

@section('content')

    <div class="login-page" style="position: fixed; top:0; left: 0; bottom: 0; right: 0;">

        <div class="login-box">
            <div class="login-logo">
                <b>{{ config('app.name') }}</b>
            </div>

            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">{{ trans('HCCore::user.login.title') }}</p>

                <div id="login-form"></div>

                <a href="{{ route('users.password.remind')}}">{{ trans('HCCore::user.passwords.forgot_password') }}</a><br>

                <div class="social-auth-links text-center">
                    @if(config('services.facebook.client_id'))
                        <a href="{{ route('login.socialite', 'facebook')}}">{!! trans('HCCore::user.socialite.facebook') !!}</a>
                        <br>
                    @endif

                    @if(config('services.github.client_id'))
                        <a href="{{ route('login.socialite', 'github')}}">{!! trans('HCCore::user.socialite.github') !!}</a>
                        <br>
                    @endif

                    @if(config('services.bitbucket.client_id'))
                        <a href="{{ route('login.socialite', 'bitbucket')}}">{!! trans('HCCore::user.socialite.bitbucket') !!}</a>
                        <br>
                    @endif

                    @if(config('services.google.client_id'))
                        <a href="{{ route('login.socialite', 'google')}}">{!! trans('HCCore::user.socialite.google') !!}</a>
                        <br>
                    @endif

                    @if(config('services.twitter.client_id'))
                        <a href="{{ route('login.socialite', 'twitter')}}">{!! trans('HCCore::user.socialite.twitter') !!}</a>
                        <br>
                    @endif

                    @if(config('services.linkedin.client_id'))
                        <a href="{{ route('login.socialite', 'linkedin')}}">{!! trans('HCCore::user.socialite.linkedin') !!}</a>
                        <br>
                    @endif
                </div>

                @if( isset($config['registration_enabled']))
                    <a href="{{ route('auth.register') }}"
                       class="text-center">{{ trans('HCCore::user.register.title') }}</a>
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
                'structureURL': '{{  route('frontend.api.form-manager', 'user-login-new')}}',
                'divID': '#login-form'
            });
        });
    </script>
@endsection

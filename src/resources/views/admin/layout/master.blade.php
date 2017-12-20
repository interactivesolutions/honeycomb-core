<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} {{ trans('HCTranslations::core.administration') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    @include('HCNewCore::admin.assets.css')

    @yield('css')
</head>
<body class="skin-blue">
<div class="wrapper">

    @if(app()->environment() == "local")
        <div style="position: absolute">
            {!! trans('HCTranslations::core.dev_env') !!}
        </div>
    @endif

    @include('HCNewCore::admin.partials.header')

    @include('HCNewCore::admin.partials.sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                @yield('content-header')
                <small>@yield('content-description')</small>
            </h1>
        </section>

        <section class="content">

            @yield('content')

        </section>
    </div>

    @include('HCNewCore::admin.partials.footer')

    @include('HCNewCore::admin.partials.right-sidebar')

</div>

{{-- js include --}}
@include('HCNewCore::admin.assets.js')

<script>
    {{-- TODO read from cache --}}
        HCService.FRONTENDLanguage = HCService.CONTENTLanguage = '{{ app()->getLocale() }}';
</script>

<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@yield('scripts')

@include('HCNewCore::admin.partials.sidebar-filter-js')
</body>
</html>

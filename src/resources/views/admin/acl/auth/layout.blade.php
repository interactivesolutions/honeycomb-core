<!DOCTYPE html>
<html>
<head>
    <title>Main</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @include('HCCoreUI::css.global')
    @include('HCCoreUI::css.core')

</head>
<body>

@yield('content')

@include('HCCoreUI::js.global')
@include('HCCoreUI::js.shared')
@include('HCCoreUI::js.form')

@yield('scripts')

<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>
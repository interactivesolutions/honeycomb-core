<!DOCTYPE html>
<html>
<head>
    <title>Main</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @include('HCCore::css.global')
    @include('HCCore::css.core')

</head>
<body>

@yield('content')

@include('HCCore::js.global')
@include('HCCore::js.shared')
@include('HCCore::js.form')

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

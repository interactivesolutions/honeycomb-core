<!DOCTYPE html>
<html>
<head>
    <title>Main</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @include('HCNewCore::css.global')
    @include('HCNewCore::css.core')

</head>
<body>

@yield('content')

@include('HCNewCore::js.global')
@include('HCNewCore::js.shared')
@include('HCNewCore::js.form')

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

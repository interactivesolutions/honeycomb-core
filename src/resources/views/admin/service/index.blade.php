@extends('HCCoreUI::admin.layout')

@if ( isset( $config['title'] ) &&  ! empty($config['title']))
    @section('content-header',  $config['title'] )
@endif

@section('content')

    <div id="here-comes-list"></div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            new HCService.List.SimpleList({
                div: '#here-comes-list',

                @include('HCCoreUI::admin.partials.list-settings')
            });
        });
    </script>

    @if(config('hc.google_map_api_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('hc.google_map_api_key') }}&libraries=places"></script>
    @endif

@endsection

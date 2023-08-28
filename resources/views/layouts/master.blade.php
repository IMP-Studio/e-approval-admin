<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="UTF-8">
        <link href="{{ asset('images/IMP-location.png') }}" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Dashboard - Absensi IMP-Studio</title>
        <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            /* width */
            ::-webkit-scrollbar {
            width: 10px;
            }

            /* Track */
            ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
            }

            /* Handle */
            ::-webkit-scrollbar-thumb {
            background: #175a72;
            border-radius: 10px;
            }

            #success-notification-content {
                position: fixed !important;
                right: 0!important;
                z-index: 999 !important;
                opacity: 1;
                transition: 3s ease-in-out;
            }
            #success-notification-content.hidden {
                opacity: 0;
                transition: 3s ease-in;
            }
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
            }

            /* Firefox */
            input[type=number] {
            -moz-appearance: textfield;
            }
            input[type="date"]::-webkit-calendar-picker-indicator {
                display: none;
            }
            .month-item-name{
                background-color: #232D45!important
            }
            .month-item-year{
                background-color: #232D45!important
            }
        </style>

        @stack('css')

        @include('layouts.flash-message')
    <body class="py-5 md:py-0">

        @include('layouts.mobile-menu')

        @include('layouts.top-bar')

        <div class="flex overflow-hidden">
            @include('layouts.side-nav')
            @yield('content')
        </div>

        @stack('js')

        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
        <script src="{{ asset('dist/js/app.js') }}"></script>
    </body>
</html>

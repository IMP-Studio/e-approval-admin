<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'light') }}">
    <head>
        <meta charset="UTF-8">
        <link href="{{ asset('images/IMP-location.png') }}" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Dashboard - Absensi IMP-Studio</title>
        <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
         <!-- Include Toastr JS -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

         <!-- Include jQuery -->
         <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
         <!-- Include Toastr CSS -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

         <!-- Add DataTables CDN -->
         <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
         <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

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
                background-color: #175a72!important;
                color: white!important
            }
            .month-item-year{
                background-color: #175a72!important;
                color: white!important
            }
            .content{
                padding-bottom: 100px !important;
            }

            table.dataTable.no-footer {
                border-bottom: 0 !important;
            }
            table.dataTable thead th, table.dataTable thead td, table.dataTable tfoot th, table.dataTable tfoot td {
                text-align: center;
            }
        </style>
        <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
        <script>
            window.OneSignalDeferred = window.OneSignalDeferred || [];
            OneSignalDeferred.push(async function(OneSignal) {
                await OneSignal.init({
                    appId: "d0249df4-3456-48a0-a492-9c5a7f6a875e",
                    serviceWorkerParam: { scope: "/dist/js/onesignal/" },
                    serviceWorkerPath: "/dist/js/onesignal/OneSignalSDKWorker.js",
                });
            });
        </script>

        @stack('css')

        @include('layouts.flash-message')
    <body class="py-5 md:py-0">

        @include('layouts.mobile-menu')

        @include('layouts.top-bar')

        <div class="flex overflow-hidden">
            @include('layouts.side-nav')
            @yield('content')
            <div class="dark-mode-switcher cursor-pointer shadow-md fixed bottom-0 right-0 box dark:bg-dark-2 border rounded-full w-40 h-12 flex items-center justify-center z-50 mb-10 mr-10">
                <div class="mr-4 text-gray-700 dark:text-gray-300">Dark Mode</div>
                <div class="dark-mode-switcher__toggle border"></div>
            </div>
        </div>

        @stack('js')
        <script>
            const html = document.documentElement;
            const darkModeSwitcher = document.querySelector('.dark-mode-switcher');

            // Fungsi untuk mengambil dan menetapkan preferensi tema dari local storage
            function applyThemePreference() {
                const theme = localStorage.getItem('theme') || 'light';
                html.classList.remove('light', 'dark'); // Hapus keduanya terlebih dahulu
                html.classList.add(theme); // Tambahkan class sesuai preferensi

                darkModeSwitcher.classList.toggle('active', theme === 'dark');
                darkModeSwitcher.querySelector('.dark-mode-switcher__toggle').classList.toggle('dark-mode-switcher__toggle--active', theme === 'dark');
            }

            // Fungsi untuk mengubah preferensi tema dan menyimpannya di local storage
            function toggleTheme() {
                const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                applyThemePreference();
            }

            // Menambahkan event listener ke elemen dark mode switcher
            darkModeSwitcher.addEventListener('click', toggleTheme);

            // Menerapkan preferensi tema saat halaman dimuat
            applyThemePreference();
        </script>

        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
        <script src="{{ asset('dist/js/app.js') }}"></script>
    </body>
</html>

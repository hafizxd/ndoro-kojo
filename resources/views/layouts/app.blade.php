<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME', 'Ndorokojo') }} - @yield('title')</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/ndorokojo_logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/ndorokojo_logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/ndorokojo_logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/ndorokojo_logo.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/ndorokojo_logo.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('assets/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script>
        document.documentElement.classList.add('navbar-horizontal');
    </script>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">

    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>

    <link href="{{ asset('assets/vendors/choices/choices.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('assets/vendors/leaflet/leaflet.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/leaflet.markercluster/MarkerCluster.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/leaflet.markercluster/MarkerCluster.Default.css') }}" rel="stylesheet">

    <!-- Toastr -->
    <script type="text/javascript" src="{{ asset('assets/vendors/toastr/toastr.js') }}"></script>
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/vendors/toastr/toastr.css') }}" />

    <!-- iOS overlay -->
    <script src="{{ asset('assets/vendors/overlay/iosOverlay.js') }}"></script>
    <script src="{{ asset('assets/vendors/overlay/spin.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendors/overlay/iosOverlay.css') }}">
    <script src="{{ asset('assets/vendors/overlay/modernizr-2.0.6.min.js') }}"></script>
    <script type="text/javascript">
        function createOverlay(screenText) {
            var target = document.createElement("div");
            document.body.appendChild(target);
            var opts = {
                lines: 13, // The number of lines to draw
                length: 11, // The length of each line
                width: 5, // The line thickness
                radius: 17, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                color: '#FFF', // #rgb or #rrggbb
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: 'auto', // Top position relative to parent in px
                left: 'auto' // Left position relative to parent in px
            };
            var spinner = new Spinner(opts).spin(target);
            gOverlay = iosOverlay({
                text: screenText,
                /*duration: 2e3,*/
                spinner: spinner
            });
        }

        var gOverlay;
    </script>

    <script type="text/javascript">
        var gModalContactCallback = "";

        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-full-width",
                "onclick": null,
                "showDuration": "15000",
                "hideDuration": "15000",
                "timeOut": "15000",
                "extendedTimeOut": "15000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
            }
        });
    </script>

    @stack('style')
</head>


<body>
    <main class="main" id="top">
        @include('layouts.top-nav')

        <div class="content">
            <div class="pb-5">
                @yield('content')
            </div>
        </div>
    </main>




    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendors/choices/choices.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lodash/lodash.min.js') }}"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="{{ asset('assets/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>

    @stack('script')
</body>

</html>

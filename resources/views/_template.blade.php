<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 11]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->
        <!-- Meta -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="description" content="DashboardKit is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
        <meta name="keywords" content="DashboardKit, Dashboard Kit, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Free Bootstrap Admin Template">
        <meta name="author" content="DashboardKit ">

        <!-- Favicon icon -->
        <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/x-icon">

        <!-- font css -->
        <link rel="stylesheet" href="{{ asset('fonts/feather.css') }}">
        <!-- <link rel="stylesheet" href="{{ asset('fonts/fontawesome.css') }}"> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="{{ asset('fonts/material.css') }}">

        <!-- vendor css -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}" id="main-style-link">

        <!-- bootstrap -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- 
            Phone formatter
            https://intl-tel-input.com/
        -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.3.4/build/css/intlTelInput.css">

        <!-- ckeditor -->
        <!-- <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css" /> -->

        @yield('header_imports')

        <script src="{{ asset('js/utils.js') }}"></script>

        <style>
            /* fix z-index layer order */
            .datepicker { 
                z-index: 9999 !important;
            }

            /* hide tiny-mce "upgrade button" & "status-bar" */
            .tox .tox-promotion-link{
                visibility: hidden;
            }
            .tox-statusbar{
                visibility: hidden;
            }
        </style>
    </head>
    <body class="">

        <!-- [ Pre-loader ] start -->
        <div class="loader-bg">
            <div class="loader-track">
                <div class="loader-fill"></div>
            </div>
        </div>
        <!-- [ Pre-loader ] End -->

        <!-- [ Mobile header ] start -->
        <div class="pc-mob-header pc-header">
            <div class="pcm-logo">
                <!-- <img src="{{ asset('images/logo.svg') }}" alt="" class="logo logo-lg"> -->
                <img src="{{ asset('images/bbt_logo_medium2.svg') }}" style="height:50px;" alt="" class="logo logo-lg">
            </div>
            <div class="pcm-toolbar">
                <a href="#!" class="pc-head-link" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <!-- <a href="#!" class="pc-head-link" id="headerdrp-collapse">
                    <i data-feather="align-right"></i>
                </a> -->
                <a href="#!" class="pc-head-link" id="header-collapse">
                    <i data-feather="more-vertical"></i>
                </a>
            </div>
        </div>
        <!-- [ Mobile header ] End -->

        @include('_includes._nav_menu')

        @include('_includes._header')
        
        <!-- <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center w-100">
            <div id="toast" class="toast align-items-center text-bg-primary border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                <div class="toast-body">
                    Hello, world! This is a toast message.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div> -->
        <div class="toast-container position-fixed bottom-50 p-3">
            <div id="toast" class="toast text-bg-primary" role="alert" data-bs-delay="3000" aria-live="assertive" aria-atomic="true">
                <!-- <div class="toast-header">
                    <img src="..." class="rounded me-2" alt="...">
                    <strong class="me-auto">Bootstrap</strong>
                    <small>11 mins ago</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div> -->
                <div class="toast-body">
                    Hello, world! This is a toast message.
                </div>
            </div>
        </div>
        <style>
            .toast {
                left: 50%;
                position: fixed;
                transform: translate(-50%, 0px);
                z-index: 9999;
            }
        </style>

        <!-- [ Main Content ] start -->
        <div class="pc-container">
            <div class="pcoded-content">

                @include('_includes._breadcrumb')

                <!-- [ Main Content ] start -->
                <div class="row">
                    @yield('content')
                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
        <!-- [ Main Content ] end -->

        <!-- Warning Section start -->
        <!-- Older IE warning message -->
        <!--[if lt IE 11]>
            <div class="ie-warning">
                <h1>Warning!!</h1>
                <p>You are using an outdated version of Internet Explorer, please upgrade
                <br/>to any of the following web browsers to access this website.
                </p>
                <div class="iew-container">
                    <ul class="iew-download">
                        <li>
                            <a href="http://www.google.com/chrome/">
                                <img src="assets/images/browser/chrome.png" alt="Chrome">
                                <div>Chrome</div>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.mozilla.org/en-US/firefox/new/">
                                <img src="assets/images/browser/firefox.png" alt="Firefox">
                                <div>Firefox</div>
                            </a>
                        </li>
                        <li>
                            <a href="http://www.opera.com">
                                <img src="assets/images/browser/opera.png" alt="Opera">
                                <div>Opera</div>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.apple.com/safari/">
                                <img src="assets/images/browser/safari.png" alt="Safari">
                                <div>Safari</div>
                            </a>
                        </li>
                        <li>
                            <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                                <img src="assets/images/browser/ie.png" alt="">
                                <div>IE (11 & above)</div>
                            </a>
                        </li>
                    </ul>
                </div>
                <p>Sorry for the inconvenience!</p>
            </div>
        <![endif]-->
        <!-- Warning Section Ends -->
        
        <!-- Required Js -->
        <script src="{{ asset('js/vendor-all.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <!-- <script src="{{ asset('js/plugins/bootstrap.min.js') }}"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('js/plugins/feather.min.js') }}"></script>
        <script src="{{ asset('js/pcoded.min.js') }}"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script> -->
        <!-- <script src="{{ asset('js/plugins/clipboard.min.js') }}"></script> -->
        <!-- <script src="{{ asset('js/uikit.min.js') }}"></script> -->

        <!-- Apex Chart -->
        <script src="{{ asset('js/plugins/apexcharts.min.js') }}"></script>

        <!-- custom-chart js -->
        <!-- <script src="{{ asset('js/pages/dashboard-sale.js') }}"></script> -->

        <!-- TOAST -->
        <script>
            const toastDom = document.getElementById('toast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastDom);

            function toast_info(message){
                $('#toast').removeClass('text-bg-danger');
                $('#toast').addClass('text-bg-primary');

                $('#toast').find('.toast-body').html(message);

                toast.show();
            }

            function toast_error(message){
                $('#toast').removeClass('text-bg-primary');
                $('#toast').addClass('text-bg-danger');

                $('#toast').find('.toast-body').html(message);

                toast.show();
            }
        </script>

        @if (session()->has('_message'))
            <script>toast_info("{{ session('_message') }}");</script>
        @endif
        @if (session()->has('_error'))
            <script>toast_error("{{ session('_error') }}");</script>
        @endif

        <!-- phone input intellisense -->
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.3.4/build/js/intlTelInput.min.js"></script>
        
        @yield('footer_imports')

        <script src="{{ url('/public/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ url('/public/ckeditor/adapters/jquery.js') }}"></script>
        <style>
            .cke_notifications_area{
                visibility: hidden !important;
            }
        </style>
        <script>
            $('textarea.editor').ckeditor();
        </script>

        @yield('post_script')

        <script>
            // $(document).ready(function () {
            //     console.log('.datepicker ready');
            //     $('.datepicker').datepicker({
            //         format: {
            //             toDisplay: 'dd-MM-yyyy',
            //         }
            //     });
            // });
        </script>
    </body>
</html>

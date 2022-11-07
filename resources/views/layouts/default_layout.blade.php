<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="pages/ico/60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="pages/ico/76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="pages/ico/120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="pages/ico/152.png">
    <link rel="icon" type="image/x-icon" href="favicon.ico"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="Meet pages - The simplest and fastest way to build web UI for your dashboard or app."
          name="description"/>
    <meta content="Ace" name="author"/>
    <link href="/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="screen"/>
    <link class="main-stylesheet" href="/pages/css/pages.css" rel="stylesheet" type="text/css"/>
    <!-- Please remove the file below for production: Contains demo classes -->
    <link class="main-stylesheet" href="/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link class="main-stylesheet" href="/custom/css/custom.css" rel="stylesheet" type="text/css"/>
    @yield("header-styles")
    <title>{{ config('app.name', 'EduApp') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="fixed-header ">
<!-- BEGIN SIDEBPANEL-->
@include("components.sidebarPrimary")
<!-- END SIDEBAR -->
<!-- END SIDEBPANEL-->
<!-- START PAGE-CONTAINER -->
<div class="page-container">

    <!-- START HEADER -->
@include("components.header")
<!-- END HEADER -->
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper">
        <!-- START PAGE CONTENT -->
        <div class="content @if(\Illuminate\Support\Facades\Request::is('/'))sm-gutter @endif">
            <!-- START JUMBOTRON -->
        @if(!\Illuminate\Support\Facades\Request::is('/'))
            @include("components.breadcrumbs",["page"=>$title??''])
        @endif
        <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div
                class=" container-fluid  @if(\Illuminate\Support\Facades\Request::is('/'))padding-25 sm-padding-10 @else container-fixed-lg @endif">
                <!-- BEGIN PlACE PAGE CONTENT HERE -->
                <div id="app">
                    @yield('content')
                </div>
                <!-- END PLACE PAGE CONTENT HERE -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        @include("components.footer")
    </div>
</div>
<!-- END PAGE CONTAINER -->
<!-- BEGIN VENDOR JS -->
<script src="/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
<script src="/assets/plugins/liga.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="/assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="/assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-actual/jquery.actual.min.js"></script>
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="/assets/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="/assets/plugins/classie/classie.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
@yield("footer-scripts")
<!-- END VENDOR JS -->
<!-- BEGIN CORE TEMPLATE JS -->
<!-- BEGIN CORE TEMPLATE JS -->
<script src="/assets/js/pages.js"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="/assets/js/scripts.js" type="text/javascript"></script>
<script src="/custom/js/blockUI.min.js" type="text/javascript"></script>
<script src="/custom/js/jquery_helpers.js" type="text/javascript"></script>
<script src="/custom/js/simpleDialog.js" type="text/javascript"></script>
<!-- END PAGE LEVEL JS -->
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<!-- END PAGE LEVEL JS -->
</body>
</html>

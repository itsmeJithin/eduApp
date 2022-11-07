<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta charset="utf-8"/>
    <title>Pages - Admin Dashboard UI Kit - Lock Screen</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"/>
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
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen"/>
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css"/>
    <!-- Please remove the file below for production: Contains demo classes -->
    <link class="main-stylesheet" href="assets/css/style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
        window.onload = function () {
            // fix for windows 8
            if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
                document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
        }
    </script>
</head>
<body class="fixed-header ">
<div class="login-wrapper ">
    <!-- START Login Background Pic Wrapper-->
    <div class="bg-pic">
        <!-- START Background Caption-->
        <div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
            <h1 class="semi-bold text-white">
                Meet pages - The simplest and fastest way to build web UI for your dashboard or app.</h1>
            <p class="small">
                Our beautifully-designed UI Framework come with hundreds of customizable features. Every Layout is just
                a starting point. ©2019-2020 All Rights Reserved. Pages® is a registered trademark of Revox Ltd.
            </p>
        </div>
        <!-- END Background Caption-->
    </div>
    <!-- END Login Background Pic Wrapper-->
    <!-- START Login Right Container-->
    <div class="login-container bg-white">
        <div class="p-l-50 p-r-50 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
            <img src="assets/img/logo-48x48_c.png" alt="logo" data-src="assets/img/logo-48x48_c.png"
                 data-src-retina="assets/img/logo-48x48_c@2x.png" width="48" height="48">
            <h2 class="p-t-25">Get Started <br/> with your Dashboard</h2>
            <p class="mw-80 m-t-5">Sign in to your account</p>
            <!-- START Login Form -->
            <form id="form-login" class="p-t-15" role="form" action="{{ route('checkCredentials') }}" method="POST">
            @csrf
            <!-- START Form Control-->
                <div class="form-group form-group-default @error('staff_email') has-error @enderror">
                    <label>Login</label>
                    <div class="controls">
                        <input id="email" type="email" class="form-control" name="staff_email"
                               value="{{ old('staff_email') }}" required autocomplete="email" autofocus/>
                    </div>
                </div>
                @error('staff_email')
                <label id="staff_email_error" class="error" role="alert" for="staff_email">
                    {{$message }}
                </label>
                @enderror
            <!-- END Form Control-->
                <!-- START Form Control-->
                <div class="form-group form-group-default  @error('password') has-error @enderror">
                    <label>Password</label>
                    <div class="controls">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               required autocomplete="current-password"/>
                    </div>
                </div>
                @error('password')
                <label id="staff_email_error" class="error" role="alert" for="staff_email">
                    {{$message }}
                </label>
            @enderror
            <!-- START Form Control-->
                <div class="row">
                    <div class="col-md-6 no-padding sm-p-l-10">
                        <div class="form-check">
                            <input type="checkbox" value="1" id="checkbox1">
                            <label for="checkbox1">Remember me</label>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-end">
                        <button aria-label="" class="btn btn-primary btn-lg m-t-10" type="submit">Sign in</button>
                    </div>
                </div>
                @if (Route::has('password.request'))
                    <div class="m-b-5 m-t-30">
                    <a class="normal" href="{{ route('password.request') }}">
                        {{ __('Lost your password??') }}
                    </a>
                    </div>
                @endif
                <div>
                    <a href="#" class="normal">Not a member yet? Signup now.</a>
                </div>
                <!-- END Form Control-->
            </form>
            <!--END Login Form-->
            <div class="pull-bottom sm-pull-bottom">
                <div class="m-b-30 p-r-80 sm-m-t-20 sm-p-r-15 sm-p-b-20 clearfix">
                    <div class="col-sm-9 no-padding m-t-10">
                        <p class="small-text normal hint-text">
                            ©2019-2020 All Rights Reserved. Pages® is a registered trademark of Revox Ltd. <a href="">Cookie
                                Policy</a>, <a href=""> Privacy and Terms</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Login Right Container-->
</div>
<!-- END OVERLAY -->
<!-- BEGIN VENDOR JS -->
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
<script src="assets/plugins/liga.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END VENDOR JS -->
<script src="pages/js/pages.min.js"></script>
<script>
    $(function () {
        $('#form-login').validate()
    })
</script>
</body>
</html>

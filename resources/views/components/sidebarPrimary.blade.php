<nav class="page-sidebar" data-pages="sidebar">
    <!-- BEGIN SIDEBAR MENU TOP TRAY CONTENT-->
    <div class="sidebar-overlay-slide from-top" id="appMenu">
        <div class="row">
            <div class="col-xs-6 no-padding">
                <a href="#" class="p-l-40"><img src="/assets/img/demo/social_app.svg" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 no-padding">
                <a href="#" class="p-l-10"><img src="/assets/img/demo/email_app.svg" alt="socail">
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="#" class="p-l-40"><img src="/assets/img/demo/calendar_app.svg" alt="socail">
                </a>
            </div>
            <div class="col-xs-6 m-t-20 no-padding">
                <a href="#" class="p-l-10"><img src="/assets/img/demo/add_more.svg" alt="socail">
                </a>
            </div>
        </div>
    </div>
    <!-- END SIDEBAR MENU TOP TRAY CONTENT-->
    <!-- BEGIN SIDEBAR MENU HEADER-->
    <div class="sidebar-header">
        <img src="/assets/img/logo_white.png" alt="logo" class="brand" data-src="/assets/img/logo_white.png"
             data-src-retina="assets/img/logo_white_2x.png" width="78" height="22">
        <div class="sidebar-header-controls">
            <button aria-label="Toggle Drawer" type="button"
                    class="btn btn-icon-link invert sidebar-slide-toggle m-l-20 m-r-10" data-pages-toggle="#appMenu">
                <i class="pg-icon">chevron_down</i>
            </button>
            <button aria-label="Pin Menu" type="button"
                    class="btn btn-icon-link invert d-lg-inline-block d-xlg-inline-block d-md-inline-block d-sm-none d-none"
                    data-toggle-pin="sidebar">
                <i class="pg-icon"></i>
            </button>
        </div>
    </div>
    <!-- END SIDEBAR MENU HEADER-->
    <!-- START SIDEBAR MENU -->
    <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
            <li class="m-t-20 ">
                <a href="{{route("home")}}">
                    <span class="title">Home</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">home</i></span>
            </li>
            <li class="">
                <a href="{{route("course")}}">
                    <span class="title">Courses</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">inbox</i></span>
            </li>
            <li class="">
                <a href="{{route("classes")}}">
                    <span class="title">Classes</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">note</i></span>
            </li>
            <li class="">
                <a href="{{route("syllabuses")}}">
                    <span class="title">Syllabuses</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">effects</i></span>
            </li>
            <li class="">
                <a href="javascript:;">
                    <span class="title">Class Groups</span>
                    <span class="arrow"></span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">shapes</i></span>
                <ul class="sub-menu">
                    <li class="">
                        <a href="{{route("classGroups")}}">Create Class Groups</a>
                        <span class="icon-thumbnail"><i class="pg-icon">CG</i></span>
                    </li>
                    <li class="">
                        <a href="{{route("designClassSubjects")}}">Design Group Subjects</a>
                        <span class="icon-thumbnail"><i class="pg-icon">GS</i></span>
                    </li>
                    <li class="">
                        <a href="{{route("manageDemoTopics")}}">Manage Demo Topics</a>
                        <span class="icon-thumbnail"><i class="pg-icon">DT</i></span>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="javascript:;">
                    <span class="title">Exams</span>
                    <span class="arrow"></span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">menu_level</i></span>
                <ul class="sub-menu">
                    <li class="">
                        <a href="{{route("exams")}}">Manage Exams</a>
                        <span class="icon-thumbnail"><i class="pg-icon">ME</i></span>
                    </li>
                    <li class="">
                        <a href="{{route("questionPool")}}">Manage Questions</a>
                        <span class="icon-thumbnail"><i class="pg-icon">MQ</i></span>
                    </li>
                    <li class="">
                        <a href="{{route("manageExamModes")}}">Manage Exam Modes</a>
                        <span class="icon-thumbnail"><i class="pg-icon">EM</i></span>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="{{route("users")}}">
                    <span class="title">Users</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">users</i></span>
            </li>
            <li class="">
                <a href="{{route("doubts")}}">
                    <span class="title">Answer Doubts</span>
                </a>
                <span class="icon-thumbnail"><i class="pg-icon">shapes_alt</i></span>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</nav>

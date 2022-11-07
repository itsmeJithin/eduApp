<div class="header ">
    <!-- START MOBILE SIDEBAR TOGGLE -->
    <a href="#" class="btn-link toggle-sidebar d-lg-none pg-icon btn-icon-link" data-toggle="sidebar">
        menu</a>
    <!-- END MOBILE SIDEBAR TOGGLE -->
    <div class="">
        <div class="brand inline   ">
            <img src="/assets/img/logo.png" alt="logo" data-src="/assets/img/logo.png"
                 data-src-retina="assets/img/logo_2x.png" width="78" height="22">
        </div>
    </div>
    <div class="d-flex align-items-center">
        <!-- START User Info-->
        <div class="dropdown pull-right d-lg-block d-none">
            <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" aria-label="profile dropdown">
              <span class="thumbnail-wrapper d32 circular inline">
      					<img src="/assets/img/profiles/avatar.jpg" alt="" data-src="/assets/img/profiles/avatar.jpg"
                             data-src-retina="assets/img/profiles/avatar_small2x.jpg" width="32" height="32">
      				</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown" role="menu">
                <a href="#" class="dropdown-item"><span>Signed in as <br/><b>{{ Auth::user()->staff_name }}</b></span></a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">Your Profile</a>
                <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>
</div>

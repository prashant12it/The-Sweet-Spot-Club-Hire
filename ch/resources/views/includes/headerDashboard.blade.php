<header class="navbar navbar-fixed-top navbar-shadow">
      <div class="navbar-branding">
        <a class="navbar-brand" href="{{url('/dashboard')}}">
          <h3 class="dashboard-text-logo">TSS Golf Hire</h3>
        </a>
        <span id="toggle_sidemenu_l" class="ad ad-lines"></span>
      </div>
      <ul class="nav navbar-nav navbar-right">
      <li class="dropdown menu-merge">
          <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
            <img src="{{ URL::asset('theme/assets/img/avatars/default_profile_image.jpg') }}" alt="avatar" class="mw30 br64">
            <span class="hidden-xs pl15"> {{ Auth::user()->name }} </span>
            <span class="caret caret-tp hidden-xs"></span>
          </a>
          <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
            
            <li class="list-group-item">
              <a href="{{url('/changepassword')}}" class="animated animated-short fadeInUp">
                <span class="fa fa-gear"></span> Change Password </a>
            </li>
            <li class="dropdown-footer">
              <a href="{{route('logout')}}" class="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <span class="fa fa-power-off pr5"></span> Logout </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </li>
          </ul>
        </li>
      </ul>
    </header>
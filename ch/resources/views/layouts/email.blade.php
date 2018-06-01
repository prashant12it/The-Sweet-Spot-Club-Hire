<!DOCTYPE html>
<html>
<body class="dashboard-page">
  <div id="main">
      <header class="navbar navbar-fixed-top navbar-shadow">
      <div class="navbar-branding">
        <a class="navbar-brand" href="{{url('/')}}">
          <h3 class="dashboard-text-logo">Golf Club Hire</h3>
        </a>
        <span id="toggle_sidemenu_l" class="ad ad-lines"></span>
      </div>
    </header>
    <section id="content_wrapper">
      @yield('content')
      <footer id="content-footer" class="affix">
        <div class="row">
          <div class="col-md-6">
            <span class="footer-legal">© 2017 Golf Club Hire</span>
          </div>
          
        </div>
      </footer>
    </section>
  </div>
</body>

</html>
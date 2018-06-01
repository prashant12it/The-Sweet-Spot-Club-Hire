<!DOCTYPE html>
<html>

<head>
@include('includes.front_partner.head')
</head>
<body class="partner-dashboard-page">
<div id="loader"></div>
  <div id="main">
      @include('includes.front_partner.post_header')
      <section id="content_wrapper">
        @yield('content')
        @include('includes.front_partner.post_footer')
      </section>
  </div>
    @include('includes.front_partner.footerScript')
</body>
</html>
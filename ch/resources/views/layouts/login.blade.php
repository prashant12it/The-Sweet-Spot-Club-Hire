<!DOCTYPE html>
<html>

<head>
@include('includes.head')
</head>
<body class="external-page sb-l-c sb-r-c login">

  <!-- Start: Main -->
  <div id="main" class="animated fadeIn">

    <!-- Start: Content-Wrapper -->
    <section id="content_wrapper">

      <!-- begin canvas animation bg -->
      @include('includes.background')

      <!-- Begin: Content -->
      <section id="content">

        @yield('content')

      </section>
      <!-- End: Content -->

    </section>
    <!-- End: Content-Wrapper -->

  </div>
  <!-- End: Main -->
@include('includes.footer')
</body>

</html>
<!DOCTYPE html>
<html>

<head>
@include('includes.head')
</head>
<body class="dashboard-page">
<div id="loader"></div>
   <!-- Start: Main -->
  <div id="main">


      <!-- Dashboard Header -->
      @include('includes.headerDashboard')

<!-- Left Menu -->
      @include('includes.sidebar')
      <!-- Begin: Content -->
      <section id="content_wrapper">

        @yield('content')
@include('includes.dashboardFooter')
      </section>
      <!-- End: Content -->
    <!-- End: Content-Wrapper -->

  </div>
  <!-- End: Main -->
@include('includes.footerScript')
</body>

</html>
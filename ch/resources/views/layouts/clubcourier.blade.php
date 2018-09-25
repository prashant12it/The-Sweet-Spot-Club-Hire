<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.clubcourier.head')
</head>
<body class="frontend-body">
<div id="loader"></div>
<!-- Dashboard Header -->
@include('includes.clubcourier.header')

@yield('content')
<!-- End: Content -->
@include('includes.clubcourier.footer')
<!-- End: Main -->
@include('includes.clubcourier.footerScript')
</body>

</html>
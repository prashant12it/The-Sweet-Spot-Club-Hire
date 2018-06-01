<!DOCTYPE html>
<html lang="en">
<head>
	@include('includes.front_partner.head')
</head>
<body class="frontend-body" id="partner-page">
<div id="loader"></div>
    <!-- Dashboard Header -->
    @include('includes.front_partner.header')

    @yield('content')
    {{--@include('includes.front_partner.newsletter')--}}
    <!-- End: Content -->
    @include('includes.front_partner.footer')
    <!-- End: Main -->
    @include('includes.front_partner.footerScript')
</body>

</html>
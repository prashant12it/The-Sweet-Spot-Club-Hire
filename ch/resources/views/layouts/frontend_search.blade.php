<!DOCTYPE html>
<html lang="en">
<head>
	@include('includes.frontend.head')
</head>
<body class="frontend-body">
<div id="loader"></div>


	<!-- Dashboard Header -->
	@include('includes.frontend.header')

	<!-- Left Menu -->
	@include('includes.frontend.search_banner')
	<!-- Begin: Content -->

		@yield('content')
		{{--@include('includes.frontend.newsletter')--}}
	<!-- End: Content -->
@include('includes.frontend.footer')
<!-- End: Main -->
@include('includes.frontend.footerScript')
</body>

</html>
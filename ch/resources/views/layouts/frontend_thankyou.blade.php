<!DOCTYPE html>
<html lang="en">
<head>
	@include('includes.frontend.head')
</head>
<body class="frontend-body">
<!-- Google Code for Convert to sale Conversion Page -->
<script type="text/javascript">
	/* <![CDATA[ */
    var google_conversion_id = 825329640;
    var google_conversion_label = "F6DUCOGDrIABEOiPxokD";
    var google_remarketing_only = false;
	/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt=""
			 src="//www.googleadservices.com/pagead/conversion/825329640/?label=F6DUCOGDrIABEOiPxokD&amp;guid=ON&amp;script=0"/>;
	</div>
</noscript>

<div id="loader"></div>


	<!-- Dashboard Header -->
	@include('includes.frontend.header')

	<!-- Left Menu -->
	@include('includes.frontend.thankyou_banner')
	<!-- Begin: Content -->

		@yield('content')
		{{--@include('includes.frontend.newsletter')--}}
	<!-- End: Content -->
@include('includes.frontend.footer')
<!-- End: Main -->
@include('includes.frontend.footerScript')
</body>

</html>
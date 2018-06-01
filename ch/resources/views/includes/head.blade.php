
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<title>TSS Golf Hire -  {{ $title or 'Making golf travel simple' }}</title>
<meta name="keywords" content="HTML5 Bootstrap 3 Admin Template UI Theme" />
<meta name="description" content="AbsoluteAdmin - A Responsive HTML5 Admin UI Framework">
<meta name="author" content="AbsoluteAdmin">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="_token" content="{!! csrf_token() !!}"/>
<!-- Font CSS (Via CDN) -->
<link rel='stylesheet' type='text/css' href="{{ url('http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700') }}">

<!-- Theme CSS -->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/assets/skin/default_skin/css/theme.css') }}">

<link rel="stylesheet" type="text/css" href="{{URL::asset('theme/assets/skin/default_skin/css/bootstrap.min.css')}}"/> 

  <!-- Admin Forms CSS -->
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/assets/admin-tools/admin-forms/css/admin-forms.css') }}"><link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/vendor/plugins/select2/css/core.css') }}">
<!-- Custom css written by Prashant Singh -->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('theme/assets/skin/default_skin/css/prashant.css') }}">
<!-- Favicon -->
<link rel="shortcut icon" href="{{ URL::asset('theme/assets/img/favicon.ico') }}">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>

 <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
 <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
 <![endif]-->
<script src="{{ URL::asset('theme/vendor/jquery/jquery-1.11.1.min.js') }}"></script>
<script>
    var siteRelPath = '{{(getenv('APP_ENV') == 'local'?'/':(getenv('APP_ENV') == 'live'?'/shop/':'/'))}}';
</script>
<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1694502390845935'); // Insert your pixel ID here.
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1694502390845935&ev=PageView&noscript=1"
    /></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->


<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-106749553-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments)};
    gtag('js', new Date());

    gtag('config', 'UA-106749553-1');
</script>
<script type="text/javascript" src="//script.crazyegg.com/pages/scripts/0071/5594.js" async="async"></script>


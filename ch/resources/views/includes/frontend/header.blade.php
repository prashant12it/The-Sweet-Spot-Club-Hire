<header id="header" data-spy="affix" data-offset-top="197" class="custom-header">
    <div class="container">
        <!-- Static navbar -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>

                    </button>
                    <a class="navbar-brand" href="{{url('../')}}"><img src="{{ URL::asset('frontend/images/logo.png') }}" alt="The Sweet spot club hire"></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="{{url('../')}}">{{__('Home')}}</a></li>
                        <li><a href="{{url('../about-us/')}}">{{__('About Us')}}</a></li>
                        <li><a href="{{url('../portfolio/our-equipment/')}}">{{__('Equipment')}}</a></li>
                        <li><a href="{{url('../')}}">{{__('Hire')}}</a></li>
                        <li><a href="{{url('../main-blog/')}}">{{__('News')}}</a></li>
                        <li><a href="{{url('../media/')}}">{{__('Media')}}</a></li>
                        <li><a href="{{url('../affiliate/')}}">{{__('Affiliates')}}</a></li>
                        <li><a href="{{url('clubsearch/')}}"><i class="fa fa-shopping-cart"></i> </a></li>
                    </ul>
                    <ul class="nav navbar-right top-social-links">
                        <li><a href="https://twitter.com/tssclubhire?lang=en"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.facebook.com/tssclubhire/"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.instagram.com/tssclubhire/?hl=en"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.youtube.com/channel/UCSE484gOqNWs5l2cVkuPEAw"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
    </div>
</header>
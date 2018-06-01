<header class="header">
        <!-- Static navbar -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#myNavbar"><span class="sr-only">Toggle navigation</span></button>
                    <a class="navbar-brand" href="{{url('/partner/dashboard')}}"><img src="{{ URL::asset('frontend/images/logo.png') }}" alt="The Sweet spot club hire"></a>    
                </div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav">
						<li class="{{($title == 'Dashboard'?'active':'')}}">
                                                    <a href="{{url('/partner/dashboard')}}">Dashboard</a>
                                                </li>
						<li class="{{($title == 'Banners'?'active':'')}}">
                                                    <a href="{{url('/partner/banners_list')}}">Banners</a>
                                                </li>
					</ul>
				</div>
                <div id="navbar" class="pull-right">
					<div class="dropdown">
					  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-user" aria-hidden="true"></i>{{Session::get('partner_credn.partnerName')}}
					  <span class="caret"></span></button>
					  <ul class="dropdown-menu">
						 <li><a href="{{url('/partner/profile')}}"><i class="fa fa-user" aria-hidden="true"></i>Profile</a></li>
                        <li><a href="{{url('/partner/change_password')}}"><i class="fa fa-cog" aria-hidden="true"></i>Change Password</a></li>
                        <li><a href="{{url('/partner/logout')}}"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></li>
					  </ul>
					</div>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
</header>

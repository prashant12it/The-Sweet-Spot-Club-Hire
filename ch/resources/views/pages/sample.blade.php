@extends('layouts.dashboard')

@section('content')


<header id="topbar" class="alt">
        <div class="topbar-left">
          <ol class="breadcrumb">
            <li class="crumb-active">
              <a href="{{url('/dashboard')}}"><span class="glyphicon glyphicon-home"></span> Dashboard</a>
            </li>
          </ol>
        </div>
      </header>
<section id="content">
  <div class="page-heading">
    <h2>Welcome to Golf Club Hire admin panel.</h2>
  </div>
</section>
@endsection
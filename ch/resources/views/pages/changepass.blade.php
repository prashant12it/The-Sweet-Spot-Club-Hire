@extends('layouts.dashboard')

@section('content')


<header id="topbar" class="alt">
        <div class="topbar-left">
          <ol class="breadcrumb">
            <li class="crumb-active">
              <a href="{{url('/dashboard')}}">Dashboard</a>
            </li>
            <li class="crumb-icon">
              <a href="{{url('/dashboard')}}">
                <span class="glyphicon glyphicon-home"></span>
              </a>
            </li>
            <li class="crumb-link">
              <a href="{{url('/dashboard')}}">Home</a>
            </li>
            <li class="crumb-trail">Change Password</li>
          </ol>
        </div>
      </header>
<section id="content">
  <div class="page-heading">

  @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

    <form id="form-change-password" role="form" method="POST" action="{{ url('/changepassword') }}" novalidate class="form-horizontal">
  <div class="col-md-9">             
    <label for="current-password" class="col-sm-4 control-label">Current Password</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
        <input type="password" class="form-control" value="{{old('current-password')}}" id="current-password" name="current-password" placeholder="Password">
        @if ($errors->has('current-password'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                    </span>
                                @endif
      </div>
    </div>
    <label for="password" class="col-sm-4 control-label">New Password</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input type="password" class="form-control" value="{{old('password')}}" id="password" name="password" placeholder="Password">
        @if ($errors->has('password'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
      </div>
    </div>
    <label for="password_confirmation" class="col-sm-4 control-label">Re-enter Password</label>
    <div class="col-sm-8">
      <div class="form-group">
        <input type="password" class="form-control" value="{{old('password_confirmation')}}" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password">
        @if ($errors->has('password_confirmation'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
      </div>
    </div>
    <div class="col-sm-offset-4 col-sm-8">
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-clipboard pull-right">Change Password</button>
      </div>
    </div>
  </div>
  
    <div class="form-group">
    
  </div>
</form>
  </div>
</section>
@endsection
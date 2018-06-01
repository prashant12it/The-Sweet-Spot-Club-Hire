@extends('layouts.email')

@section('content')

<section id="content">
  <div class="page-heading">
      <p>Hi {{$accountDetails['name']}},</p><br/><br/>
      <p>Welcome to Golf Club Hire. Your new account successfully created. You can login with following credentials:</p><br/><br/>
      <p>Email : {{$accountDetails['email']}}</p><br/>
      <p>Password : {{$accountDetails['password']}}</p><br/>
      <p>Login Link : <a href="{{$accountDetails['login_link']}}">Click here to login</a></p><br/><br/><br/><br/>
      
      
      <p>Kind Regards,<br/>Golf Club Hire</p>
  </div>
</section>
@endsection
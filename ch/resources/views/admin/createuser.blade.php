<div class="col-md-6 form-control">
	<form method="POST" action="/admin">
	{!! csrf_field() !!}
		<input type="text" placeholder="Name" name="name" id="name" />
		<input type="email" placeholder="Email" name="email" id="email" />
		<input type="password" placeholder="Password" name="password" id="password" />
		<input type="submit" name="register" id="register" value="SignUp" />
	</form>	
</div>
@extends('shared.layoutwithoutnavbar')
@section('title', 'Login')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4">
		</div>
		<!-- LOGIN -->
		<div id="loginpanel" class="col-md-4">
			<form action="{{ url('login') }}" method="post">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="">
					<img src="public/img/bpjs_logo600x140.png" class="img-responsive" alt="Cinque Terre">
				</div>
				</br></br>
				<div class="input-group">				
					<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
					<input name="username" type="text" class="form-control input-lg" placeholder="Username" aria-describedby="basic-addon1" required>
				</div>
				</br>
				<div class="input-group">
					<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
					<input name="password" type="password" class="form-control input-lg" placeholder="Password" aria-describedby="basic-addon1" required>
				</div>
				</br>
				<div class="text-right">
					<a href="" class="">Forgot Password</a>
				</div>
				</br>
				<div class="text-center">
					<button type="submit" class="btn btn-success btn-block btn-lg">LOGIN</button>
				</div>
				@if (!empty($loginMessage))
					</br></br>
					<div class="alert alert-danger" role="alert">{{ $loginMessage }}</div>
                @endif
			</form>			
		</div>
		<div class="col-md-4">
		</div>
	</div>
</div>
@endsection
<!DOCTYPE html>
<html class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{@$settings->title}}</title>
	<meta name="keywords" content="{{@$settings->meta_keyword}}">
	<meta name="title" content="{{@$settings->meta_title}}">
     <link rel="icon" href="{{ url('public/uploads')}}/{{@$settings->icon}}" type="image/x-icon" />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="{{ URL::asset('public/assets/admin/css/style.css')}}" rel="stylesheet"> 
	
	<style> 
	 button.btn.btn-primary.btn-block:hover {
       color: white !important;
     }
    </style>
</head>
<body class="h-100" style="background: linear-gradient(1deg, #0ca8d3, #922e4b);">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
										<a href="{{url('/')}}"><img src="{{ url('public/uploads')}}/{{@$settings->logo}}" height="50px" ></a>  
									</div>
                                    <h4 class="text-center mb-4 text-white">Admin Login</h4>
                                    <form method="POST"  id="comment_form" action="{{ route('admin.login.submit') }}"
                                         class="user frm-single">
										 @csrf

                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Email</strong></label>
                                            <input type="email" name="email" value="" autocomplete="off" class="form-control" placeholder="Type Email Address" required>
                                        </div>
										<div class="form-group">
											<label class="mb-1 text-white"><strong>Password</strong></label>
											<input type="password" autocomplete="off" value="" class="form-control" name="password" placeholder="Type Password" required>
										</div>
										@if($errors->has('email'))
											<div class="alert alert-danger alert-dismissable">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												{{ $errors->first('email') }}
											</div>
										@endif
										
										@if($errors->has('password'))
											<div class="alert alert-danger alert-dismissable">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												{{ $errors->first('password') }}
											</div>
										@endif
										
										@if(Session::has('error'))
											<div class="alert alert-danger alert-dismissable">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												{{ Session::get('error') }}
											</div>
										@endif
										<div class="form-group"> 
											<button type="submit" class="btn btn-primary btn-block" style="background:linear-gradient(1deg, #9a282e, #4656a1);border: 1px;"  name="button">Login</button>
										</div> 
									</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <script src="{{ URL::asset('public/assets/backend/vendor/global/global.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/backend/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/backend/js/custom.min.js')}}"></script>
    <script src="{{ URL::asset('public/assets/backend/js/deznav-init.js')}}"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

 

</body>
</html>
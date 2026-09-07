@extends('admin.includes.masterpage-admin')

@section('content')
<style>
img#adminimg {
    height: 300px;
    with: 300px;
    padding: 10px;
    border: 2px solid gray;
    margin-bottom: 15px;
}
</style>
<div class="d-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">{{$admin->name}}</h1>
	<a href="{!! url('admin/dashboard') !!}" class="d-sm-inline-block btn btn-sm btn-primary -sm"><i class="fa fa-arrow-left fa-sm text-white"></i> Back</a>
</div>
<div class="clearfix"></div>
	<div id="response" class="col-md-12">
		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{{ Session::get('message') }}
			</div>
		@endif
	</div>

	<div id="response" class="col-md-12">
		@if(Session::has('error'))
			<div class="alert alert-danger alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				{{ Session::get('error') }}
			</div>
		@endif
	</div>
	
	
<form method="POST" action="{{ route('update_admin') }}" data-parsley-validate=""  action="cabstore" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	 
	 <input type="hidden" name="id" value="{{$admin->id}}">
	  <div class="row small-spacing">
		
		<div class="col-lg-8 col-xs-12">
		
		<div class="card  mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">  Name : </h6></div>
			<div class="card-body">
                <input type="text" name="name"  required autocomplete="off" class="form-control" value="{{$admin->name}}" placeholder="Type Name">
		</div>
		</div>
		
		
		<div class="card  mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Email Address : </h6></div>
			<div class="card-body">
                <input type="text" name="email"  required autocomplete="off" class="form-control" value="{{$admin->email}}" placeholder="Type Email Address">
		     </div>
		</div> 
		
		<div class="card ">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Phone Number : </h6></div>
			<div class="card-body">
                <input type="text" name="phone"  required  autocomplete="off" class="form-control" value="{{$admin->phone}}" placeholder="Type Phone Number">
		</div>
		</div> 
			<br>
		<br>
		<br>
	     <div class="form-action-col text-right">
	     	 <button name="addProduct_btn" type="submit" class="btn btn-success add-product_btn">Update Profile</button>
		
         </div>
  
  
		
		</div>
 
	 <div class="col-lg-4 col-xs-12">
		 <div class="card  mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Image : </h6></div>
				<div class="card-body">
				   <img id="adminimg" src="{{url('/')}}/public/uploads/admin/{{$admin->photo}}" alt="">
                    <input class="hidden"  accept="image/*" onchange="readURL(this)" id="uploadFile" name="photo" type="file">
                    <button name="admin_image_btn" id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div>
			 						
		   </div>
		</div> 
	</div>
	

		
 
</form>


@stop
@section('footer')
 <script>
        function uploadclick(){
            $("#uploadFile").click();
            $("#uploadFile").change(function(event) {
                $("#uploadTrigger").html($("#uploadFile").val());
            });
        }

        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#adminimg').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
 
@stop

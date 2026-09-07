@extends('admin.includes.masterpage-admin')

@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">About </h1>
	    <a href="{!! url('admin/dashboard') !!}" class="d-sm-inline-block btn btn-warning shadow-sm ml-auto btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left fa-sm text-white"></i></span><span class="text">Back</span></a>
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
<form method="POST" action="{!! url('admin/update_about') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row small-spacing">
		<div class="col-lg-8 col-xs-12">
		<div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">  Heading  : </h6></div>
			<div class="card-body">
                <input type="text" name="about_heading"  autocomplete="off" class="form-control" value="{{$data->about_heading}}" placeholder="Heading">
		</div>
		</div>
		  
		
		<div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold"> Description : </h6></div>
			<div class="card-body">
               <textarea class="form-control summernote" rows="10" cols="30" name="about_description">{{$data->about_description}}</textarea> 
		</div>
		</div> 
		 
		</div>
 
	 <div class="col-lg-4 col-xs-12">
		 <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Image : </h6></div>
				<div class="card-body">
				@if($data->about_image)
					 <img id="adminimg" src="{{url('/')}}/public/uploads/{{$data->about_image}}" >
				 @else
			         <img id="adminimg" src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL(this)" id="uploadFile" name="photo" type="file">
                    <button name="admin_image_btn" id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div>
				<div class="form-group text-right">
						<button name="addProduct_btn" type="submit" class="btn btn-success add-product_btn">Update </button>
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

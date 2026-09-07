@extends('admin.includes.masterpage-admin')

@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800"> Home Banner Section</h1>
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
<form method="POST" action="{!! url('admin/update_home_settings') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row small-spacing">
		<div class="col-lg-8 col-xs-12">
		<div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">  Home Title </h6></div>
			<div class="card-body">
                <input type="text" name="home_title"   autocomplete="off" class="form-control" value="{{$data->home_title}}" placeholder="Title">
		</div>
		</div>
		<div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">  Home Heading </h6></div>
			<div class="card-body">
                <input type="text" name="home_heading"  autocomplete="off" class="form-control" value="{{$data->home_heading}}" placeholder="Heading">
		</div>
		</div>
		
		  
		
		<div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold"> Description </h6></div>
			<div class="card-body">
               <textarea class="form-control summernote" rows="10" cols="30" name="home_description">{{$data->home_description}}</textarea> 
		</div>
		</div> 
		 
		</div>
 
	 <div class="col-lg-4 col-xs-12">
		 <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Home Background Image   </h6></div>
				<div class="card-body">
				@if($data->home_background)
					 <img id="adminimg"  class="upload-image"   src="{{url('/')}}/public/uploads/{{$data->home_background}}" >
				 @else
			         <img id="adminimg"   class="upload-image"  src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL(this)" id="uploadFile" name="home_background" type="file">
                    <button  id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div> 
		   </div>
		   
		    <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">Home Slider Image   </h6></div>
				<div class="card-body">
				@if($data->home_slider)
					 <img id="adminimg1" class="upload-image" src="{{url('/')}}/public/uploads/{{$data->home_slider}}" >
				 @else
			         <img id="adminimg1"   class="upload-image"  src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL1(this)" id="uploadFile1" name="home_slider" type="file">
                    <button   id="uploadTrigger1" onclick="uploadclick1()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div> 
		   </div>  
		</div> 
		
	   <div class="form-action-col text-right">
		<button id="submit" type="submit" class="btn btn-success add-product_btn">Update Record</button>
	   </div>
	
	
 
	</div>
 
</form>


@stop
@section('footer')
 
 
@stop

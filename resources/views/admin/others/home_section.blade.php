@extends('admin.includes.masterpage-admin')

@section('content')
 
<div class="d-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800"> Home Section</h1>
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
<form method="POST" action="{!! url('admin/update_home_section') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row small-spacing">
		<div class="col-lg-8 col-xs-12">
		
		 	<div class="card d-none">
                <div class="card-header">
                    <h4 class="card-title">Main Home Section  </h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" autocomplete="off" class="form-control" name="service_title"  value="{{$data->service_title}}"  placeholder="Enter Title" > 
                    </div>
                    <div class="form-group">
                        <label>Description</label>
						<textarea class="form-control" name="service_description"   placeholder="Enter Description">{{$data->service_description}}</textarea>
                    </div>
                </div>
         </div>
		
		
		<div class="card">
                <div class="card-header">
                    <h4 class="card-title">Home Section 1</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" autocomplete="off" class="form-control" name="service_title1"  value="{{$data->service_title1}}"  placeholder="Enter Title" > 
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="service_description1"   placeholder="Enter Description">{{$data->service_description1}}</textarea>
                    </div>
                </div>
         </div>
		 
		 <div class="card">
                <div class="card-header">
                     <h4 class="card-title">Home Section 2</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" autocomplete="off" class="form-control" name="service_title2"  value="{{$data->service_title2}}"  placeholder="Enter Title"  > 
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="service_description2"   placeholder="Enter Description">{{$data->service_description2}}</textarea>
                    </div>
                </div>
         </div>
		 
		 <div class="card">
                <div class="card-header">
                      <h4 class="card-title">Home Section 3</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" autocomplete="off" class="form-control" name="service_title3"  value="{{$data->service_title3}}"  placeholder="Enter Title" > 
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                         <textarea class="form-control" name="service_description3"   placeholder="Enter Description">{{$data->service_description3}}</textarea>
                    </div>
                </div>
         </div>
		 
		 
		
		
		  
	 
		 
		</div>
 
	 <div class="col-lg-4 col-xs-12">
		 <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">1st Section Image   </h6></div>
				<div class="card-body">
				@if($data->service_image1)
					 <img id="adminimg"  class="upload-image"   src="{{url('/')}}/public/uploads/{{$data->service_image1}}" >
				 @else
			         <img id="adminimg"   class="upload-image"  src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL(this)" id="uploadFile" name="service_image1" type="file">
                    <button  id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div> 
		   </div>
		   
		    <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">2nd Section Image   </h6></div>
				<div class="card-body">
				@if($data->service_image2)
					 <img id="adminimg1" class="upload-image" src="{{url('/')}}/public/uploads/{{$data->service_image2}}" >
				 @else
			         <img id="adminimg1"   class="upload-image"  src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL1(this)" id="uploadFile1" name="service_image2" type="file">
                    <button   id="uploadTrigger1" onclick="uploadclick1()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
				</div> 
		   </div> 
          <div class="card shadow mb-4">
			<div class="card-header py-3"><h6 class="m-0 font-weight-bold">3rd Section Image   </h6></div>
				<div class="card-body">
				@if($data->service_image3)
					 <img id="adminimg2" class="upload-image" src="{{url('/')}}/public/uploads/{{$data->service_image3}}" >
				 @else
			         <img id="adminimg2"   class="upload-image"  src="{{url('/')}}/public/uploads/no-image.png">
				@endif
                    <input class="hidden"  accept="image/*" onchange="readURL2(this)" id="uploadFile2" name="service_image3" type="file">
                    <button   id="uploadTrigger2" onclick="uploadclick2()" type="button" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
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

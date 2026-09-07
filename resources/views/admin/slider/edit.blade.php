@extends('admin.includes.masterpage-admin')

@section('content')
 <div class="page-titles">
	<div class="d-flex align-items-center justify-content-between">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="{!! url('admin/category') !!}">Category List</a></li>
			<li class="breadcrumb-item active">Category Deatils</li>
		</ol>
  
    </div>
</div>
<div id="response" class="col-md-12">
    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
    @endif 
</div>
 <form method="POST" action="{{route('slider_update')}}" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="row">
	 	<div class="col-xl-8">
	 		<div class="card">
	 			<div class="card-header">
                    <h4 class="card-title">Details</h4>
                </div>
                <div class="card-body">
                	<div class="form-row">
                		 
                        <div class="form-group col-md-12">
                            <label> Title</label> 
							<input type="hidden" name="id" value="{{$data->id}}" required>
                            <input type="text" autocomplete="off" class="form-control" name="name" value="{{$data->name}}" placeholder="Enter Title" required>
                        </div> 

						<div class="form-group col-md-12">
                            <label> Page Link</label> 
                            <input type="text" autocomplete="off" class="form-control" name="page_link" value="{{$data->page_link}}"  placeholder="Enter Page Link" required>
                        </div>  

                         <div class="form-group col-md-12">
                            <label>category Description</label>
                            <textarea rows="6" class="form-control summernote" name="description" id="description" placeholder="Enter Description">{{$data->description}}</textarea>
                        </div> 

                	</div>
                </div>
            </div> 
			<div class="form-action-col">
				<div class="row d-flex align-items-center">
					<div class="col-4">Staus: 
				        <?php if($data->status == "active"){ ?>
				            <span class="badge badge-success">{{ucfirst($data->status)}}</span>
				        <?php } else { ?>
				            <span class="badge badge-warning">{{ucfirst($data->status)}}</span>
				        <?php } ?>
					</div>
					<div class="col-8 text-right">
                        <button name="addProduct_btn" id="submit" type="submit" class="btn btn-success add-product_btn">Update</button>
					</div>
				</div>
			</div>
	 		 
        </div>
        <div class="col-xl-4">
        	<div class="card">
	 			<div class="card-header">
                    <h4 class="card-title">  Image</h4>
                </div>
                <div class="card-body">
                	@if($data->image)
                		<div class="text-center mb-3"><img id="adminimg" src="{{url('/')}}/public/uploads/slider/{{$data->image}}" style="max-height: 150px;">
                		 
                	@else
						<div class="text-center mb-3"><img id="adminimg" src="{{ url('public/uploads/no-1.jpg')}}" style="max-height: 150px;">
                		 
					@endif
                	<div class="text-center">
	                    <input class="d-none"  accept="image/*"  onchange="readURL(this)" id="uploadFile" name="image" type="file">
	                    <button name="admin_image_btn" id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-primary btn-sm add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
                    </div>
                </div>
	 		</div> 
			 <div class="card"> 
                 <div class="card-body"> 
                    <div class="form-group d-flex align-items-center justify-content-space-between">
                        <label class="option-label">Status  </label>
                        <div class="radio-toggle">
                            <input type="radio" id="publish" name="status"  <?php if($data->status == "active"){ echo "checked"; }?> value="active">
                            <label for="publish">Publish</label>
                            <input type="radio" id="inactive" name="status"  <?php if($data->status == "inactive"){ echo "checked"; }?> value="inactive" >
                            <label for="inactive">Inactive</label>
                        </div>
                    </div> 
                </div>
	 		</div>
			
        </div>
    </div>
	 
 </form>
<div style="height: 100px;"></div>
                 
					
					
					

@stop
@section('footer')
 
 <script type="text/javascript">
/* Encode string to slug */
function convertToSlug(str) {
    document.getElementById("meta_title").value =   str;
    //replace all special characters | symbols with a space
    str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ').toLowerCase();

    // trim spaces at start and end of string
    str = str.replace(/^\s+|\s+$/gm, '');

    // replace space with dash/hyphen
    str = str.replace(/\s+/g, '-');
    document.getElementById("slug").value = str;
    //return str;
} 
  
</script>
@stop

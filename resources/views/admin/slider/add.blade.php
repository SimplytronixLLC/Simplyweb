@extends('admin.includes.masterpage-admin')

@section('content')
 
 
<div id="response">
    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
    @endif 
</div>
<form method="POST" action="{!! route('slider_save') !!}" data-parsley-validate="" class="form-horizontal" enctype="multipart/form-data">
     @csrf
	 <div class="row">
	 	<div class="col-xl-8">
	 		<div class="card">
	 			<div class="card-header">
                    <h4 class="card-title">Slider Details</h4>
                </div>
                <div class="card-body">
                	<div class="form-row">  

                		<div class="form-group col-md-12">
                			<label>Title</label> 
                              <input type="text" autocomplete="off"  value="" class="form-control" name="name"  placeholder="Enter Title" required>
                        </div>

                        <div class="form-group col-md-12">
                			<label>Page Link</label> 
                              <input type="text" autocomplete="off"  value="" class="form-control" name="page_link"  placeholder="Enter Page Link" required>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <label>  Description</label>
                            <textarea rows="6" class="form-control " name="description" id="description" placeholder="Enter Description"></textarea> 
                        </div>  
                	</div>
                </div> 
	 		</div>
              
  
            <div class="form-action-col text-right">
                <button name="addProduct_btn" id="submit" type="submit" class="btn btn-success add-product_btn">Save</button>
            </div>

 
	 	</div>
	 	<div class="col-xl-4">
	 		<div class="card">
	 			<div class="card-header">
                    <h4 class="card-title">  Image</h4>
                </div>
                <div class="card-body">
                	<div class="text-center mb-3"><img id="adminimg" src="{{ url('public/uploads/no-image.png')}}" style="max-height: 150px;">
                	  <div class="text-center">
	                    <input class="d-none" required accept="image/*"  onchange="readURL(this)" id="uploadFile" name="image" type="file">
	                    <button name="admin_image_btn" id="uploadTrigger" onclick="uploadclick()" type="button" class="btn btn-primary btn-sm add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
                    </div>
                </div>
	 		</div> 
	 		<div class="card"> 
                 <div class="card-body"> 
                    <div class="form-group d-flex align-items-center justify-content-space-between">
                        <label class="option-label">Status  </label>
                        <div class="radio-toggle">
                            <input type="radio" id="publish" name="status" checked="" value="active">
                            <label for="publish">Publish</label>
                            <input type="radio" id="inactive" name="status" value="inactive" >
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
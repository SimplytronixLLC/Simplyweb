@extends('admin.includes.masterpage-admin')

@section('content')
<div class="page-titles">
    <div class="d-flex align-items-center justify-content-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
            <li class="breadcrumb-item">Pages</li>
            <li class="breadcrumb-item active">Disclaimer</li>
        </ol>
    </div>
</div>
@if(Session::has('message'))
	<div class="alert alert-success alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		{{ Session::get('message') }}
	</div>
@endif
	
<form method="POST" action="{!! url('admin/update_disclaimer') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row justify-content-center">
		<div class="col-lg-8">
		<div class="card">			
            <div class="card-header">
                <h4 class="card-title">Page Details</h4>
            </div>
            <div class="card-body">
	            <div class="form-group">
	                <label>Page Name</label>
	                <input type="text" name="disclaimer_title"  autocomplete="off" class="form-control" value="{{$data->disclaimer_title}}" placeholder="Heading">
	            </div>
	            <div class="form-group">
	                <label>Page Content</label>
	                <textarea class="form-control summernote" rows="10" cols="30" name="disclaimer_description">{{$data->disclaimer_description}}</textarea> 
	            </div>
	            <div class="form-group text-center">
	                <button name="addProduct_btn" type="submit" class="btn btn-success add-product_btn">Update </button>
	            </div>
			</div>		 
		</div> 
	</div> 
</form>


@stop
@section('footer')
 
@stop

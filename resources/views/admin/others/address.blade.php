@extends('admin.includes.masterpage-admin')

@section('content')
 
 <div class="page-titles">
    <div class="d-flex align-items-center justify-content-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
            <li class="breadcrumb-item active">Website Settings</li>
        </ol>
    </div>
</div>
@if(Session::has('message'))
	<div class="alert alert-success alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		{{ Session::get('message') }}
	</div>
@endif
<form method="POST" action="{!! url('admin/update_address') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row justify-content-center">
		<div class="col-lg-6">		
			<div class="card">
				<div class="card-header"><h4 class="title">Website Settings</h4></div>
				<div class="card-body">
			        <div class="form-group">
			            <label>Website Name</label>
		                <input type="text" name="title"  autocomplete="off" class="form-control" value="{{@$data->title}}" placeholder="Site Title">
					</div>
			        <div class="form-group">
			            <label>Website Url</label>
		                <input type="text" name="url"  autocomplete="off" class="form-control" value="{{@$data->url}}" placeholder="Site Url ">
					</div>
			        <div class="form-group">
			            <label>Email</label>
		                <input type="email" name="email"  autocomplete="off" class="form-control" value="{{@$data->email}}" placeholder="Email Address ">
					</div>
			        <div class="form-group">
			            <label>Phone Number</label>
		                <input type="text" name="phone"  autocomplete="off" class="form-control" value="{{@$data->phone}}" placeholder="Phone Number ">
					</div>
			        <div class="form-group">
			            <label>Whatsapp Number</label>
		                <input type="text" name="whatsapp"  autocomplete="off" class="form-control" value="{{@$data->whatsapp}}" placeholder="Whatsapp Number">
					</div>
			        <div class="form-group">
			            <label>Footer Text</label>
		                <input type="text" name="footer_text"  autocomplete="off" class="form-control" value="{{@$data->footer_text}}" placeholder="Footer Text">
					</div>
			        <div class="form-group">
			            <label>Address</label>
		                <textarea class="form-control summernote"  name="address">{{$data->address}}</textarea> 
					</div>
			        <div class="form-group">
			            <label>Footer About Us</label>
		                <textarea class="form-control summernote"  name="footer_about">{{$data->footer_about}}</textarea> 
					</div>
				</div>
			</div>		
			<div class="form-action-col text-right">
				<button name="addProduct_btn" id="submit" type="submit" class="btn btn-success add-product_btn">Update</button>
			</div>
		</div>
	</div>
</form>
<div style="height: 100px;"></div>

@stop
@section('footer')
 
 
@stop

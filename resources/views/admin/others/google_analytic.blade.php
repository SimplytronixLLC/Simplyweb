@extends('admin.includes.masterpage-admin')

@section('content')
<div class="page-titles">
    <div class="d-flex align-items-center justify-content-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
            <li class="breadcrumb-item active">Google Analytics</li>
        </ol>
    </div>
</div> 
@if(Session::has('message'))
	<div class="alert alert-success alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		{{ Session::get('message') }}
	</div>
@endif
<form method="POST" action="{!! url('admin/update_google_analytic') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="row justify-content-center">
		<div class="col-lg-6">		
			<div class="card">
				<div class="card-header"><h4 class="title">Google Analytics Script</h4></div>
				<div class="card-body">
			        <div class="form-group">
			            <!-- <label>Google Analytics Script</label> -->
               			<textarea class="form-control" rows="10" cols="30" name="google_analytics">{{@$data->google_analytics}}</textarea>
               		</div>
			        <div class="form-group text-right">
               			<button name="addProduct_btn" type="submit" class="btn btn-success add-product_btn">Update </button> 
					</div>
				</div>
			</div>		 
		</div> 
	</div> 
</form>


@stop
@section('footer')
  
 
@stop

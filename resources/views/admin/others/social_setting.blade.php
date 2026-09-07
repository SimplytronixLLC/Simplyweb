@extends('admin.includes.masterpage-admin')

@section('content')
<div class="page-titles">
    <div class="d-flex align-items-center justify-content-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{!! url('admin/dashboard') !!}">Dashboard</a></li>
            <li class="breadcrumb-item active">Social Links Setting</li>
        </ol>
    </div>
</div> 
@if(Session::has('message'))
	<div class="alert alert-success alert-dismissable">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		{{ Session::get('message') }}
	</div>
@endif
<form method="POST" action="{!! url('admin/update_social_setting') !!}" action="" class="" enctype="multipart/form-data">
	{{csrf_field()}}

	<div class="row justify-content-center">
		<div class="col-lg-8">		
			<div class="card">
				<div class="card-header"><h4 class="title">Social Links Setting</h4></div>
				<div class="card-body">
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Facebook :</label>
			            <input type="text" class="form-control" name="facebook" value="{{$data->facebook}}" id="facebook" placeholder="http://facebook.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="facebookshow" name="facebook_status" value="1" @if($data->facebook_status == '1')  checked @endif>
			            	<label for="facebookshow">Show</label>
			            	<input type="radio" id="facebookhide" name="facebook_status" value="0" @if($data->facebook_status == '0')  checked @endif>
			            	<label for="facebookhide">Hide</label>
			            </div>
			        </div>
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Twitter :</label>
			            <input type="text" class="form-control" name="twiter" value="{{$data->twiter}}" id="twiter" placeholder="http://twiter.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="twitershow" name="twiter_status" value="1" @if($data->twiter_status == '1')  checked @endif>
			            	<label for="twitershow">Show</label>
			            	<input type="radio" id="twiterhide" name="twiter_status" value="0" @if($data->twiter_status == '0')  checked @endif>
			            	<label for="twiterhide">Hide</label>
			            </div>
			        </div>
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Linkedin :</label>
			            <input type="text" class="form-control" name="lingdin" value="{{$data->lingdin}}" id="lingdin" placeholder="http://linkedin.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="linkedinshow" name="lingdin_status" value="1" @if($data->lingdin_status == '1')  checked @endif>
			            	<label for="linkedinshow">Show</label>
			            	<input type="radio" id="linkedinhide" name="lingdin_status" value="0" @if($data->lingdin_status == '0')  checked @endif>
			            	<label for="linkedinhide">Hide</label>
			            </div>
			        </div>
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Instagram :</label>
			            <input type="text" class="form-control" name="instagram" value="{{$data->instagram}}" id="instagram" placeholder="http://instagram.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="linstagramshow" name="instagram_status" value="1" @if($data->instagram_status == '1')  checked @endif>
			            	<label for="linstagramshow">Show</label>
			            	<input type="radio" id="instagramhide" name="instagram_status" value="0" @if($data->instagram_status == '0')  checked @endif>
			            	<label for="instagramhide">Hide</label>
			            </div>
			        </div>
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Youtube :</label>
			            <input type="text" class="form-control" name="youtube" value="{{$data->youtube}}" id="youtube" placeholder="http://youtube.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="youtubeshow" name="youtube_status" value="1" @if($data->youtube_status == '1')  checked @endif>
			            	<label for="youtubeshow">Show</label>
			            	<input type="radio" id="youtubehide" name="youtube_status" value="0" @if($data->youtube_status == '0')  checked @endif>
			            	<label for="youtubehide">Hide</label>
			            </div>
			        </div>
					<div class="form-group d-flex align-items-center justify-content-space-between">
			            <label class="option-label">Tiktok :</label>
			            <input type="text" class="form-control" name="tiktok" value="{{$data->tiktok}}" id="tiktok" placeholder="http://tiktok.com/">
			            <div class="radio-toggle">
			            	<input type="radio" id="tiktokshow" name="tiktok_status" value="1" @if($data->tiktok_status == '1')  checked @endif>
			            	<label for="tiktokshow">Show</label>
			            	<input type="radio" id="tiktokhide" name="tiktok_status" value="0" @if($data->tiktok_status == '0')  checked @endif>
			            	<label for="tiktokhide">Hide</label>
			            </div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
	<div class="form-action-col text-right">
		<button name="addProduct_btn" id="submit" type="submit" class="btn btn-success add-product_btn">Update Links</button>
	</div> 
</form>


@stop
@section('footer')
  
 
@stop

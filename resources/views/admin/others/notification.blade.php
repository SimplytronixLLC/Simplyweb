@extends('admin.includes.masterpage-admin')
@section('content')
 <!-- Page Heading -->
 
 <div class="d-flex align-items-center justify-content-between mb-4">
	<h1 class="h3 mb-0 text-gray-800">Notifications</h1>
	   
</div>
<div class="clearfix"></div>

<div class="box-content">
<div class="card shadow mb-4 route-tab">
 
	<div class="card-body tab-content" id="routeTabContent">
		<div id="activeRoute" class="tab-pane fade show active" role="tabpanel" aria-labelledby="active-route">

			<div class="table-responsive">
				<!--Active section start -->

				 
				 
				 <table id="notification" class="table table-striped margin-bottom-10 dt-responsive" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="10%"> #</th>
							<th width="10%"> Sender</th>
							<th width="30%"> Heading</th>
							<th width="50%" >Message</th>
						</tr>
					</thead>
					<tbody>
					   @php 
						 $i = 1;
						@endphp
						 @foreach($notification as $data)
						<tr>  
							<td class="td-popular"> {{$i}}</td>
							<td class="td-popular"> <a href="{!! url('admin/user') !!}/{{ $data->sender }}" target="_blank"> {{@$data->email}} </a>  </td>
							<td class="td-popular">  {!!$data->heading!!}   </td>
							<td class="td-popular">  {!!$data->message!!}   </td>
						</tr> 
						@php
						$i++; 
						@endphp
						@endforeach 
					</tbody>
				</table>
 

				<!-- End Active Section-->
			</div>
		</div>
	 </div>
</div>
</div>

 
@stop
@section('footer')
<script>
 
	$(document).ready(function() {
		$.fn.dataTable.ext.errMode = 'throw';
		var dataTable = $("#notification").DataTable();
	     
		});
</script>
@stop

@extends('admin.includes.masterpage-admin')
@section('seo')
    <title> Registered Users   - {{@$settings->title}} </title>
@stop
 
@section('content') 
@include('alerts')
 
<div class="card"> 
	<div class="card-header">
	     <h3>Registered Users </h3>
	     <a href="#" class="btn btn-primary" target="_blank">Add New User</a>    
    </div>
	 
	<div class="card-body tab-content" >
		  

			<div class="table-responsive"> 
				<table id="postlist" class="table table-striped margin-bottom-10 dt-responsive" cellspacing="0" width="100%"> 
					   <thead>
							<tr >
								<th> Id </th>
								<th> First Name </th>
								<th> Last Name </th>
								<th> Email Address </th>
								<th> Contact Number </th> 
								<th> Action </th> 	
							</tr>
						</thead>
						<tbody class="row_position">
							<tr>
							    <td>1</td>
							    <td>John</td>
							    <td>Doe</td>
							    <td>john.doe@example.com</td>
							    <td>+1-555-123-4567</td>
							    <td class="d-flex flex-row gap-2">
				                	<img src="{!! url('public/uploads/visual.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/edit.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/trash.png') !!}" width="23px" height="23px">
				                </td> 
							</tr>
							<tr>
							    <td>2</td>
							    <td>Jane</td>
							    <td>Smith</td>
							    <td>jane.smith@example.com</td>
							    <td>+1-555-234-5678</td>
							    <td class="d-flex flex-row gap-2">
				                	<img src="{!! url('public/uploads/visual.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/edit.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/trash.png') !!}" width="23px" height="23px">
				                </td> 
							</tr>
							<tr>
							    <td>3</td>
							    <td>Michael</td>
							    <td>Johnson</td>
							    <td>michael.johnson@example.com</td>
							    <td>+1-555-345-6789</td>
							    <td class="d-flex flex-row gap-2">
				                	<img src="{!! url('public/uploads/visual.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/edit.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/trash.png') !!}" width="23px" height="23px">
				                </td> 
							</tr>
							<tr>
							    <td>4</td>
							    <td>Emily</td>
							    <td>Brown</td>
							    <td>emily.brown@example.com</td>
							    <td>+1-555-456-7890</td>
							    <td class="d-flex flex-row gap-2">
				                	<img src="{!! url('public/uploads/visual.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/edit.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/trash.png') !!}" width="23px" height="23px">
				                </td> 
							</tr>
							<tr>
							    <td>5</td>
							    <td>David</td>
							    <td>Williams</td>
							    <td>david.williams@example.com</td>
							    <td>+1-555-567-8901</td>
							    <td class="d-flex flex-row gap-2">
				                	<img src="{!! url('public/uploads/visual.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/edit.png') !!}" width="25px" height="25px">
				                	<img src="{!! url('public/uploads/trash.png') !!}" width="23px" height="23px">
				                </td> 
							</tr>
						</tbody>
				</table>
			</div>  
	 </div>
</div>

<div class="modal table-modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content panel-danger">
			<div class="modal-header panel-heading">
				<h3 class="modal-title" id="myModalLabel"><i class="fa fa-exclamation-circle fa-fw"></i> Confirm Delete</h3>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>You are about to delete this Record.</p>
				<h4>Do you want to proceed?</h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>

 
@stop
@section('footer')
  
 
 <script>  

	$('#postlist').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": '_all' }  // Disabling sorting for all columns
        ]
    });
		
</script>
@stop

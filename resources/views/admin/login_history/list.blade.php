@extends('admin.includes.masterpage-admin')
@section('content')
 <!-- Page Heading -->
 
 @include('alerts')
<div class="card">
    <div class="card-header">
    	 <h3> Manage login History</h3>
    </div>
	
	<div class="card-body tab-content" id="routeTabContent">
		<div id="activeRoute" class="tab-pane fade show active" role="tabpanel" aria-labelledby="active-route">
			<div class="table-responsive"> 
			<table id="postlist" class="table table-striped margin-bottom-10 dt-responsive" cellspacing="0" width="100%"> 
					<thead>
							<th width="5%">#</th>
							<th width="30%">Login User</th> 
							<th width="15%">IP Address</th>
							<th width="15%">Browser</th> 
							<th width="10%"> login Time</th> 
							<th width="10%">Last Used</th> 
							<th width="15%">Status</th>   
						</tr>
					</thead>
					<tbody class="row_position">
					</tbody>
			 </table>
			 
					 
				 

				<!-- End Active Section-->
			</div>
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

	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	}); 
	
 $(document).ready(function() {
	  
		//$.fn.dataTable.ext.errMode = 'throw';
		var dataTable = $("#postlist").DataTable({ 
			language: {
				processing: '<i class="fa  fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
			},
			"processing": true,
			"serverSide": true,
			"pageLength": 10,
			"lengthMenu": [[10, 25, 50, 100, 5000], [10, 25, 50, 100, "All"]], 
			dom: 'Blfrtip',  
			// order: [ [4, "DESC"] ],
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 'print'
			],
			"ajax": {
				"url": "<?= route('login_history_list') ?>",
				"dataType": "json",
				"type": "POST",
				'data': function(data) {  
					data._token = '<?= csrf_token() ?>';
				}
			}, 
			
			"drawCallback": function() { 
			},
			// show columns which  we want to show  in data  base table 
			"columns": [{   "data": "id","searchable": false,"orderable": false},
			            {   "data": "user_name","searchable": false,"orderable": false}, 
						{   "data": "ip_address","searchable": false,"orderable": false}, 
						{   "data": "browser","searchable": false,"orderable": false},   
						{   "data": "current_login","searchable": false,"orderable": false},  
						{   "data": "last_login","searchable": false,"orderable": false},   
						{   "data": "status","searchable": false,"orderable": false}] 
		});
   
 });
		
</script>
@stop

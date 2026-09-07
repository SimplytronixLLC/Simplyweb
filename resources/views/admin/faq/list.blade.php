@extends('admin.includes.masterpage-admin')


@section('seo')
    <title> FAQ List  - {{@$settings->title}} </title>
@stop


@section('content')
  
@include('alerts')


<style>
	   #postlist  tr {
			display: inline-grid; 
		}
		#postlist .table-striped tbody tr:nth-of-type(odd) {
			width: 100% !important;
		} 
		#postlist table.dataTable tbody tr { 
			width: 100% !important;
		} 
		#postlist    strong {
			color: #272525 !important;
		}
		#postlist thead {
			display: none !important;
		} 
		 
		.table tbody tr td {
			vertical-align: middle;
			border-color: #ffffff !important;
			padding: 10px 15px 10px 15px;
		} 

</style>
<div class="card">
 

	<div class="card-header">
	<h3> Frequently Asked Questions (FAQs)</h3>  
    	<ul class="nav" id="routetab" role="tablist">  
			<li class="nav-item">
				<select name="type"  id="type" class="form-control">
						<option value="">--Select Type--</option>
						<option value="Journal Publishing">Journal Publishing</option>
						<option value="Book Publishing">Book Publishing</option>    
						<option value="Order Support">Order Support</option>    
						<option value="Editor - Reviewer Support">Editor- Reviewer Support</option>    
						<option value="Advertisement Support"> Advertisement Support</option>    
				</select>
			</li> 
			<li class="nav-item">
			    <select name="status" id="status" class="form-control" >
						<option disabled>Select Status</option> 
						<option value="all">All</option> 
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>            
               </select>
			</li> 
		 </ul>   
		 <a href="{!! url('admin/manage-faq/add') !!}" class="btn  btn-sm btn-success"><span class="icon text-white-50"><i class="fa fa-plus fa-sm text-white"></i></span><span class="text"> Add</span></a>
    </div>
	 
	<div class="card-body tab-content" >
		  

			<div class="table-responsive"> 
				<table id="postlist" class="table table-striped margin-bottom-10 dt-responsive" cellspacing="0" width="100%"> 
					  <!-- <thead>
							<tr style="display: inline-grid;">
								<th width="100%">#</th>
								<th width="100%"> Type</th>
								<th width="100%"> Question</th>
								<th width="100%"> Answer</th>  
								<th width="100%"> Link</th> 
								<th width="100%"> Status</th>
								<th width="100%" >Actions</th>
							</tr>
						</thead>  -->
						<tbody class="row_position">
							
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
				"url": "<?= route('faq_list') ?>",
				"dataType": "json",
				"type": "POST",
				'data': function(data) {   
					data.status = $('#status').val();
					data.type = $('#type').val();
					data._token = '<?= csrf_token() ?>';
				}
			},
			"drawCallback": function() { 
			},
			// show columns which  we want to show  in data  base table
			"columns": [{   "data": "id","searchable": false,"orderable": false}, 
						{   "data": "type","searchable": false,"orderable": false},
						{   "data": "question","searchable": false,"orderable": false},
						{   "data": "answer","searchable": false,"orderable": false}, 
						{   "data": "link","searchable": false,"orderable": false},  
						{   "data": "status","searchable": false,"orderable": false},   
						{ 	"data": "action","searchable": false,"orderable": false }]  
		}); 
         //

		$('#type').change(function() {  
			dataTable.draw();
		});

		$('#status').change(function() {  
			dataTable.draw();
		});



 });
		
</script>
@stop

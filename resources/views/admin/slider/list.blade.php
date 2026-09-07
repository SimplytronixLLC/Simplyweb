@extends('admin.includes.masterpage-admin')
@section('content')
 <!-- Page Heading -->
 
@if(Session::has('message'))
<div class="alert alert-success alert-dismissable">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	{{ Session::get('message') }}
</div>
@endif


<div class="card">
    <div class="card-header">
    	<ul class="nav" id="routetab" role="tablist">
			<li class="nav-item">
				<a class="nav-link status  stats active" data-id="active" data-toggle="tab" href="#" role="tab" aria-controls="activeRoute" aria-selected="true"><h4 class="card-title">Publish</h4></a>
			</li>
			<li class="nav-item">
				<a class="nav-link status" data-id="deactive" data-toggle="tab" href="#"  role="tab" aria-controls="stopRoute" aria-selected="true"><h4 class="card-title">UnPublish</h4></a>
			</li>  
		</ul> 
		 
		<a href="{!! url('admin/slider/add') !!}" class="btn btn-info"><span class="icon text-white-50"><i class="fas fa-plus fa-sm text-white"></i></span><span class="text">Add New Category</span></a>

    </div>
	
	<div class="card-body tab-content" id="routeTabContent">
		<div id="activeRoute" class="tab-pane fade show active" role="tabpanel" aria-labelledby="active-route">

			<div class="table-responsive"> 
			<table id="postlist" class="table table-striped margin-bottom-10 dt-responsive" cellspacing="0" width="100%"> 
					<thead>
						<tr> 
							<th width="5%">#</th>
							<th width="20%">Image</th>
							<th width="30%">Title</th> 
							<th width="20%">Link</th> 
							<th width="15%">Status</th> 
							<th width="10%" >Actions</th>
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
				"url": "<?= route('slider_list') ?>",
				"dataType": "json",
				"type": "POST",
				'data': function(data) { 
					data.status = $('.stats').attr("data-id");
					data._token = '<?= csrf_token() ?>';
				}
			}, 
			
			"drawCallback": function() { 
			},
			// show columns which  we want to show  in data  base table
			"columns": [{   "data": "id","searchable": false,"orderable": false},
			            {   "data": "image","searchable": false,"orderable": false},
						{   "data": "name","searchable": false,"orderable": false},
						{   "data": "page_link","searchable": false,"orderable": false},
						{   "data": "status","searchable": false,"orderable": false},
						{ 	"data": "action","searchable": false,"orderable": false }] 
		});
  
		// on clcik show all qnquiry status wise filter status stats
		$('.status').click(function() {
			$('.status').removeClass('stats')
			$(this).addClass('stats'); 
         	dataTable.draw();
		});
		 
 	    
 });
		
</script>
@stop

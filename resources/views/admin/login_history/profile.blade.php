@extends('admin.includes.masterpage-admin')
@section('content')
 <!-- Page Heading -->
 
 @include('alerts')

 <style>
	.strongs {
    color: #464646;
    font-weight: 600;
    font-size: 18px;
} 
</style>
 
 
<div class="card"> 
	                <div class="row d-none" style="padding: 10px;margin: 5px;" >  
	                            <div class="col-md-3">
									    @if(@$user->photo)
                                            <img src="{{url('public/uploads/user')}}/{{@$user->photo}}" alt="inforfpplcoin" style=" height: 220px;width: 200px;border: 1px solid #aba7a7;padding:5px;object-fit:contain;">
                                       @else
									         <img src="{{url('public/uploads/user/no-image.png')}}" alt="inforfpplcoin" style=" height: 220px;width: 200px;border: 1px solid #aba7a7;padding:5px;object-fit:contain;">
									   @endif  
                                </div>  

                                <div class="col-md-9   ">
                                      <div class="mt-4">  
                                         <h1> {{@$user->title}} {{@$user->first_name}} {{@$user->last_name}} </h1> 
                                         <h4>Membership No. : {{@$user->member_id}} </h4>  
                                         <h4>Designation :   {{@$user->post_title}} </h4> 
                                      </div>
                                </div> 
			  
								<div class="col-md-12 mt-4   "> 
							   	    <h2 > Member Details </h2>  
								    <hr>
                               </div>

                            <div class="col-md-6 "> 
						         	<p> <strong class="strongs">Gender :  {{@$user->post_title}}  </strong>  </p>  
                                    <p> <strong class="strongs"> Date of Birth:  {{@$user->post_title}}  </strong></p> 
                                    <p> <strong class="strongs"> Father’s Name :  {{@$user->post_title}}   </strong> </p> 
                                    <p> <strong class="strongs"> Academic Qualification  :  {{@$user->post_title}}   </strong></p> 
									<p> <strong class="strongs">Professional Qualification :  {{@$user->post_title}}   </strong> </p> 
									<p> <strong class="strongs" > Working Experience :  {{@$user->post_title}}   </strong></p> 
                            </div> 

                            <div class="col-md-6"> 
									<p> <strong class="strongs">Joining Date :  {{date('d M Y',strtotime(@$user->join_date))}}  </strong>  </p> 
                                    <p> <strong class="strongs">Leaving Date :     </strong></p>
                                    <p> <strong class="strongs"> Service Period :  {{date('d M Y',strtotime(@$user->created_at))}}  </strong>  </p>  
                            </div> 
							
							<div class="col-md-12 mt-4  "> 
							   	    <h2 > Address &  Contact Information </h2>  
								    <hr>
                               </div>
							<div class="col-md-4"> 
						           	<h4 style="color: #607D8B;font-weight: 600;font-size: 23px;" >Contact Information </h4>
							        <p> <strong class="strongs">Cell : </strong> </p> 
                                    <p> <strong class="strongs">Alternate Phone: </strong></p>
                                    <p> <strong class="strongs">Email : </strong></p> 
                                    <p> <strong class="strongs">Alternate Email : </strong> </p>     
                            </div> 

							<div class="col-md-4">
							        <h4 style="color: #607D8B;font-weight: 600;font-size: 23px;" >Corresponding Address </h4>
							         <p> <strong class="strongs">Address :  </strong></p> 
                                     <p> <strong class="strongs">City : </strong></p>
                                     <p><strong class="strongs"> State :  </strong></p> 
                                     <p><strong class="strongs">Postal Code :  </strong></p> 
                            </div> 

							<div class="col-md-4">
							         <h4 style="color: #607D8B;font-weight: 600;font-size: 23px;" >Corresponding Address </h4>
							         <p> <strong class="strongs">Address :  </strong>  </p> 
                                    <p> <strong class="strongs">City : </strong></p>
                                    <p> <strong class="strongs"> State : </strong>  </p> 
                                    <p> <strong class="strongs"> Postal Code: </strong>  </p> 
                            </div> 


                </div>

	 
	<div class="card-body tab-content" > 
	          <h2  > Login History of {{$user->name}} </h2>  
			<hr>
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
		</div> 
	 </div>
</div>


<div class="form-action-col text-right">
               <a href="{!! url('admin/manage-login-history/') !!}" class="btn btn-sm btn-warning">  Back </a>
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
				"url": "<?= route('login_history_profile_list') ?>",
				"dataType": "json",
				"type": "POST",
				'data': function(data) {  
					data.user_id = '<?= @$user->id; ?>';
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

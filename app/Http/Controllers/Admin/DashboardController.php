<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Http;
use App\Models\Subject;
use App\Models\Field;
use App\Models\User;
use App\Models\Country; 
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\Journals;
use App\Models\CaseLog;
use App\Models\CaseLogReply;
use App\Models\FAQ; 
use App\Models\NewsUpdate;  
use App\Models\QuickLinks;   
use DB;
use Hash;
use File;
use Mail;
use App\Helpers\Common; 
class DashboardController extends Controller {
    
    public function index()
    {
        return view('admin.dashboard');
    }

    public function __construct() {
             $this->middleware('admin')->except('logout');
	}
   
   
		public function test(Request $request){

		       $ip = $this->getIp();  

			  // $country = $this->getcountry($ip);

			  $response = @file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);

			 echo "<pre>";
			 print_r($response); 
			 die(); 
			echo  $from = date("Y-m-d 00:00:00.000");
			echo  "<br>".$to = date("Y-m-d 23:59:00.000");
			$country  =  DB::select(DB::raw("SELECT country , COUNT(country) as total FROM counter WHERE country IS NOT NULL and `created_at` BETWEEN '".$from."' AND '".$to."' GROUP BY country ORDER by total DESC"));
			
		}

		

		function getCountry($ip) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
           // return $ipdat->geoplugin_countryName;

				
			 
		}
		




		function getIp(){
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
                if (array_key_exists($key, $_SERVER) === true){
                    foreach (explode(',', $_SERVER[$key]) as $ip){
                        $ip = trim($ip); // just to be safe
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                            return $ip;
                        }
                    }
                }
            }
    }



	  public function dashboard_filter(Request $request){ 

	         $from = date("Y-m-d 00:00:00.000",strtotime($request->start_date));
             $to = date("Y-m-d 23:59:00.000",strtotime($request->end_date));
	         $total  = DB::table("counter")->whereBetween('created_at', [$from, $to])->count();

	         $desktop  = DB::table("counter")->where('device','desktop')->whereBetween('created_at', [$from, $to])->count();
	         $tablet  = DB::table("counter")->where('device','tablet')->whereBetween('created_at', [$from, $to])->count();
	         $mobile  = DB::table("counter")->where('device','mobile')->whereBetween('created_at', [$from, $to])->count();
	      
             $country  =  DB::select(DB::raw("SELECT country  , COUNT(country) as total FROM counter WHERE country IS NOT NULL and   `created_at` BETWEEN '".$from."' AND '".$to."' GROUP BY country ORDER by total DESC"));
             $os  =  DB::select(DB::raw("SELECT os  , COUNT(os) as total FROM counter WHERE os IS NOT NULL and `created_at` BETWEEN '".$from."' AND '".$to."' GROUP BY os ORDER by total DESC"));
	           ?>
	           <div class="row">
                
				<div class="col-sm-3">
					<div class="card avtivity-card">
						 <div class="card-body">
							<div class="media align-items-center">
								<div class="media-body">
									<h4 class="fs-20" >Total Users</h4>
									<span class="title text-white font-w600" ><?php echo $total ?? '0';  ?></span>
								</div>
							</div>
							<div class="progress" style="height:5px;">
								<div class="progress-bar bg-success" style="width: 100%; height:5px;" role="progressbar">
									<span class="sr-only">100% Complete</span>
								</div>
							</div>
						</div>
						<div class="effect bg-success"></div>
					</div>
				</div>

			    <div class="col-sm-3"><br>
					<div class="card avtivity-card">
						 <div class="card-body">
							<div class="media align-items-center">
								<div class="media-body">
									<h4 class="fs-20" style="font-size: 15px !important;" >Desktop Users</h4>
									<span class="title text-white font-w600" style="font-size: 15px !important;"><?php echo $desktop ?? '0';  ?></span>
								</div>
							</div>
							<div class="progress" style="height:5px;">
								<div class="progress-bar bg-success" style="width: 100%; height:5px;" role="progressbar">
									<span class="sr-only">100% Complete</span>
								</div>
							</div>
						</div>
						<div class="effect bg-success"></div>
					</div>
				</div>

			   <div class="col-sm-3"><br>
					<div class="card avtivity-card">
					 <div class="card-body">
							<div class="media align-items-center">
								<div class="media-body">
									<h4 class="fs-20" style="font-size: 15px !important;">Tablet Users</h4> 
									<span class="title text-white font-w600" style="font-size: 15px !important;"><?php echo $tablet ?? '0';  ?></span>
								</div>
							</div>
							<div class="progress" style="height:5px;">
								<div class="progress-bar bg-success" style="width: 100%; height:5px;" role="progressbar">
									<span class="sr-only">100% Complete</span>
								</div>
							</div>
						</div>
						<div class="effect bg-success"></div>
					</div>
				</div>

				<div class="col-sm-3"><br>
					<div class="card avtivity-card">
						<div class="card-body">
							<div class="media align-items-center">
								<div class="media-body">
									<h4 class="fs-20" style="font-size: 15px !important;" >Mobile Users</h4> 
									<span class="title text-white font-w600" style="font-size: 15px !important;"><?php echo $mobile ?? '0';  ?></span>
								</div>
							</div>
							<div class="progress" style="height:5px;">
								<div class="progress-bar bg-success" style="width: 100%; height:5px;" role="progressbar">
									<span class="sr-only">100% Complete</span>
								</div>
							</div>
						</div>
						<div class="effect bg-success"></div>
					</div>
				</div> 
			</div>

<div class="row">
			<div class="col-sm-6">
				 <div class="card">
					<div class="card-header">
						<h4 class="fs-20 mb-0">Country Wise User</h4>
					</div>
					<div class="card-body p-0 shrink-card">
							<table class="table shadow-hover table-shrink">
								<tbody> 
									<tr>
										<th>
											<p class="mb-0">Country</p>
										</th>
										<th>
											<p class="mb-0 font-w600 text-green">Total</p>
										</th>
									</tr>
                                     <?php  if($country) {  
                                         foreach ($country as $key => $value) { ?>  
									<tr>
										<td>
											<p class="mb-0"> <?php echo $value->country;  ?> </p>
										</td>
										<td>
											<p class="mb-0 font-w600 text-green"><?php echo $value->total;  ?></p>
										</td>
									</tr> 
								     <?php } }  ?> 
								</tbody>
							</table>
					</div>
				</div>
			</div>


			<div class="col-sm-6">
				 <div class="card">
					<div class="card-header">
						<h4 class="fs-20 mb-0 ">OS Wise User</h4>
					</div>
					<div class="card-body p-0 shrink-card">
							<table class="table shadow-hover table-shrink">
								<tbody> 
									<tr>
										<th>
											<p class="mb-0">Operating System </p>
										</th>
										<th>
											<p class="mb-0 font-w600 text-green">Total</p>
										</th>
									</tr>
                                     <?php  if($os) {  
                                         foreach ($os as $key => $value) { ?>  
									<tr>
										<td>
											<p class="mb-0"> <?php echo $value->os;  ?> </p>
										</td>
										<td>
											<p class="mb-0 font-w600 text-green"><?php echo $value->total;  ?></p>
										</td>
									</tr> 
								     <?php } }  ?> 
								</tbody>
							</table>
					</div>
				</div>
			</div>
</div>


		<?php 
    }

     ///////////// Registered Users Page Start /////////////////////////////////////////////////

    	public function registered_users(Request $request){
    		return view('admin.registered-users');
    	}

     ///////////// Products Page Start /////////////////////////////////////////////////

    	public function products(Request $request){
    		return view('admin.products.products');
    	}

     ///////////// Category Page Start /////////////////////////////////////////////////

    	public function category_page(Request $request){
    		return view('admin.category.category');
    	}


    ///////////// Quotation Page Start /////////////////////////////////////////////////

    	public function quotation(Request $request){
    		return view('admin.quotation');
    	}



    ///////////// Manufacturer Page Start //////////////////////////////////////////////

    	public function manufacturer(Request $request){
    		return view('admin.manufacturer');
    	}

    ///////////// Invoice Page Start //////////////////////////////////////////////


    	public function all_invoices(Request $request){
    		return view('admin.invoice.all-invoices');
    	}


        public function pending_invoice(Request $request){ 
				return view('admin.invoice.pending-invoice'); 
		}

		public function complete_invoice(Request $request){
			return view('admin.invoice.complete-invoice');
		}

		public function payment_history(Request $request){
			return view('admin.invoice.payment-history');
		}
	  

	///////////// Invoice Page End //////////////////////////////////////////////

       	///////////// Subject Section Start //////////////////////////////////////////////
        public function subject(Request $request){ 
				return view('admin.subject.list'); 
		}
		 

		public function subject_list(Request $request){ 
         
		$status =  $request['status'];  
		if($status == 'all') { 
			$status = ['active', 'inactive'];
		} else { 
			$status = [$status];
		} 
		 $totalData = Subject::whereIn('status',$status)->count();
		 $totalFiltered = $totalData;
		 $totalFiltered = $totalData;
		 $limit = $request->input('length');
		 $start = $request->input('start');
		 $order = 'name';
		 $dir = 'ASC';
		 $search = $request->input('search.value'); 
		 if(empty($search)) {
			 $posts =  Subject::whereIn('status',$status)->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = Subject::whereIn('status',$status)->count();
		 } else {
			 $posts =  Subject::whereIn('status',$status) 
									->where(function($query) use ($search){
										 $query->where('created_at', 'LIKE', '%'.$search.'%');
										 $query->orWhere('name', 'LIKE', '%'.$search.'%'); 
									 }) ->offset($start)->limit($limit)   ->orderBy($order,$dir)->get(); 
			 $totalFiltered = Subject::whereIn('status',$status) 
									 ->where(function($query) use ($search){
										 $query->where('created_at', 'LIKE', '%'.$search.'%');
										 $query->orWhere('name', 'LIKE', '%'.$search.'%'); 
									  })->count();
		 }
		 $data = array();
		 if(!empty($posts))  {
			 $i=1; 
			 foreach ($posts as $post) {  
									  
				 if($post->status == "active"){
					 $status  = '<strong style="color:green" >Active</strong>';
				 } else {
					 $status  = '<strong style="color:red">Inactive</strong>';  
				 }

				 $edit  = '<a href="'.url('').'/admin/subject/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
			 	 $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/subject/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
				
				 $nestedData['id'] =  $i;   
				 $nestedData['name'] =  $post->name;   
				 $nestedData['status'] =  $status;   
				 $nestedData['action'] = $edit.$delete;
				 $data[] = $nestedData;
					$i++;
			 } 
		 } 
		 $json_data = array(
					 "draw"            => intval($request->input('draw')),  
					 "recordsTotal"    => intval($totalData),  
					 "recordsFiltered" => intval($totalFiltered), 
					 "data"            => $data   
					 );
		 echo json_encode($json_data); 
	  
	  }
   
	 public function subject_add(){
		 return view('admin.subject.add');
	 } 
     public function subject_save(Request $request) {  
			$data = new  Subject;
			$data->fill($request->all()); 
			$data->save(); 
        return redirect('admin/subject')->with('message','Subject Created Successfully.'); 
    }  
	public function subject_edit($id){ 
        $data['data']  =  Subject::where('id',$id)->first();  
        return view('admin.subject.edit',$data); 
	} 
	public function subject_update(Request $request){ 
		$Subject = Subject::findOrFail($request->id);
		$data = $request->all();    
		$Subject->update($data); 
		return redirect(url('admin/subject/'))->with('message','Subject Updated Successfully.');  
	} 
	public function subject_delete($id){
		 $data = Subject::findOrFail($id); 
	     $data->delete();
	    return redirect(url('admin/subject'))->with('message','Record Deleted Successfully.'); 
	}
	
 ///////////// Subject Section End //////////////////////////////////////////////
 
 
  ///////////// field Section Start //////////////////////////////////////////////
	  public function field(Request $request){  
	   	$data['subject']  =  Subject::orderBy('name','ASC')->get();  
        return view('admin.field.list',$data);  
	}
	 

	public function field_list(Request $request){ 
				
				$status =  $request['status'];  
				if($status == 'all') { 
					$status = ['active', 'inactive'];
				} else { 
					$status = [$status];
				} 
				$subject =  $request['subject'];
				$totalData = Field::whereIn('status',$status)
				->where(function ($query) use ($subject) { if($subject) {  $query->where('sub_id',$subject); }})->count();
				$totalFiltered = $totalData;
				$totalFiltered = $totalData;
				$limit = $request->input('length');
				$start = $request->input('start');
				$order = 'name';
				$dir = 'ASC';  
				$search = $request->input('search.value'); 
				if(empty($search)) {
					$posts =  Field::whereIn('status',$status)
					->where(function ($query) use ($subject) { if($subject) {  $query->where('sub_id',$subject); }})
					->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 

					$totalFiltered = Field::whereIn('status',$status)
					->where(function ($query) use ($subject) { if($subject) {  $query->where('sub_id',$subject); }})
					->count();
				} else {
					$posts =  Field::whereIn('status',$status)
											->where(function ($query) use ($subject) { if($subject) {  $query->where('sub_id',$subject); }}) 
											->where(function($query) use ($search){
												$query->where('created_at', 'LIKE', '%'.$search.'%');
												$query->orWhere('name', 'LIKE', '%'.$search.'%'); 
											}) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 

					$totalFiltered = Field::whereIn('status',$status)
											->where(function ($query) use ($subject) { if($subject) {  $query->where('sub_id',$subject); }}) 
											->where(function($query) use ($search){
												$query->where('created_at', 'LIKE', '%'.$search.'%');
												$query->orWhere('name', 'LIKE', '%'.$search.'%'); 
											})->count();
				}
				$data = array();
				if(!empty($posts))  {
					$i=1; 
					foreach ($posts as $post) {  
											
						if($post->status == "active"){
							$status  = '<strong style="color:green" >Active</strong>';
						} else {
							$status  = '<strong style="color:red">Inactive</strong>';  
						}

						$edit  = '<a href="'.url('').'/admin/field/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
						$delete = '<a href="javascript:;" data-href=" '.url('').'/admin/field/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
						
						$nestedData['id'] =  $i;   
						$nestedData['subject'] = $this->get_subject_name($post->sub_id);
						$nestedData['name'] =  $post->name;    
						$nestedData['status'] =  $status;   
						$nestedData['action'] = $edit.$delete;
						$data[] = $nestedData;
							$i++;
					} 
				} 
				$json_data = array(
							"draw"            => intval($request->input('draw')),  
							"recordsTotal"    => intval($totalData),  
							"recordsFiltered" => intval($totalFiltered), 
							"data"            => $data   
							);
				echo json_encode($json_data); 
  
  }

    function get_subject_name($id){
		  return Subject::where('id',$id)->first()->name ?? ''; 
	}
 
	public function field_add(Request $request){  
		$data['subject']  =  Subject::orderBy('name','ASC')->get();  
		return view('admin.field.add',$data);  
	}

 
 public function field_save(Request $request) {  
		$data = new  Field;
		$data->fill($request->all()); 
		$data->save(); 
	return redirect('admin/field')->with('message','Record Created Successfully.'); 
}  
public function field_edit($id){ 
	$data['data']  =  Field::where('id',$id)->first();  
	$data['subject']  =  Subject::orderBy('name','ASC')->get();
	return view('admin.field.edit',$data); 
} 
public function field_update(Request $request){ 
	$field = Field::findOrFail($request->id);
	$data = $request->all();    
	$field->update($data); 
	return redirect(url('admin/field/'))->with('message','Record Updated Successfully.');  
} 
public function field_delete($id){
	 $data = Field::findOrFail($id); 
	 $data->delete();
	return redirect(url('admin/field'))->with('message','Record Deleted Successfully.'); 
}

///////////// field Section End //////////////////////////////////////////////


	///////////// Manage  Feedback Section Start  //////////////////////////////////////////////
	
    public function manage_feedback(Request $request){  
        return view('admin.feedback.list');  
	}


	public function feedback_list(Request $request){ 
		 
		 //archive
		$totalData = Feedback::where('archive','no')->count();
		$totalFiltered = $totalData;
		$totalFiltered = $totalData;
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = 'name';
		$dir = 'ASC';  
		$search = $request->input('search.value'); 
		if(empty($search)) {
			$posts =  Feedback::where('archive','no')->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			$totalFiltered = Feedback::where('archive','no')->count();
		} else {
			$posts =  Feedback::where('archive','no')->where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%');
										$query->orWhere('name', 'LIKE', '%'.$search.'%'); 
									}) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 

			$totalFiltered = Feedback::where('archive','no')->where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%');
										$query->orWhere('name', 'LIKE', '%'.$search.'%'); 
									})->count();
		}
		$data = array();
		if(!empty($posts))  {
			$i=1; 
			foreach ($posts as $post) {  
									
				

				$edit  = '<a href="'.url('').'/admin/manage-feedback/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
				$delete = '<a href="javascript:;" data-href=" '.url('').'/admin/manage-feedback/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
				$archive = '<a href="javascript:;" data-href=" '.url('').'/admin/manage-feedback/'.$post->id.'/archive"  class="btn btn-info shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-archive"   data-placement="top" title="Archive " ><i class="fa fa-archive"  ></i></a>';  
				$message = '<a href="'.url('').'/admin/manage-feedback/'.$post->id.'/view" style="background: #009688;color: white;padding: 5px 10px 5px 10px;border-radius: 25px;" data-placement="top" title="View " ><i class="fa fa-eye" > </i>  </a>';  
 
				$nestedData['id'] =  $i;
				$nestedData['name'] =  $post->title.' '. $post->name;
				$nestedData['organization'] =  $post->organization;
				$nestedData['city'] =  $post->city;
				
				$nestedData['country'] =  $post->country;
				$nestedData['mobile'] =  $post->mobile;
				$nestedData['email'] =  $post->email; 
				$nestedData['created_at'] =  date('dM Y H:i:A',strtotime($post->created_at));
				$nestedData['message'] =  $message;   
				$nestedData['action'] = $archive.$edit.$delete;
				$data[] = $nestedData;
					$i++;
			} 
		} 
		$json_data = array(
					"draw"            => intval($request->input('draw')),  
					"recordsTotal"    => intval($totalData),  
					"recordsFiltered" => intval($totalFiltered), 
					"data"            => $data   
					);
		echo json_encode($json_data); 
	}
	

	public function feedback_add(Request $request){  
		$data['country']  =  Country::orderBy('countries_name','ASC')->get();  
		return view('admin.feedback.add',$data);  
	}


	public function feedback_save(Request $request) {
		$data = new  Feedback;
		$data->fill($request->all());
		$data->save();
    	return redirect('admin/manage-feedback')->with('message','Record Created Successfully.'); 
    }




	public function feedback_edit($id){
		$data['data']  =  Feedback::where('id',$id)->first();  
		$data['country']  =  Country::orderBy('countries_name','ASC')->get();  
		return view('admin.feedback.edit',$data); 
	} 
 
	public function feedback_update(Request $request){ 
		$field = Feedback::findOrFail($request->id);
		$data = $request->all();      
		$field->update($data); 
		return redirect(url('admin/manage-feedback/'))->with('message','Record Updated Successfully.');  
	} 


	public function feedback_view($id){ 
		$data['data']  =  Feedback::where('id',$id)->first();   
		$data['reply']  =  FeedbackReply::where('f_id',$id)->orderBy('id','DESC')->get();   
		return view('admin.feedback.view',$data); 
	} 


	public function feedback_reply(Request $request){
	  
		$records = Feedback::findOrFail($request->f_id); 
		$data = new  FeedbackReply;
		$data->fill($request->all());
		$data->save();   
		$settings  = DB::table("settings")->first();
		$array['title']    =  'Feedback Reply from '.$settings->sender_name;; 
		$array['name']     =    $records->name; 
		$array['email']    =    $records->email; 
		$array['comments'] =    $request->comments;
		$array['sender_email'] = $settings->sender_email; 
		$array['sender_name'] =  $settings->sender_name;   
		$mail =  Mail::send('email.feedback_reply', $array, function($message)use($array) {
		      $message->to($array['email'])
		              ->subject($array['title'])
					  ->cc($array['sender_email'])
					  ->from($array['sender_email'],$array['sender_name']);
		  }); 
		 return redirect()->back()->with('message','Feedback Reply sent Successfully.');  
	}  

	public function feedback_delete($id){
		 $data = Feedback::findOrFail($id);  
		 $data->delete();
		return redirect()->back()->with('message','Record Deleted Successfully.'); 
	}

	public function move_to_archive($id){
		$field = Feedback::findOrFail($id);
		$data['archive'] = 'yes';        
		$field->update($data);  
	   return redirect()->back()->with('message','Record Move to Archive Successfully.'); 
   }



	


	///////////// Manage  Feedback  Section End //////////////////////////////////////////////


	// 
	public function feedback_archive(Request $request){ 
		 return view('admin.feedback.archive_list');  
	}

	public function feedback_archive_list(Request $request){  
	   //archive
	   $totalData = Feedback::where('archive','yes')->count();
	   $totalFiltered = $totalData;
	   $totalFiltered = $totalData;
	   $limit = $request->input('length');
	   $start = $request->input('start');
	   $order = 'name';
	   $dir = 'ASC';  
	   $search = $request->input('search.value'); 
	   if(empty($search)) {
		   $posts =  Feedback::where('archive','yes')->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
		   $totalFiltered = Feedback::where('archive','yes')->count();
	   } else {
		   $posts =  Feedback::where('archive','yes')->where(function($query) use ($search){
									   $query->where('created_at', 'LIKE', '%'.$search.'%');
									   $query->orWhere('name', 'LIKE', '%'.$search.'%'); 
								   }) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 

		   $totalFiltered = Feedback::where('archive','yes')->where(function($query) use ($search){
									   $query->where('created_at', 'LIKE', '%'.$search.'%');
									   $query->orWhere('name', 'LIKE', '%'.$search.'%'); 
								   })->count();
	   }
	   $data = array();
	   if(!empty($posts))  {
		   $i=1; 
		   foreach ($posts as $post) {   

			   $edit  = '<a href="'.url('').'/admin/manage-feedback/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
			   $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/manage-feedback/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
			   $message = '<a href="'.url('').'/admin/manage-feedback/'.$post->id.'/view" style="background: #009688;color: white;padding: 5px 10px 5px 10px;border-radius: 25px;" data-placement="top" title="View " ><i class="fa fa-eye" > </i> View </a>';  

			   $nestedData['id'] =  $i;
			   $nestedData['name'] =  $post->title.' '. $post->name;
			   $nestedData['organization'] =  $post->organization;
			   $nestedData['city'] =  $post->city;
			   $nestedData['state'] =  $post->state;
			   $nestedData['country'] =  $post->country;
			   $nestedData['mobile'] =  $post->mobile;
			   $nestedData['email'] =  $post->email; 
			   $nestedData['message'] =  $message;  
			   $nestedData['action'] = $edit.$delete;
			   $data[] = $nestedData;
				   $i++;
		   } 
	   } 
	   $json_data = array(
				   "draw"            => intval($request->input('draw')),  
				   "recordsTotal"    => intval($totalData),  
				   "recordsFiltered" => intval($totalFiltered), 
				   "data"            => $data   
				   );
	   echo json_encode($json_data); 
   }
 
   public function case_log(Request $request){ 
    	return view('admin.feedback.case_log_list');  
   }

   public function case_log_add(Request $request){ 
    	return view('admin.feedback.case_log_add');  
   }


	public function case_log_add_details(Request $request){  
			if(request()->has('id')) {
				$data['type'] =  request('id');
				$data['journal'] =  Journals::orderBy('title','ASC')->get();  
				return view('admin.feedback.case_log_add_details',$data);  
			} else {
				return redirect(url('admin/manage-case-log/add')); 
			}
	}
   

   public function case_log_list(Request $request){ 
 	//archive
		$totalData = CaseLog::count();
		$totalFiltered = $totalData;
		$totalFiltered = $totalData;
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = 'id';
		$dir = 'DESC';  
		$search = $request->input('search.value'); 
		if(empty($search)) {
			$posts =  CaseLog::offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			$totalFiltered = CaseLog::count();
		} else {
			$posts =  CaseLog::where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%');
										$query->orWhere('ticket_number', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('department', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('urgency', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('related_service', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('book_title', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('media_type', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('publication_type', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('subject', 'LIKE', '%'.$search.'%'); 
									}) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
 
			$totalFiltered = CaseLog::where(function($query) use ($search){
								$query->where('created_at', 'LIKE', '%'.$search.'%');
								$query->orWhere('ticket_number', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('department', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('urgency', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('related_service', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('book_title', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('media_type', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('publication_type', 'LIKE', '%'.$search.'%'); 
								$query->orWhere('subject', 'LIKE', '%'.$search.'%'); 
									})->count();
		}
		$data = array();
		if(!empty($posts))  {
			$i=1; 
			foreach ($posts as $post) {   
 
				$view  = '<a href="'.url('').'/admin/manage-case-log/'.$post->id.'/view" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-eye"></i>  </a>'; 
				$ticket_number  = '<a href="'.url('').'/admin/manage-case-log/'.$post->id.'/view" style="color:black;font-weight:bold;" > '.$post->ticket_number.'</a>'; 
				
				 if($post->updated_at == '0000-00-00 00:00:00'){
					$updated_at = '--';
				 } else{
					 $updated_at = date('dM Y H:i:A',strtotime($post->updated_at));
				 }

				 if($post->status == 'open'){
					$status =   '<strong style="color:green">Open</strong>';
				 }else if($post->status == 'closed'){
					$status =  '<strong style="color:red">Closed</strong>';
				 }else if($post->status == 'pending'){
					$status = '<strong style="color:orange">Pending</strong>';
				 }

				$nestedData['DT_RowClass'] = $post->status; 
				$nestedData['id'] =  $i;
				$nestedData['ticket_number'] =  $ticket_number;
				$nestedData['subject'] =  $post->subject;
				$nestedData['department'] =  $post->department;
				$nestedData['status'] =    $status;
				$nestedData['urgency'] =  $post->urgency; 
				$nestedData['created_at'] = date('dM Y H:i:A',strtotime($post->created_at));
				$nestedData['updated_at'] = $updated_at;
				$nestedData['action'] = $view;
				$data[] = $nestedData;
					$i++;
			} 
		} 
		$json_data = array(
					"draw"            => intval($request->input('draw')),  
					"recordsTotal"    => intval($totalData),  
					"recordsFiltered" => intval($totalFiltered), 
					"data"            => $data   
					);
		echo json_encode($json_data); 
	}
  
   public function save_case_log(Request $request){ 
	 	$data = new  CaseLog;
		$data->fill($request->all());

		$ticket_number =  "TKT".rand(123,456).date('y');
		if ($request->hasFile('attachments')) {
			$attachments = [];
			foreach ($request->file('attachments') as $file) {
				$originalName = $file->getClientOriginalName();
				$extension = $file->getClientOriginalExtension();
				$uniqueName = time() . '_' . uniqid() . '.' . $extension;
				$uniqueName = str_replace(' ', '', $uniqueName);
				$file->move(public_path('uploads/case_logs'), $uniqueName);
				// Save the file name in the attachments array
				$attachments[] = $uniqueName;
			}
			// Convert the array of file names to a JSON string
			$attachmentsJson = json_encode($attachments);
			// Save the JSON string in the database column
			$data['attachments'] = $attachmentsJson;
		}
		$data['ticket_number'] = $ticket_number;
		$data['user_id'] = Auth::user()->id; 
		$data->save();
		$settings  = DB::table("settings")->first();
		$array['title']    =   'Case Ticket Confirmation, Ticket No. is #'.$ticket_number; 
		$array['subject']    =   'Case Ticket Confirmation';  

		$array['name'] = Auth::user()->name; 
		$array['email'] = Auth::user()->email;

		$array['cc']     =     $request->cc;
		$array['comments'] =     'Your request has been received and our team will contact you as soon as possible.';
		$array['sender_email'] = $settings->sender_email; 
		$array['sender_name'] =  $settings->sender_name;   


		if (!empty($array['cc'])) {

			$mail =  Mail::send('email.case_log', $array, function($message)use($array) {
				$message->to($array['email'])
						->subject($array['title'])
						->cc($array['sender_email'])
						->cc($array['cc'])
						->from($array['sender_email'],$array['sender_name']);
			});  
		} else{
			$mail =  Mail::send('email.case_log', $array, function($message)use($array) {
				$message->to($array['email'])
						->subject($array['title'])
						->cc($array['sender_email'])
						->from($array['sender_email'],$array['sender_name']);
			});  

		}
		
		return redirect(url('admin/manage-case-log'))->with('message','Your Ticket has Submitted Successfully.');  

   }
     
   

   public function case_log_view($id){
	   $data['data'] = CaseLog::findOrFail($id);
	   $data['reply'] = CaseLogReply::where('case_id',$id)->orderBy('id','desc')->get();
	   return view('admin.feedback.case_log_view',$data);  
    }
    

	
	public function save_case_log_reply(Request $request){ 
 
		$data = new  CaseLogReply;
	    $data->fill($request->all()); 
	   if ($request->hasFile('attachments')) {
		   $attachments = [];
		   foreach ($request->file('attachments') as $file) {
			   $originalName = $file->getClientOriginalName();
			   $extension = $file->getClientOriginalExtension();
			   $uniqueName = time() . '_' . uniqid() . '.' . $extension;
			   $uniqueName = str_replace(' ', '', $uniqueName);
			   $file->move(public_path('uploads/case_logs'), $uniqueName);
			   // Save the file name in the attachments array
			   $attachments[] = $uniqueName;
		   }
		   // Convert the array of file names to a JSON string
		   $attachmentsJson = json_encode($attachments);
		   // Save the JSON string in the database column
		   $data['attachments'] = $attachmentsJson;
	   }
		$data['sender_id'] = Auth::user()->id; 
		$data->save(); 

	    $CaseLog = CaseLog::findOrFail($request->case_id); 
		$updateLog['status'] =  $request->status;
		$updateLog['updated_at'] =  date('Y-m-d H:i:s'); 
		$CaseLog->update($updateLog);
		 
		  
		$settings  = DB::table("settings")->first();
		$array['title'] = 'Support Reply on Ticket No. #' . $CaseLog->ticket_number; 
		$array['subject'] = 'Case Ticket Reply from Support';  
		$array['name'] = Auth::user()->name;
		$array['cc'] = $request->cc;
		$array['email'] = Auth::user()->email;
		$array['comments'] = 'Support Team sent a reply to your query, please login to your account for view more details.';
		$array['sender_email'] = $settings->sender_email; 
		$array['sender_name'] = $settings->sender_name;

		// Check if CC recipients exist
		if (!empty($array['cc'])) {
			$mail = Mail::send('email.case_log', $array, function($message) use ($array) {
				$message->to($array['email'])
						->subject($array['title'])
						->cc($array['sender_email']) 
						->cc($array['cc'])
						->from($array['sender_email'], $array['sender_name']);
			});  
		} else {
			// If no CC recipients, send email without CC
			$mail = Mail::send('email.case_log', $array, function($message) use ($array) {
				$message->to($array['email'])
						->subject($array['title'])
						->cc($array['sender_email']) 
						->from($array['sender_email'], $array['sender_name']);
			});  
		}



	   return redirect()->back()->with('message','Reply has been sent successfully.');  

  }


	// /////////////////////FAQ Start  /////////////////////////////// 
	
	
	public function faq(Request $request){  
        return view('admin.faq.list');  
	} 
	public function faq_add(Request $request){  
        return view('admin.faq.add');  
	} 
	public function faq_save(Request $request){   
		$data = new  FAQ;
		$data->fill($request->all()); 
		$data->save(); 
		return redirect('admin/manage-faq')->with('message','Record Created Successfully.'); 
    }   
	public function faq_edit($id){ 
        $data['faq'] = FAQ::findOrFail($id); 
		return view('admin.faq.edit',$data);  
	} 
	public function faq_update(Request $request){   
	 	    $faq = FAQ::findOrFail($request->id);
			$data = $request->all();      
			$faq->update($data); 
			return redirect(url('admin/manage-faq/'))->with('message','Record Updated Successfully.');   
	} 

	public function faq_list(Request $request){ 
         
		$status =  $request['status'];
		$type =  $request['type'];  
		if($status == 'all') { 
			$status = ['active', 'inactive'];
		} else { 
			$status = [$status];
		} 
		 $totalData = FAQ::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})->count();
		 $totalFiltered = $totalData;
		 $totalFiltered = $totalData;
		 $limit = $request->input('length');
		 $start = $request->input('start');
		 $order = 'id';
		 $dir = 'DESC';
		 $search = $request->input('search.value'); 
		 if(empty($search)) {
			 $posts =  FAQ::whereIn('status',$status)
			 ->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
			 ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = FAQ::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})->count();
		 } else {
			 $posts =  FAQ::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
									->where(function($query) use ($search){
										 $query->where('created_at', 'LIKE', '%'.$search.'%');
										 $query->orWhere('type', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('question', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('answer', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('link', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('status', 'LIKE', '%'.$search.'%');  
									 }) ->offset($start)->limit($limit)   ->orderBy($order,$dir)->get(); 
			 $totalFiltered = FAQ::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
									 ->where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%');
										$query->orWhere('type', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('question', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('answer', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('link', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('status', 'LIKE', '%'.$search.'%');  
									  })->count();
		 }
		 $data = array();
		 if(!empty($posts))  {
			 $i=1; 
			 foreach ($posts as $post) {  
									  
				 if($post->status == "active"){
					 $status  = '<strong style="color:green !important; " >Active</strong>';
				 } else {
					 $status  = '<strong style="color:red !important;">Inactive</strong>';  
				 }

				 $edit  = '<a href="'.url('').'/admin/manage-faq/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
			 	 $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/manage-faq/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
				 $link  = '--';
				 if($post->link){
					$link  = '<a href="'.$post->link.'" target="_blank" > '.$post->link.'  </a>'; 
				 }
				 $nestedData['id'] = "<strong># : </strong>".$i;   
				 $nestedData['type'] =  "<strong>Type : </strong>".$post->type;
				 $nestedData['question'] =  "<strong>Question : </strong>".$post->question;
				 $nestedData['answer'] =  "<strong>Answer : </strong>".$post->answer;   
				 $nestedData['link'] =  "<strong>Link</strong> : ".$link;   
				 $nestedData['status'] =  "<strong>Status</strong> : ".$status;   
				 $nestedData['action'] ="<strong>Action</strong> :  ". $edit.$delete;
				 $data[] = $nestedData;
					$i++;
			 } 
		 } 
		 $json_data = array(
					 "draw"            => intval($request->input('draw')),  
					 "recordsTotal"    => intval($totalData),  
					 "recordsFiltered" => intval($totalFiltered), 
					 "data"            => $data   
					 );
		 echo json_encode($json_data); 
	  
	   } 

	    public function faq_delete($id){
			$data = FAQ::findOrFail($id); 
			$data->delete();
			return redirect(url('admin/manage-faq'))->with('message','Record Deleted Successfully.'); 
		}

		///////////////////faq Section End /////////////////////////////




 // /////////////////////NewsUpdate Start  /////////////////////////////// 
	
	
	public function news_update(Request $request){   
        return view('admin.news.list');  
	} 
	public function news_update_add(Request $request){  
        return view('admin.news.add');  
	} 
	public function news_update_save(Request $request){
		$data = new  NewsUpdate;
		$data->fill($request->all()); 
		if ($file = $request->file('image')){
            $photo_name = rand().$request->file('image')->getClientOriginalName();
            $file->move(public_path('uploads/news'), $photo_name);
            $data['image'] = $photo_name;
        } 
		$data->save(); 
		return redirect('admin/news-update')->with('message','Record Created Successfully.'); 
    }   
	public function news_update_edit($id){ 
        $data['news'] = NewsUpdate::findOrFail($id); 
		return view('admin.news.edit',$data);  
	} 
	public function news_update_update(Request $request){   
	 	    $news = NewsUpdate::findOrFail($request->id);
			$data = $request->all();   
			if ($file = $request->file('image')){ 
				if (file_exists(public_path('uploads/news/'.$news->image)))  {
					@unlink(public_path('uploads/news/'.$news->image));
				}   
				$originalName = $request->file('image')->getClientOriginalName();
				$extension = $request->file('image')->getClientOriginalExtension(); 
				$uniqueName = time() . '_' . uniqid() . '.' . $extension; 
				$uniqueName = str_replace(' ', '', $uniqueName); 
				$file->move(public_path('uploads/news'), $uniqueName); 
				$data['image'] = $uniqueName;
			}   
			$news->update($data); 
			return redirect(url('admin/news-update/'))->with('message','Record Updated Successfully.');   
	} 
	
	public function news_update_list(Request $request){ 
         
		$status =  $request['status'];
		$type =  $request['type'];  
		if($status == 'all') { 
			$status = ['active', 'inactive'];
		} else { 
			$status = [$status];
		} 
		 $totalData = NewsUpdate::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})->count();
		 $totalFiltered = $totalData;
		 $totalFiltered = $totalData;
		 $limit = $request->input('length');
		 $start = $request->input('start');
		 $order = 'id';
		 $dir = 'DESC';
		 $search = $request->input('search.value'); 
		 if(empty($search)) {
			 $posts =  NewsUpdate::whereIn('status',$status)
			 ->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
			 ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = NewsUpdate::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})->count();
		 } else {
			 $posts =  NewsUpdate::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
									->where(function($query) use ($search){
										 $query->where('created_at', 'LIKE', '%'.$search.'%');
										 $query->orWhere('type', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('title', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('description', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('link', 'LIKE', '%'.$search.'%');  
									 }) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = NewsUpdate::whereIn('status',$status)->where(function ($query) use ($type) { if($type) {  $query->where('type',$type); }})
									 ->where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%');
										$query->orWhere('type', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('title', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('description', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('link', 'LIKE', '%'.$search.'%');   
									  })->count();
		 }
		 $data = array();
		 if(!empty($posts))  {
			 $i=1; 
			 foreach ($posts as $post) {  
									  
				 if($post->status == "active"){
					 $status  = '<strong style="color:green !important; " >Active</strong>';
				 } else {
					 $status  = '<strong style="color:red !important;">Inactive</strong>';  
				 }

				 $edit  = '<a href="'.url('').'/admin/news-update/'.$post->id.'/edit" class="btn btn-warning btn-sm  " data-toggle="tooltip" data-placement="top" title="Edit " >  Edit  </a>'; 
			 	  $delete = '   &nbsp;<a href="javascript:;" data-href=" '.url('').'/admin/news-update/'.$post->id.'/delete"  class="btn btn-danger btn-sm  "  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " >  Delete</a>';  
				 $link  = '--';
				 if($post->link){
					$link  = '<a href="'.$post->link.'" target="_blank" > '.$post->link.'  </a>'; 
				 }
				 $image = "";
				 if($post->image){
					$image  = ' <img src="'.url('public/uploads/news').'/'.$post->image.' "  class="news-img">'; 
				 } 
				  
				 $nestedData['id'] = "<strong># : </strong>".$i;   
				 $nestedData['type'] =  "<strong>Type : </strong>".ucwords($post->type);
				 $nestedData['title'] =  "<strong>".ucwords($post->type). " Title : </strong>".$post->title;
				 $nestedData['description'] =  "<strong>Description of the  ".ucwords($post->type). " : </strong>".$post->description;
				 $nestedData['image'] =  "<strong>Image : </strong>".$image; 
				 $nestedData['author'] =  "<strong>Author:</strong> : ".$post->author;
				 $nestedData['link'] =  "<strong>Courtesy Link:</strong> : ".$link;   
				 $nestedData['status'] =  "<strong>Status</strong> : ".$status;   
				 $nestedData['action'] ="<strong>Action</strong> :  ".$edit.$delete;
				 $data[] = $nestedData;
					$i++;
			 } 
		 } 
		 $json_data = array(
					 "draw"            => intval($request->input('draw')),  
					 "recordsTotal"    => intval($totalData),  
					 "recordsFiltered" => intval($totalFiltered), 
					 "data"            => $data   
					 );
		 echo json_encode($json_data); 
	  
	   } 

	    public function news_update_delete($id){
			$data = NewsUpdate::findOrFail($id); 
			$data->delete();
			return redirect(url('admin/news-update'))->with('message','Record Deleted Successfully.'); 
		}

		///////////////////NewsUpdate Section End /////////////////////////////

		







		
 // /////////////////////NewsUpdate Start  /////////////////////////////// 
	
	
	public function quick_link(Request $request){   
        return view('admin.quicklink.list');  
	} 
	public function quick_link_add(Request $request){  
        return view('admin.quicklink.add');  
	} 
	public function quick_link_save(Request $request){
		$data = new  QuickLinks;
		$data->fill($request->all()); 
		if ($file = $request->file('image')){
            $photo_name = rand().$request->file('image')->getClientOriginalName();
            $file->move(public_path('uploads/quicklink'), $photo_name);
            $data['image'] = $photo_name;
        } 
		$data->save(); 
		return redirect('admin/quick-link')->with('message','Record Created Successfully.'); 
    }   
	public function quick_link_edit($id){ 
        $data['quicklink'] = QuickLinks::findOrFail($id); 
		return view('admin.quicklink.edit',$data);  
	} 
	public function quick_link_update(Request $request){   
	 	    $quick_link = QuickLinks::findOrFail($request->id);
			$data = $request->all();   
			if ($file = $request->file('image')){ 
				if (file_exists(public_path('uploads/quicklink/'.$quick_link->image)))  {
					@unlink(public_path('uploads/quicklink/'.$quick_link->image));
				}   
				$originalName = $request->file('image')->getClientOriginalName();
				$extension = $request->file('image')->getClientOriginalExtension(); 
				$uniqueName = time() . '_' . uniqid() . '.' . $extension; 
				$uniqueName = str_replace(' ', '', $uniqueName); 
				$file->move(public_path('uploads/quicklink'), $uniqueName); 
				$data['image'] = $uniqueName;
			}   
			$quick_link->update($data); 
			return redirect(url('admin/quick-link/'))->with('message','Record Updated Successfully.');   
	} 
	
	public function quick_link_list(Request $request){ 
         
		 $status =  $request['status'];  
		 $totalData = QuickLinks::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})->count();
		 $totalFiltered = $totalData;
		 $totalFiltered = $totalData;
		 $limit = $request->input('length');
		 $start = $request->input('start');
		 $order = 'id';
		 $dir = 'DESC';
		 $search = $request->input('search.value'); 
		 if(empty($search)) {
			 $posts =  QuickLinks::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})
			 ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = QuickLinks::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})->count();
		 } else {
			 $posts =  QuickLinks::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})
									->where(function($query) use ($search){
										 $query->where('created_at', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('title', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('description', 'LIKE', '%'.$search.'%'); 
										 $query->orWhere('link', 'LIKE', '%'.$search.'%');  
									 }) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
			 $totalFiltered = QuickLinks::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})
									 ->where(function($query) use ($search){
										$query->where('created_at', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('title', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('description', 'LIKE', '%'.$search.'%'); 
										$query->orWhere('link', 'LIKE', '%'.$search.'%');   
									  })->count();
		 }
		 $data = array();
		 if(!empty($posts))  {
			 $i=1; 
			 foreach ($posts as $post) {  
									  
				 if($post->status == "active"){
					 $status  = '<strong style="color:green !important; " >Active</strong>';
				 } else {
					 $status  = '<strong style="color:red !important;">Inactive</strong>';  
				 }

				 $edit  = '<a href="'.url('').'/admin/quick-link/'.$post->id.'/edit" class="btn btn-warning btn-sm  " data-toggle="tooltip" data-placement="top" title="Edit " >  Edit  </a>'; 
			 	  $delete = '   &nbsp;<a href="javascript:;" data-href=" '.url('').'/admin/quick-link/'.$post->id.'/delete"  class="btn btn-danger btn-sm  "  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " >  Delete</a>';  
				 $link  = '--';
				 if($post->link){
					$link  = '<a href="'.$post->link.'" target="_blank" > '.$post->link.'  </a>'; 
				 }
				 $image = "";
				 if($post->image){
					$image  = ' <img src="'.url('public/uploads/quicklink').'/'.$post->image.' "  class="quick_link-img">'; 
				 } 
				  
				 $nestedData['id'] = "<strong># : </strong>".$i;    
				 $nestedData['title'] = "<strong> Title : </strong>".$post->title;
				 $nestedData['description'] = "<strong>Description : </strong>".$post->description;
				 $nestedData['image'] = "<strong>Image : </strong>".$image;  
				 $nestedData['link'] = "<strong> Link:</strong> : ".$link;   
				 $nestedData['status'] = "<strong>Status</strong> : ".$status;   
				 $nestedData['action'] = "<strong>Action</strong> :  ".$edit.$delete;
				 $data[] = $nestedData;
					$i++;
			 } 
		 } 
		 $json_data = array(
					 "draw"            => intval($request->input('draw')),  
					 "recordsTotal"    => intval($totalData),  
					 "recordsFiltered" => intval($totalFiltered), 
					 "data"            => $data   
					 );
		 echo json_encode($json_data);  
	   } 

	    public function quick_link_delete($id){
			$data = QuickLinks::findOrFail($id); 
			$data->delete();
			return redirect(url('admin/quick-link'))->with('message','Record Deleted Successfully.'); 
		}

		///////////////////QuickLinks Section End /////////////////////////////

		




 
}



<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;
use App\Models\User;
use App\Models\Country;
use App\Models\UserAddress; 
use App\Models\UserDetails;
use App\Models\Journals;
use App\Models\UserQualification;
use App\Models\UserExperience;
use App\Models\StaffDesignation; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Helpers\Common; 
use Session; 
use Mail; 
use Carbon\Carbon;
class ManageStaffController extends Controller {
     
    public function __construct() {
         $this->middleware('admin')->except('logout');
    }
	 
    public function index(){ 
         return view('admin.staff.list'); 
    }
  
	//All jobs with ajax   Show all Jobs  with ajxa data table
    public function manage_staff_list(Request $request){  
        $status =  'active'; 
        $user_type = 'staff';  
        $totalData = User::leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                    ->where(function ($query) use ($status,$user_type) {
                        if ($status) { $query->where('users.status', $status); }
                        if ($user_type) { $query->where('users.user_type', $user_type);  }
                    })->count(); 
        $totalFiltered = $totalData;
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');  
        $order = 'users.id';
        $dir = 'desc'; 

        $search = $request->input('search.value'); 

        if(empty($search)) {
            
                    $posts = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                            ->where(function ($query) use ($status,$user_type) {
                                if ($status) {  $query->where('users.status', $status);  }
                                if ($user_type) {   $query->where('users.user_type', $user_type);   }
                            })
                            ->select('users.*','users.user_id as  member_id','users.id as  u_id','user_details.*')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get(); 


                        $totalFiltered = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                        ->where(function ($query) use ($status,$user_type) {
                            if ($status) {  $query->where('users.status', $status);  }
                            if ($user_type) {  $query->where('users.user_type', $user_type);  }
                        })
                        ->count(); 
        } else{

            $posts = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                        ->where(function ($query) use ($status,$user_type) {
                            if ($status) {  $query->where('users.status', $status);   }
                            if ($user_type) {  $query->where('users.user_type', $user_type);  }
                        }) 
                        ->where(function($query1) use ($search){
                            $query1->where('users.title', 'LIKE', '%'.$search.'%');
                            $query1->orWhere('users.name', 'LIKE', '%'.$search.'%');
                            $query1->orWhere('users.first_name', 'LIKE', '%'.$search.'%');
                            $query1->orWhere('users.last_name', 'LIKE', '%'.$search.'%'); 
                            $query1->orWhere('users.username', 'LIKE', '%'.$search.'%'); 
                            $query1->orWhere('users.email', 'LIKE', '%'.$search.'%'); 
                            $query1->orWhere('users.phone', 'LIKE', '%'.$search.'%'); 
                        })
                        ->select('users.*','users.user_id as  member_id','users.id as  u_id','user_details.*')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get(); 
                       

            $totalFiltered = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                        ->where(function ($query) use ($status,$user_type) {
                            if ($status) {   $query->where('users.status', $status); }
                            if ($user_type) {  $query->where('users.user_type', $user_type);  } 
                        }) 
                        ->where(function($query) use ($search){
                            $query->where('users.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('users.name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('users.first_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('users.last_name', 'LIKE', '%'.$search.'%'); 
                            $query->orWhere('users.username', 'LIKE', '%'.$search.'%'); 
                            $query->orWhere('users.email', 'LIKE', '%'.$search.'%'); 
                            $query->orWhere('users.phone', 'LIKE', '%'.$search.'%');     
                        })
                        ->count(); 
        }
        
        
        $data = array();
        if(!empty($posts)){

            $i=1; 
            foreach ($posts as $user) {  
  
                if($user->email_verify=="yes"){
                    $user_type = '<button data-toggle="tooltip" data-placement="top" title="User is Verified" class="btn btn-sm btn-success"> ' .  ucwords($user->email_verify) . '</button>';
                } else {
                	$user_type = '<button data-toggle="tooltip" data-placement="top" title="User is not Verified" class="btn btn-sm btn-warning">' .  ucwords($user->email_verify) . '</button>';
                }    

                if($user->status == "active"){
                    $status  = '<strong style="color:green;">Active</strong>';
                } else {
                    $status  = ' <strong style="color:red;">Inactive</strong>';  
                }

                $name =  $user->title . ' ' . $user->first_name . ' ' . $user->last_name;
              
                $success = '<a href="javascript:;"  data-name=" ' . $name . ' " data-email=" ' . $user->email . ' "  data-subject="' . $user->editorial_subject . '"  data-success=" ' . $user->editorial_note . ' "    style="background: white;color: #046cab;font-size: 20px;"  class=" send_mail"  data-toggle="modal" data-target="#send-mail"  data-placement="top" data-title="Send Mail to ' . $user->name . '" ><i class="fas fa-fw fa fa-envelope"  ></i></a>';  
               
                
                    if($user->resume){
                        $resume = '<a href="'.url('').'/public/uploads/resume/'.@$user->resume.'"  download class="blue" > <i class="fa fa-download" aria-hidden="true"></i> Download Resume</a>';  
                    } else  { 
                        $resume = '<a href="javascript:void(0);"  class="black" > <i class="fa fa-download" aria-hidden="true"></i> Download Resume</a>';
                    }
  
                    if($user->consent){
                        $consent = ' <a href="'.url('').'/public/uploads/consent/'.@$user->consent.'" download class="blue" ><i class="fa fa-download" aria-hidden="true"></i> Download Consent </a>';
                    } else  { 
                        $consent = ' <a href="javascript:void(0);"   class="black" ><i class="fa fa-download" aria-hidden="true"></i> Download Consent </a>';
                    }  
                 if($user->photo_id){
                    $photo_id = '<a href="'.url('').'/public/uploads/photo_id/'.@$user->photo_id.'" download class="blue" ><i class="fa fa-download" aria-hidden="true"></i> Download Photo ID </a>';  
                 } else  { 
                    $photo_id = '<a href="javascript:void(0);" class="black" ><i class="fa fa-download" aria-hidden="true"></i> Download Photo ID </a>';
                  }   
                  
                  if($user->leaving_date){
                       $leaving_status  = "Left on ".date('d M Y',strtotime($user->leaving_date));
                       $leaving_date  =   date('d M Y',strtotime($user->leaving_date));
                       $leaving_dates  =   date('d M Y',strtotime($user->leaving_date));
                  } else{
                       $leaving_status  = "In Service";
                       $leaving_date  =   'N/A';  
                       $leaving_dates  = "In Service";
                  } 

                $assignment_period = $this->calculateDateDifference($user->join_date,$leaving_date); 
 
                $total_experience = $this->total_experience($user->u_id); 

                $nestedData['name'] =  '<div class="row  staff-listing">

                                <div class="col-md-12 main-haed" >
                                    <h4>' . $name . '</h4> 
                                    <h4> <span style="font-weight: 700;color: #464646;" > Designation  : </span> <span >'.$user->post_title.'</span></h4> 
                                     <h4> <span style="font-weight: 700;color: #464646;"  >Job Status : </span> <span >  '.$leaving_status.' </span></h4> 
                                    <h4> <span style="font-weight: 700;color: #464646;"> Member ID : </span> <span > '.$user->member_id.' </span></h4> 
                                </div>

                                <div class="col-md-3">
                                        <h5 style="text-align: center;margin: 0px 0px 10px 0px;">   </h5>
                                        <img src="'.url('').'/public/uploads/user/'.$user->photo.'" alt="'.$user->name.'"  style=" height: 220px;width: 200px;border: 1px solid #aba7a7;padding:5px;object-fit:contain;">
                                </div> 

                                <div class="col-md-3">
                                      <div class="mt-4">  
                                       
                                         <h5>Contact No :</h5> <p > '.$user->phone.' </p>
                                         <h5>E-mail : </h5> <p> '.$user->email.'  </p> 
                                         <h5>Father/Spouse  : </h5> <p> '.$user->father_spouse.'  </p>  
                                         <h5>Nature of Post  : </h5> <p> '.$user->post_nature.'  </p>  
                                        
                                      </div>
                                </div>

                            <div class="col-md-3">
                                <div class="mt-4"> 
                                    <h5>Application Date :</h5>
                                    <p>'. date('d M Y',strtotime($user->created_at)) .'</p>

                                    <h5>Joining Date :</h5>
                                    <p>'. date('d M Y',strtotime($user->join_date)).'</p>

                                    <h5>Leaving Date  :</h5>
                                    <p>'. $leaving_dates .'</p> 
                                   
                                    <h5  >Service Period  :</h5>
                                    <p>  '.$assignment_period.'</p> 
                                 </div> 
                            </div>  

                            <div class="col-md-3"> 
                                <div class="mt-1">
                                    <ul class="editr-btn">
                                        <li>  '. $resume .' </li>
                                          <li>  '. $photo_id .' </li> 
                                        <li> <a href=" '.url('/downlaod-staff-certificate').'/'.$user->u_id.'"> <i class="fa fa-download" aria-hidden="true"></i>  Download Certificate </a> </li>
                                        <li> <a href=" '.url('/send-staff-certificate').'/'.$user->u_id.'"> <i class="fa fa-envelope" aria-hidden="true"></i> Send Certificate </a> </li>
                                        <li> <a  href="javascript:;" data-href=" '.url('').'/admin/manage-staff/'.$user->u_id.'/archive" data-toggle="modal" data-target="#confirm-archive"   data-placement="top" title="Move to Archive" >  <i class="fa fa-archive" aria-hidden="true"></i>  Move to Archive</a> </li>
                                        <li style="border: none;" > 
                                               <h5> Total Experience :  </h5>
                                               <p>  '.$total_experience.' </p>  
                                          </li>
                                    </ul> 
                                </div> 
                            </div>   



                            <div class="col-md-12  mt-3 footer-line"> 
                                <div class="row"> 
                                    <div class="col-md-8"> 
                                    <p> <strong style="color: #dd0000;" >  '.$success.'  Send Message  :</strong> '.@$user->editorial_note.'</p> 
                                      
                                    </div>
                                    <div class="col-md-4"> 
                                        <ul class="btn-fotr">   
                                            <li> <a href="#"> ' . $status . ' </a> </li> 
                                            <li style="background: green;border: 0;" > <a  href="'.url('').'/admin/manage-staff/'.$user->u_id.'/edit" data-toggle="tooltip" data-placement="top" title="Edit" style="color: white">  Edit</a></li>
                                            <li style="background: red;border: 0;" >  <a  href="javascript:;" data-href=" '.url('').'/admin/manage-staff/'.$user->u_id.'/delete" data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete"  style="color: white">Delete</a></li>
                                        </ul>
                                    </div>
                                 </div>
                            </div> 
                </div> ';    
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
 
  
     public function create(Request $request){    
        $data['designation']  = StaffDesignation::orderby('name','ASC')->get();
         return view('admin.staff.add',$data);
	}
	 
   public function store(Request $request) {  
             request()->validate([ 
                 'username' => 'required|min:6|unique:users,username',
                 'email' => 'required|email|unique:users',
                 'password' => 'required|min:6',                
                 'confirm_password' => 'required|min:6|same:password',
            ]); 
            $user_id =  "RFP".rand(1234,5678).date('y');
            $user = new User;
            $user->username = $request->username;
            $user->user_id = $user_id;
            $user->name = $request->username; 
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;   
            $user->password = Hash::make($request->password);
            $user->title = $request->title;
            $user->father_spouse = $request->father_spouse; 
            $user->phone = $request->phone; 
            $user->country_code = $request->country_code;
            $user->login_type = 'staff';
            $user->user_type = 'staff'; 
            $user->status = 'active';
            $user->email_verify = 'yes';
            $user->email_verified_at = date('Y-m-d H:i:s');
            if($file = $request->file('photo')){  
                $originalName = $request->file('photo')->getClientOriginalName();
                $extension = $request->file('photo')->getClientOriginalExtension(); 
                $uniqueName = uniqid() . '.' . $extension; 
                $uniqueName = str_replace(' ', '', $uniqueName); 
                $file->move(public_path('uploads/user'), $uniqueName);  
                $user->photo = $uniqueName;
            }  
            $user->save(); 
            $last_id = $user->id;
            
            // Create UserDetails and associate it with the user  
            $userDetails = new UserDetails;
            $userDetails->user_id = $last_id; 
            $userDetails->post_nature = $request->post_nature; 
            $userDetails->post_title = $request->post_title;  
            $userDetails->about = $request->about; 
            $userDetails->editorial_note = $request->editorial_note; 
            $userDetails->dob = $request->dob; 
            $userDetails->gender = $request->gender;  
            $userDetails->join_date = $request->join_date; 
            $userDetails->leaving_date = $request->leaving_date;  
            // Assign other properties as needed
            
            if ($file = $request->file('resume')){   
                $extension1 = $file->getClientOriginalExtension(); 
                $uniqueName1 = $last_id . '_' . uniqid() . '.' . $extension1; 
                $uniqueName1 = str_replace(' ', '', $uniqueName1); 
                $file->move(public_path('uploads/resume'), $uniqueName1); 
                $userDetails->resume = $uniqueName1;
            }  
            if ($file = $request->file('photo_id')){   
                $extension3 = $file->getClientOriginalExtension(); 
                $uniqueName3 = $last_id . '_' . uniqid() . '.' . $extension3; 
                $uniqueName3 = str_replace(' ', '', $uniqueName3); 
                $file->move(public_path('uploads/photo_id'), $uniqueName3); 
                $userDetails->photo_id = $uniqueName3;
            }  

            $userDetails->save();  
 
            $post_title = $request->post_title; 
            $title =  $request->title; 
            $first_name =  $request->first_name; 
            $last_name =  $request->last_name; 
            $email =  $request->email; 
            $settings  = DB::table("settings")->first();
            $array['title']    =  ' Staff Application Submission';  
            $array['position']    =   $post_title ?? ''; 
            $array['name']     =      $title . ' ' . $first_name . ' ' . $last_name; 
            $array['email']    =     $email;  
            $array['sender_email'] = $settings->sender_email; 
            $array['sender_name'] =  $settings->sender_name;   
            $mail =  Mail::send('email.staff_creation_mail', $array, function($success)use($array) {
                  $success->to($array['email'])
                          ->subject($array['title'])
                           ->cc($array['sender_email'])
                          ->from($array['sender_email'],$array['sender_name']);
              });   
           return redirect('admin/manage-staff')->with('success',' Staff Account Created Successfully.');
    }   
 
     

	 
    public function edit($id){ 
         $data['data']  =  User::where('id',$id)->first(); 
         $data['designation']  = StaffDesignation::orderby('name','ASC')->get(); 
		return view('admin.staff.edit',$data);
	}

 
	 
   public function manage_staff_update(Request $request){  
        $rules = [ 'username' => 'unique:users,username,'.$request->id,
                     'email' => 'unique:users,email,'.$request->id]; 
         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
			return redirect()->back()->with('danger', 'This Email Already Exist');   
         }
		 $id =  $request->id;
         $User = User::findOrFail($id);
         $data = $request->all(); 
         if ($file = $request->file('photo')){   
            if (file_exists(public_path('uploads/user/'.$User->photo)))  { 
					@unlink('public/uploads/user/'.$User->photo); 
		     }  
            $originalName = $request->file('photo')->getClientOriginalName();
            $extension = $request->file('photo')->getClientOriginalExtension(); 
            $uniqueName = uniqid() . '.' . $extension; 
            $uniqueName = str_replace(' ', '', $uniqueName); 
            $file->move(public_path('uploads/user'), $uniqueName);  
            $data['photo'] = $uniqueName;
         } 
	     $Update = $User->update($data); 
         $userDetailsData['about'] = $request->about; 
         $userDetailsData['editorial_note'] = $request->editorial_note; 
         $userDetailsData['dob'] = $request->dob; 
         $userDetailsData['post_nature'] = $request->post_nature;
         $userDetailsData['post_title'] = $request->post_title; 
         $userDetailsData['gender'] = $request->gender; 
         $userDetailsData['join_date'] = $request->join_date; 
         $userDetailsData['leaving_date'] = $request->leaving_date;  
      

         if ($file = $request->file('resume')){
            if (file_exists(public_path('uploads/resume/'.$User->userDetails->resume)))  { 
                @unlink('public/uploads/resume/'.$User->userDetails->resume); 
            }  
            $extension1 = $file->getClientOriginalExtension(); 
            $uniqueName1 = $request->id . '_' . uniqid() . '.' . $extension1; 
            $uniqueName1 = str_replace(' ', '', $uniqueName1); 
            $file->move(public_path('uploads/resume'), $uniqueName1); 
            $userDetailsData['resume'] = $uniqueName1;
        }  

        if ($file = $request->file('photo_id')){   
            if (file_exists(public_path('uploads/photo_id/'.$User->userDetails->photo_id)))  { 
                @unlink('public/uploads/photo_id/'.$User->userDetails->consent); 
            }   
            $extension3 = $file->getClientOriginalExtension(); 
            $uniqueName3 = $request->id . '_' . uniqid() . '.' . $extension3; 
            $uniqueName3 = str_replace(' ', '', $uniqueName3); 
            $file->move(public_path('uploads/photo_id'), $uniqueName3); 
            $userDetailsData['photo_id'] = $uniqueName3;
        } 


        $User->userDetails()->update($userDetailsData); 

		return redirect()->back()->with('success', 'Record Updated Successfully');   
    }
    
	   
    //Delete User 
    public function delete_staff($id) {
 
        $user = User::findOrFail($id);
        $userDetails = $user->userDetails;
        if ($userDetails) {
            return back()->with('danger', 'Cannot delete staff Account, because related details exist in another tables.');
        } 
		// if (file_exists(public_path('uploads/staff/'.$user->photo)))  { 
		// 			@unlink('public/uploads/staff/'.$user->photo); 
		//  } 
		// $user->delete();
        return redirect('admin/manage-staff')->with('success','Record  Deleted Successfully.');
    } 


	public function archive_staff($id) {
        $user = User::findOrFail($id); 
        $data['status'] = 'inactive'; 
        $Update = $user->update($data); 
        return redirect('admin/manage-staff')->with('success','Record Moved to Archive Successfully.');
    }

    public function remove_archive_staff($id) {
        $user = User::findOrFail($id); 
        $data['status'] = 'active'; 
        $Update = $user->update($data); 
        return redirect('admin/manage-staff-archive')->with('success','Record Removed from Archive Page.');
    }


    // 
      

  /*   User Address Information Save */
    
        public function staff_address($id){ 
            $data['data']  = User::where('id',$id)->first();
            $data['address']  = UserAddress::where('user_id',$id)->get();
            return view('admin.staff.address_list',$data);
        }
        
        public function staff_address_add($id){
            $data['data']  =  User::select('id','title','first_name','last_name')->where('id',$id)->first();
            $data['country']  =  Country::orderBy('countries_name','ASC')->get();
            return view('admin.staff.address_add',$data);
        }
    
        public function staff_address_save(Request $request) { 
            $data = new  UserAddress;
            $data->fill($request->all());
            $data->save();
            return redirect('admin/manage-staff/'.$request->user_id.'/address')->with('success','Record Created Successfully.'); 
        }
     
        public function staff_address_edit($id,$slug){ 
                $data['data']  = User::where('id',$id)->first();
                $data['address']  =  UserAddress::where('id',$slug)->first();
                $data['country']  =  Country::orderBy('countries_name','ASC')->get();
                return view('admin.staff.address_edit',$data);
        } 

        public function staff_address_update(Request $request){ 
                    $UserAddress = UserAddress::findOrFail($request->id);
                    $data = $request->all();    
                    $UserAddress->update($data); 
                return redirect('admin/manage-staff/'.$request->user_id.'/address')->with('success','Record Updated Successfully.'); 
         }
 
        public function staff_address_delete($id,$slug){ 
            $data = UserAddress::findOrFail($slug); 
            $data->delete();
        return redirect(url('admin/manage-staff/'.$id.'/address'))->with('success','Record Deleted Successfully.'); 
        }


  /*   User Address Information Save */



  
  /*   User qualifications Information Save */
    
  public function staff_qualification($id){ 
    $data['data']  = User::where('id',$id)->first();
    $data['qualifications']  = UserQualification::where('user_id',$id)->get(); 
    return view('admin.staff.qualifications_list',$data);
}

public function staff_qualification_add($id){
    $data['data']  =  User::select('id','title','first_name','last_name')->where('id',$id)->first();
    return view('admin.staff.qualifications_add',$data);
}

public function staff_qualification_save(Request $request) { 
    $data = new  UserQualification;
    $data->fill($request->all());
    $data->save();
    return redirect('admin/manage-staff/'.$request->user_id.'/qualification')->with('success','Record Created Successfully.'); 
}

public function staff_qualification_edit($id,$slug){ 
        $data['data']  = User::where('id',$id)->first();
        $data['qualifications']  =  UserQualification::where('id',$slug)->first(); 
        return view('admin.staff.qualifications_edit',$data);
} 

public function staff_qualification_update(Request $request){ 
            $UserQualification = UserQualification::findOrFail($request->id);
            $data = $request->all();    
            $UserQualification->update($data); 
        return redirect('admin/manage-staff/'.$request->user_id.'/qualification')->with('success','Record Updated Successfully.'); 
 }

public function staff_qualification_delete($id,$slug){ 
    $data = UserQualification::findOrFail($slug); 
    $data->delete();
return redirect(url('admin/manage-staff/'.$id.'/qualification'))->with('success','Record Deleted Successfully.'); 
}


/*   User qualification Information Save */




        
  /*   User experience Information Save */
            
        public function staff_experience($id){ 
            $data['data']  = User::where('id',$id)->first();
            $data['experience']  = UserExperience::where('user_id',$id)->get();
            return view('admin.staff.experience_list',$data);
        }

        public function staff_experience_add($id){
            $data['data']  =  User::select('id','title','first_name','last_name')->where('id',$id)->first();
            $data['country']  =  Country::orderBy('countries_name','ASC')->get();
            return view('admin.staff.experience_add',$data);
        }

        public function staff_experience_save(Request $request) { 
            $data = new  UserExperience;
            $data->fill($request->all());
            $data->save();
            return redirect('admin/manage-staff/'.$request->user_id.'/experience')->with('success','Record Created Successfully.'); 
        }

        public function staff_experience_edit($id,$slug){ 
                $data['data']  = User::where('id',$id)->first();
                $data['experience']  =  UserExperience::where('id',$slug)->first();
                $data['country']  =  Country::orderBy('countries_name','ASC')->get();
                return view('admin.staff.experience_edit',$data);
        } 

        public function staff_experience_update(Request $request){  
                $UserExperience = UserExperience::findOrFail($request->id);
                $data = $request->all();    
                $UserExperience->update($data); 
                return redirect('admin/manage-staff/'.$request->user_id.'/experience')->with('success','Record Updated Successfully.'); 
        }

        public function staff_experience_delete($id,$slug){ 
            $data = UserExperience::findOrFail($slug); 
            $data->delete();
        return redirect(url('admin/manage-staff/'.$id.'/experience'))->with('success','Record Deleted Successfully.'); 
        }

 



    /* manage_designation Save */
            
        public function manage_staff_designation(){  
            $data['data']  = StaffDesignation::orderby('name','ASC')->get();
            return view('admin.staff.designation_list',$data);
        }

        public function staff_designation_add(){
            return view('admin.staff.designation_add');
        }

        public function staff_designation_save(Request $request) { 
            $data = new  StaffDesignation;
            $data->fill($request->all());
            $data->save();
            return redirect('admin/manage-designation')->with('success','Record Created Successfully.'); 
        }

        public function staff_designation_edit($id){ 
           $data['data']  = StaffDesignation::where('id',$id)->first(); 
           return view('admin.staff.designation_edit',$data);
        } 

        public function staff_designation_update(Request $request){  
                $designation = StaffDesignation::findOrFail($request->id);
                $data = $request->all();    
                $designation->update($data); 
                return redirect('admin/manage-designation')->with('success','Record Updated Successfully.'); 
        }

        public function staff_designation_delete($id){ 
            $data = StaffDesignation::findOrFail($id); 
            $data->delete();
               return redirect(url('admin/manage-designation'))->with('success','Record Deleted Successfully.'); 
        }
  
       
      ///////////////////////////////////////////////


      
  

   public function manage_staff_archive(){ 
       return view('admin.staff.archive_list'); 
   }
  
   //All jobs with ajax   Show all Jobs  with ajxa data table
   public function manage_staff_archive_list(Request $request){  
       $status =  'inactive'; 
       $user_type = 'staff';  
       $totalData = User::leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                   ->where(function ($query) use ($status,$user_type) {
                       if ($status) { $query->where('users.status', $status); }
                       if ($user_type) { $query->where('users.user_type', $user_type);  }
                   })->count(); 
       $totalFiltered = $totalData;
       $totalFiltered = $totalData;
       $limit = $request->input('length');
       $start = $request->input('start');  
       $order = 'users.id';
       $dir = 'desc'; 

       $search = $request->input('search.value'); 

       if(empty($search)) {
           
                   $posts = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                           ->where(function ($query) use ($status,$user_type) {
                               if ($status) {  $query->where('users.status', $status);  }
                               if ($user_type) {   $query->where('users.user_type', $user_type);   }
                           })
                           ->select('users.*','users.user_id as  member_id','users.id as  u_id','user_details.*')
                           ->offset($start)
                           ->limit($limit)
                           ->orderBy($order,$dir)
                           ->get(); 


                       $totalFiltered = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                       ->where(function ($query) use ($status,$user_type) {
                           if ($status) {  $query->where('users.status', $status);  }
                           if ($user_type) {  $query->where('users.user_type', $user_type);  }
                       })
                       ->count(); 
       } else{

           $posts = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                       ->where(function ($query) use ($status,$user_type) {
                           if ($status) {  $query->where('users.status', $status);   }
                           if ($user_type) {  $query->where('users.user_type', $user_type);  }
                       }) 
                       ->where(function($query1) use ($search){
                           $query1->where('users.title', 'LIKE', '%'.$search.'%');
                           $query1->orWhere('users.name', 'LIKE', '%'.$search.'%');
                           $query1->orWhere('users.first_name', 'LIKE', '%'.$search.'%');
                           $query1->orWhere('users.last_name', 'LIKE', '%'.$search.'%'); 
                           $query1->orWhere('users.username', 'LIKE', '%'.$search.'%'); 
                           $query1->orWhere('users.email', 'LIKE', '%'.$search.'%'); 
                           $query1->orWhere('users.phone', 'LIKE', '%'.$search.'%'); 
                       })
                       ->select('users.*','users.user_id as  member_id','users.id as  u_id','user_details.*')
                       ->offset($start)
                       ->limit($limit)
                       ->orderBy($order,$dir)
                       ->get(); 
                      

           $totalFiltered = User::leftjoin('user_details', 'users.id', '=', 'user_details.user_id')
                       ->where(function ($query) use ($status,$user_type) {
                           if ($status) {   $query->where('users.status', $status); }
                           if ($user_type) {  $query->where('users.user_type', $user_type);  } 
                       }) 
                       ->where(function($query) use ($search){
                           $query->where('users.title', 'LIKE', '%'.$search.'%');
                           $query->orWhere('users.name', 'LIKE', '%'.$search.'%');
                           $query->orWhere('users.first_name', 'LIKE', '%'.$search.'%');
                           $query->orWhere('users.last_name', 'LIKE', '%'.$search.'%'); 
                           $query->orWhere('users.username', 'LIKE', '%'.$search.'%'); 
                           $query->orWhere('users.email', 'LIKE', '%'.$search.'%'); 
                           $query->orWhere('users.phone', 'LIKE', '%'.$search.'%');     
                       })
                       ->count(); 
       }
       
       
       $data = array();
       if(!empty($posts)){

           $i=1; 
           foreach ($posts as $user) {  
 
               if($user->email_verify=="yes"){
                   $user_type = '<button data-toggle="tooltip" data-placement="top" title="User is Verified" class="btn btn-sm btn-success"> ' .  ucwords($user->email_verify) . '</button>';
               } else {
                   $user_type = '<button data-toggle="tooltip" data-placement="top" title="User is not Verified" class="btn btn-sm btn-warning">' .  ucwords($user->email_verify) . '</button>';
               }    

               if($user->status == "active"){
                   $status  = '<strong style="color:green;">Active</strong>';
               } else {
                   $status  = ' <strong style="color:red;">Inactive</strong>';  
               }
               $name =  $user->title . ' ' . $user->first_name . ' ' . $user->last_name;
               $success = '<a href="javascript:;"  data-name=" ' . $name . ' " data-email=" ' . $user->email . ' " data-subject="' . $user->editorial_subject . '"   data-success=" ' . $user->editorial_note . ' "   style="background: white;color: #046cab;font-size: 20px;"  class=" send_mail"  data-toggle="modal" data-target="#send-mail"  data-placement="top" title="Send Mail to ' . $user->email . '" ><i class="fas fa-fw fa fa-envelope"  ></i></a>';  
              
               
                   if($user->resume){
                       $resume = '<a href="'.url('').'/public/uploads/resume/'.@$user->resume.'"  download class="blue" > <i class="fa fa-download" aria-hidden="true"></i> Download Resume</a>';  
                   } else  { 
                       $resume = '<a href="javascript:void(0);"  class="black" > <i class="fa fa-download" aria-hidden="true"></i> Download Resume</a>';
                   }
 
                   if($user->consent){
                       $consent = ' <a href="'.url('').'/public/uploads/consent/'.@$user->consent.'" download class="blue" ><i class="fa fa-download" aria-hidden="true"></i> Download Consent </a>';
                   } else  { 
                       $consent = ' <a href="javascript:void(0);"   class="black" ><i class="fa fa-download" aria-hidden="true"></i> Download Consent </a>';
                   }  
                if($user->photo_id){
                   $photo_id = '<a href="'.url('').'/public/uploads/photo_id/'.@$user->photo_id.'" download class="blue" ><i class="fa fa-download" aria-hidden="true"></i> Download Photo ID </a>';  
                } else  { 
                   $photo_id = '<a href="javascript:void(0);" class="black" ><i class="fa fa-download" aria-hidden="true"></i> Download Photo ID </a>';
                 }   
                 if($user->leaving_date){
                    $leaving_status  = "Left on ".date('d M Y',strtotime($user->leaving_date));
                    $leaving_date  =   date('d M Y',strtotime($user->leaving_date));
                    $leaving_dates  =   date('d M Y',strtotime($user->leaving_date));
                    
                } else{
                  $leaving_status  = "In Service";
                  $leaving_date  =   'N/A';
                  $leaving_dates  =   'In Service';
                  
                }  
               $assignment_period = $this->calculateDateDifference($user->join_date,$leaving_date); 
               $nestedData['name'] =  '<div class="row  staff-listing">

               <div class="col-md-12 main-haed" >
                   <h4>' . $name . '</h4> 
                   <h4> <span style="font-weight: 700;color: #464646;" > Designation  : </span> <span >'.$user->post_title.'</span></h4> 
                    <h4> <span style="font-weight: 700;color: #464646;"  >Job Status : </span> <span >  '.$leaving_status.' </span></h4> 
                   <h4> <span style="font-weight: 700;color: #464646;"> Member ID : </span> <span > '.$user->member_id.' </span></h4> 
               </div>

               <div class="col-md-3">
                       <h5 style="text-align: center;margin: 0px 0px 10px 0px;">   </h5>
                       <img src="'.url('').'/public/uploads/user/'.$user->photo.'" alt="'.$user->name.'"  style=" height: 220px;width: 200px;border: 1px solid #aba7a7;padding:5px;object-fit:contain;">
               </div> 

               <div class="col-md-3">
                     <div class="mt-4">  
                      
                        <h5>Contact No :</h5> <p > '.$user->phone.' </p>
                        <h5>E-mail : </h5> <p> '.$user->email.'  </p> 
                        <h5>Father/Spouse  : </h5> <p> '.$user->father_spouse.'  </p>  
                        <h5>Nature of Post  : </h5> <p> '.$user->post_nature.'  </p>  
                       
                     </div>
               </div>

           <div class="col-md-3">
               <div class="mt-4"> 
                   <h5>Application Date :</h5>
                   <p>'. date('d M Y',strtotime($user->created_at)) .'</p>

                   <h5>Joining Date :</h5>
                   <p>'. date('d M Y',strtotime($user->join_date)).'</p>

                   <h5>Leaving Date  :</h5>
                   <p>'. $leaving_dates .'</p> 
                  
                   <h5  >Service Period  :</h5>
                   <p>  '.$assignment_period.'</p> 
                </div> 
           </div>  

           <div class="col-md-3"> 
               <div class="mt-1">
                   <ul class="editr-btn">
                       <li>  '. $resume .' </li>
                        <li>  '. $photo_id .' </li>
                        <li> <a  href="javascript:;" data-href=" '.url('').'/admin/manage-staff/'.$user->u_id.'/remove_archive" data-toggle="modal" data-target="#confirm-archive"   data-placement="top" title="Move to Archive" >  <i class="fa fa-archive" aria-hidden="true"></i>  Remove from Archive</a> </li>
                         <li style="border: none;" > 
                              <h5  >Total Experience : </h5>
                              <p>  </p>  
                         </li>
                   </ul> 
               </div> 
           </div>    
           <div class="col-md-12  mt-3 footer-line"> 
               <div class="row"> 
                   <div class="col-md-8"> 
                   <p> <strong style="color: #dd0000;" >  '.$success.'  Send Message  :</strong> '.@$user->editorial_note.'</p> 
                     
                   </div>
                   <div class="col-md-4"> 
                       <ul class="btn-fotr">   
                           <li> <a href="#"> ' . $status . ' </a> </li>
                           <li style="background: green;border: 0;" > <a  href="'.url('').'/admin/manage-staff/'.$user->u_id.'/edit" data-toggle="tooltip" data-placement="top" title="Edit" style="color: white">  Edit</a></li>
                           <li style="background: red;border: 0;" >  <a  href="javascript:;" data-href=" '.url('').'/admin/manage-staff/'.$user->u_id.'/delete" data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete"  style="color: white">Delete</a></li>
                       </ul>
                   </div>
                </div>
           </div> 
</div> ';     
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
 
   public function manage_staff_archive_edit($id){ 
        $data['data']  =  User::where('id',$id)->first(); 
        $data['designation']  = StaffDesignation::orderby('name','ASC')->get(); 
       return view('admin.staff.archive_edit',$data);
   }
 
  public function manage_staff_archive_update(Request $request){

       $rules = [ 'username' => 'unique:users,username,'.$request->id,
                    'email' => 'unique:users,email,'.$request->id]; 
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return redirect()->back()->with('error', 'This Email Already Exist');   
        }
        $id =  $request->id;
        $User = User::findOrFail($id);
        $data = $request->all(); 
        if ($file = $request->file('photo')){   
           if (file_exists(public_path('uploads/user/'.$User->photo)))  { 
                   @unlink('public/uploads/user/'.$User->photo); 
            }  
           $originalName = $request->file('photo')->getClientOriginalName();
           $extension = $request->file('photo')->getClientOriginalExtension(); 
           $uniqueName = uniqid() . '.' . $extension; 
           $uniqueName = str_replace(' ', '', $uniqueName); 
           $file->move(public_path('uploads/user'), $uniqueName);  
           $data['photo'] = $uniqueName;
        } 
        $Update = $User->update($data); 
        $userDetailsData['about'] = $request->about; 
        $userDetailsData['editorial_note'] = $request->editorial_note; 
        $userDetailsData['dob'] = $request->dob; 
        $userDetailsData['post_nature'] = $request->post_nature;
        $userDetailsData['post_title'] = $request->post_title; 
        $userDetailsData['gender'] = $request->gender; 
        $userDetailsData['join_date'] = $request->join_date; 
        $userDetailsData['leaving_date'] = $request->leaving_date;  

        if ($file = $request->file('resume')){
           if (file_exists(public_path('uploads/resume/'.$User->userDetails->resume)))  { 
               @unlink('public/uploads/resume/'.$User->userDetails->resume); 
           }  
           $extension1 = $file->getClientOriginalExtension(); 
           $uniqueName1 = $request->id . '_' . uniqid() . '.' . $extension1; 
           $uniqueName1 = str_replace(' ', '', $uniqueName1); 
           $file->move(public_path('uploads/resume'), $uniqueName1); 
           $userDetailsData['resume'] = $uniqueName1;
       }  

       if ($file = $request->file('photo_id')){   
        if (file_exists(public_path('uploads/photo_id/'.$User->userDetails->photo_id)))  { 
            @unlink('public/uploads/photo_id/'.$User->userDetails->consent); 
        }   
        $extension3 = $file->getClientOriginalExtension(); 
        $uniqueName3 = $request->id . '_' . uniqid() . '.' . $extension3; 
        $uniqueName3 = str_replace(' ', '', $uniqueName3); 
        $file->move(public_path('uploads/photo_id'), $uniqueName3); 
        $userDetailsData['photo_id'] = $uniqueName3;
       } 

       $User->userDetails()->update($userDetailsData); 
       return redirect()->back()->with('success', 'Record Updated Successfully');   
   }
   
      
   //Delete User 
   public function manage_staff_archive_delete($id) {
       $user = User::findOrFail($id);
       $userDetails = $user->userDetails;
       if ($userDetails) {
           return back()->with('danger', 'Cannot delete staff Account, because related details exist in another tables.');
       }
       return redirect('admin/manage-staff-archive')->with('success','Record  Deleted Successfully.');
   } 
   

     
        public static function get_journal_name($id) { 
            return  Journals::findOrFail($id)->title ?? 'N/A';   
        }


        public static function get_position_name($id) { 
            return 'N/A';   
        }

        //
        public static function total_experience($id) { 

            $experience  = UserExperience::where('user_id',$id)->get(); 

            if($experience){

                $totalYears = 0;
                $totalMonths = 0;
                $totalDays = 0; 

               foreach($experience as $key=>$val){ 

                            if($val->leaving_date) {
                                    $joining_date = $val->joining_date;
                                    $leaving_date = $val->leaving_date;
                                    $start = new DateTime($joining_date);
                                    $end = new DateTime($leaving_date);
                                    $interval = $start->diff($end); 
                             } else {
                                    $start = new DateTime($val->joining_date);
                                    $end = new DateTime();  
                                    $interval = $start->diff($end); 
                             } 
                            $totalYears += $interval->y;
                            $totalMonths += $interval->m;
                            $totalDays += $interval->d;   
                }}
                

                    if ($totalDays >= 30) {
                        $totalMonths += floor($totalDays / 30);
                        $totalDays %= 30;
                    }

                    // Adjust totalYears if totalMonths exceed 12
                    if ($totalMonths >= 12) {
                        $totalYears += floor($totalMonths / 12);
                        $totalMonths %= 12;
                    }

                    // Display the total time difference
                    return  "$totalYears years, $totalMonths months, $totalDays days"; 
                    
        }



        public static function calculateDateDifference($specificDate, $currentDate = 'N/A') {
            // Convert the specific date to a Carbon instance
            $specificDate = Carbon::parse($specificDate);
        
            // Get the current date
            if ($currentDate == 'N/A') {
                $currentDate = Carbon::now();
            } else {
                $currentDate = Carbon::parse($currentDate);
            }
        
            // Calculate the difference in years, months, and days
            $diff = $currentDate->diff($specificDate);
        
            // Format the result
            $result = '';
            if ($diff->y != 0) {
                $result .= $diff->y . ' years, ';
            }
            if ($diff->m != 0) {
                $result .= $diff->m . ' months, ';
            }
            if ($diff->d != 0) {
                $result .= $diff->d . ' days';
            }
        
            // Remove trailing comma and space if they exist
            $result = rtrim($result, ', ');
        
            return $result;
        }
        




	
}

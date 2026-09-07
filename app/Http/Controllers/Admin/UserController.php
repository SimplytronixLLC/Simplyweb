<?php

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use App\Post;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Helpers\Common;
use Session;
class UserController extends Controller {
     
    public function __construct() {
             $this->middleware('admin')->except('logout');
    }
	
  
	
   public function index(){
	      return view('admin.user.list');
	}
	
	 
	//All jobs with ajax   Show all Jobs  with ajxa data table
    public function user_list(Request $request){ 
        $columns = array( 
                0  => 'name',
                1  => 'email',
                2  => 'phone',
                3  => 'email_verify',
                4  => 'created_at', 
        ); 
        $status =  $request['status'];
        $user_type = $request['user_type'];
        $login_type = $request['login_type'];
        $totalData = User::where('status',$status)->where('user_type',$user_type)->where('login_type',$login_type)->count();
        $totalFiltered = $totalData;
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
    
      //  $order = $columns[$request->input('order.0.column')];
       // $dir = $request->input('order.0.dir'); 
        $order = 'id';
        $dir = 'desc';

        $search = $request->input('search.value'); 
        if(empty($search)) {
            $posts =  User::where('status',$status)->where('user_type',$user_type)->where('login_type',$login_type)->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
            $totalFiltered = User::where('status',$status)->where('user_type',$user_type)->where('login_type',$login_type)->count();
        } else{
            $posts =  User::where('status',$status)->where('user_type',$user_type)->where('login_type',$login_type)
                                   ->where(function($query) use ($search){
                                    $query->where('created_at', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('email', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('phone', 'LIKE', '%'.$search.'%'); 
                            })
                            ->offset($start)
                            ->limit($limit)   
                            ->orderBy($order,$dir)
                            ->get(); 
            $totalFiltered = User::where('status',$status)->where('user_type',$user_type)->where('login_type',$login_type)
                                    ->where(function($query) use ($search){
                                    $query->where('created_at', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('email', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('phone', 'LIKE', '%'.$search.'%');
                            })
                            ->count();
        }
        $data = array();
        if(!empty($posts))  {
            $i=1; 
            foreach ($posts as $user) {  
                if($user->email_verify=="yes"){
                    $verify = '<button data-toggle="tooltip" data-placement="top" title="User is Verified" class="btn btn-sm btn-success"> ' .  ucwords($user->email_verify) . '</button>';
                } else {
                	$verify = '<button data-toggle="tooltip" data-placement="top" title="User is not Verified" class="btn btn-sm btn-warning">' .  ucwords($user->email_verify) . '</button>';
                }                      
                if($user->status != "deactive"){
                    $status  = '<a href="'.url('').'/admin/user/'.$user->id.'/close"   class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Close User Account" ><i class="fa fa-toggle-off"></i>  </a>';
                } else {
                    $status  = '<a href="'.url('').'/admin/user/'.$user->id.'/open"  class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Open User Account" ><i class="fa fa-toggle-on"></i>  </a>';  
                }
                $view = '<a href="'.url('').'/admin/user/'.$user->id.'"     class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="View User"><i class="fa fa-eye"></i>  </a>';  
                $edit  = '<a href="'.url('').'/admin/user/'.$user->id.'/edit"      class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Edit User"><i class="fa fa-edit"></i>  </a>';  
                $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/user/'.$user->id.'/delete"  class="btn btn-sm btn-danger"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete User Account" ><i class="fa fa-trash"  ></i></a>';  
                $message = '<a href="javascript:;"  data-name=" ' . $user->name . ' " data-email=" ' . $user->email . ' "   class="btn btn-sm btn-info send_mail"  data-toggle="modal" data-target="#send-mail"  data-placement="top" title="Send Mail to User" ><i class="fas fa-fw fa fa-envelope"  ></i></a>';  
                $nestedData['name'] =  $user->username;   
                $nestedData['email'] =    '<a  href="'.url('').'/admin/user/'.$user->id.'" > '.$user->email.'</a>';
                $nestedData['phone'] =    '<a href="tel:'.$user->phone.'"> '.$user->phone.'</a>';
                $nestedData['email_verify'] =  $verify;
                $nestedData['created_at'] =  ' <a data-toggle="tooltip"  data-placement="top"  title="'.date('d-m-Y H:i:s',strtotime($user->created_at)) .'">'. date('d M Y',strtotime($user->created_at)) .'</a>'; 
                $nestedData['action'] =   $edit.' '.$status.' '.$message;
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
 

	public function create(){
		return view('admin.user.add');
	}
	 
	public function export_user(){
		$posts  =  User::orderBy('id','desc')->where('login_type','user')->where('status','active')->get();
		if(count($posts) >= 1){
			ob_start();
			ob_get_clean();
			$filename = "User" . date('Y-m-d') . ".xls";
			header('Content-Disposition: attachment; filename="' . $filename . '";');
			header( "Content-Type: application/vnd.ms-excel" );
			echo 'Register Time' . "\t" . 'Account Status' . "\t" . 'Name' . "\t" . 'Type' . "\t" . 'Email' . "\t" . 'Phone' . "\t" . 'Address' . "\t" . 'City' . "\t" . 'State' . "\t" . 'Zipcode' . "\t" . 'Country'  . "\n";
				foreach ($posts as $key => $user) {
					echo date('d-M-Y',strtotime($user->created_at))  . "\t" . ucwords($user->status) . "\t" . $user->name . "\t" . ucwords($user->login_type) . "\t" . $user->email . "\t" . $user->phone . "\t" . $user->address . "\t" . $user->city . "\t" . $user->state . "\t" . $user->zip . "\t" . $user->country . "\n" ;
				}  
			die();
		} else {  
			echo "<script>history.go(-1);</script>";
		}
	}
 
   public function store(Request $request) { 

             request()->validate([ 
                 'username' => 'required|min:4|unique:users,username',
                 'email' => 'required|email|unique:users',
                 'password' => 'required|min:6',                
                 'confirm_password' => 'required|min:6|same:password',
            ]);
            $user = new User;
            $user->name = $request->name; 
            $user->email = $request->email;   
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->login_type = $request->login_type;
            $user->user_type = $request->login_type;
            $user->status = 'active';
            $user->email_verify = 'yes';
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save(); 
           return redirect('admin/user')->with('message',' User Created Successfully.');
    }   
 
    
	public function view($id){
         $data  =  User::where('id',$id)->first();
        return view('admin.user.view',compact('data'));
	}
	
   public function edit($id){
		 $data  =  User::where('id',$id)->first();
        return view('admin.user.edit',compact('data'));
	}
	
	 
   public function update_admin_user(Request $request){
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
				if($User->photo != 'user.png') {
					@unlink('public/uploads/user/'.$User->photo);
				}  
			 }
            $photo =  uniqid() . $request->file('photo')->getClientOriginalName();
            $file->move('public/uploads/user',$photo);
            $data['photo'] = $photo;
        }
		
	     $Update =   $User->update($data);
		return redirect()->back()->with('message', 'Record Updated Successfully');   
    }
    
	
     //Cloase User 
    public function close($id) {
        $data = User::findOrFail($id);
        $record['status'] = "deactive";
        $data->update($record);
        return redirect('admin/user')->with('message','Record Bocked Successfully.');
    }
	
	
    // Open User 
    public function open($id) { 
        $data = User::findOrFail($id);
        $record['status'] = "active";
        $data->update($record);
        return redirect('admin/user')->with('message','Record UnBocked Successfully.');
    }
	
	
    //Delete User 
    public function destroy($id) {
        $Record = User::findOrFail($id);
		if (file_exists(public_path('uploads/user/'.$Record->photo)))  {
				if($Record->photo != 'user.png') {
					@unlink('public/uploads/user/'.$Record->photo);
				}  
		 }


		$Record->delete();
        return redirect('admin/user')->with('message','Record  Deleted Successfully.');
    } 

    public function track($id) { 
        $data = User::findOrFail($id);
        $record['status'] = "active";
        $data->update($record);
        return redirect('admin/user')->with('message','Record UnBocked Successfully.');
    }
	
	
	
}

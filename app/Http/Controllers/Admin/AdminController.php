<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use DB;
use Hash;
class AdminController extends Controller
{
     
    use AuthenticatesUsers;
 
    public function __construct() { 
            $this->middleware('admin')->except('logout');
    }
	 
      public function index()  {
        $admin = User::find(Auth::user()->id);
        return view('admin.profile.profile' , compact('admin'));
    }

    public function adminpassword() {
        $admin = User::find(Auth::user()->id);
        return view('admin.profile.password' , compact('admin'));
    }
 
    public function update_admin(Request $request){
		 
		$id = $request->id;
        $user = User::findOrFail($id);

        $rules = [ 'username' => 'unique:users,username,'.$request->id,
        'email' => 'unique:users,email,'.$request->id];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
            return redirect()->back()->with('error', 'This Email Already Exist');   
            }
        $input = $request->all();  
        if ($file = $request->file('photo')){ 
            if (file_exists(public_path('uploads/admin/'.$user->photo)))  {
                @unlink(public_path('uploads/admin/'.$user->photo));
            }   
            $originalName = $request->file('photo')->getClientOriginalName();
            $extension = $request->file('photo')->getClientOriginalExtension(); 
            $uniqueName = time() . '_' . uniqid() . '.' . $extension; 
            $uniqueName = str_replace(' ', '', $uniqueName); 
            $file->move(public_path('uploads/admin'), $uniqueName); 
            $input['photo'] = $uniqueName;
        }  
        $user->update($input);
        return redirect()->back()->with('message', 'Profile Updated Successfully');   
    }

    public function update_admin_password(Request $request){
        $user = User::findOrFail($request->id);
        $input['password'] = "";
        if ($request->cpass){
            if (Hash::check($request->cpass, $user->password)){

                if ($request->newpass == $request->renewpass){
                    $input['password'] = Hash::make($request->newpass);
                }else{
                     return redirect()->back()->with('error', 'Confirm Password Does not match');  
                }
            }else{
                return redirect()->back()->with('error', 'Current Password Does not match');  ;
            }
        }
        $user->update($input);
       return redirect()->back()->with('message', 'Password Updated Successfully');  
    }
	
	
	
	
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB;
use Hash;
class LoginController extends Controller
{
  
      public function __construct()  {
        $this->middleware('guest')->except('logout');
      }
     
	//show admin page
	public function index(){ 
		return view('admin/login');
	}
	
	//function for submit admin login form
    public function admin_login(Request $request){
	   request()->validate([
            'email' => 'required', 'email',
            'password' => 'required', 'string', 'max:255'
        ]); 
	   	$email = $request->email;
    	$password = $request->password;
	   
      
              // Check validation
			if (Auth::attempt(['email' => $email, 'password' => $password,'user_type'=>'admin'])) {
				$user = Auth::user();
				if ($user->user_type == "admin") {
					return redirect()->route('admin_dashboard');
				}
			} else {
				return back()->with('error','Crediantials not matched with records.');
				return redirect('/admin');
			} 
        
		
		 
    	
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function logout(Request $request){
        if (!Auth::user()) {
            return redirect('/admin');
        }
        $user = Auth::user();

		if ($user->user_type == "admin") {
		    Auth::logout();
        	return redirect('/admin');
		}else{
            Auth::logout();
            return redirect('/');
        }
    }
}

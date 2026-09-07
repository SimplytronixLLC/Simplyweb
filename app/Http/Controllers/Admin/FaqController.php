<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB; 
use App\Models\Faq;
class FaqController extends Controller {
     
    public function __construct() {
            $this->middleware('admin')->except('logout');
    }
	 
	public function index(){
		$active  =  faq ::orderBy('id','asc')->where('status','active')->get();
		$closed =   Faq ::orderBy('id','asc')->where('status','deactive')->get();
        return view('admin.faq.list',compact('active','closed'));
	}
 
	public function create(){
		return view('admin.faq.add');
	}

    
     public function store(Request $request) {
        $data = new Faq;
        $data->fill($request->all());
        $data->save(); 
         return redirect('admin/faq')->with('message',' Faq  Created Successfully.');
    } 
    
	public function view($id){
         $data  =  Faq ::where('id',$id)->first();
        return view('admin.faq.view',compact('data'));
	}
	
   public function edit($id){
		 $data  =  Faq ::where('id',$id)->first();
        return view('admin.faq.edit',compact('data'));
	}
	 
   public function update($id,Request $request) {
        $Faq = Faq::findOrFail($id);
        $data = $request->all();
        $Faq->update($data);
        return redirect()->back()->with('message', 'Record Updated Successfully');  
    }
	 
     //Cloase Faq 
    public function close($id) {
        $data = Faq::findOrFail($id);
        $record['status'] = "deactive";
        $data->update($record);
        return redirect('admin/faq')->with('message','Faq Closed Successfully.');
    }
 
    // Open Faq 
    public function open($id) { 
        $data = Faq::findOrFail($id);
        $record['status'] = "active";
        $data->update($record);
        return redirect('admin/faq')->with('message','Faq Opened Successfully.');
    }
	 
    //Delete Faq 
    public function destroy($id) {
         $data = Faq::findOrFail($id);	
         $data->delete();
        return redirect('admin/faq')->with('message','Faq  Deleted Successfully.');
    } 
 
}

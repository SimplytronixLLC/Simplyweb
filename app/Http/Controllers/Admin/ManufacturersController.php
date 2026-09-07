<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\Manufacturers; 
class ManufacturersController extends Controller {
		 
		public function __construct() {
				$this->middleware('admin')->except('logout');
		}

		
		public function manufacturers(){   
			  return view('admin.manufacturers.list');
		} 
 
		public function manufacturers_list(Request $request){  
			 $status =  $request['status'];    
			 $totalData = Manufacturers::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})->count();
			 $totalFiltered = $totalData;
			 $totalFiltered = $totalData;
			 $limit = $request->input('length');
			 $start = $request->input('start');
			 $order = 'id';
			 $dir = 'ASC';  
			 $search = $request->input('search.value'); 
			 if(empty($search)) {
				 $posts =  Manufacturers::where(function ($query) use ($status) { 
								if($status) {  $query->where('status',$status); }
							})->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
		
				 $totalFiltered = Manufacturers::where(function ($query) use ($status) { 
					if($status) {  $query->where('status',$status); }
				 })->count();
			 } else {
				 $posts =  Manufacturers::where(function ($query) use ($status) { 
												if($status) {  $query->where('status',$status); }
											})
										->where(function($query) use ($search){
											$query->where('created_at', 'LIKE', '%'.$search.'%');
											$query->orWhere('name', 'LIKE', '%'.$search.'%'); 
										 }) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
		
				 $totalFiltered = Manufacturers::where(function ($query) use ($status) { 
													if($status) {  $query->where('status',$status); }
												})
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
		
					 $edit  = '<a href="'.url('').'/admin/manufacturers/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
					 $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/manufacturers/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
					 
					 if($post->image){
						$image =    '<img src="'.url('public/uploads/manufacturers').'/'.$post->image.'"  height="100px" width="150px">'; 
					 } else{ 
						$image =    '<img  src="'.url('public/uploads/manufacturers/no-image.png').'  height="100px" width="150px"  >'; 
					 }

					 $nestedData['id'] =  $i;    
					 $nestedData['name'] =  $post->name ?? '--';  
					 $nestedData['status'] =  $status;   
					 $nestedData['action'] =  $delete;
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
 
	

	public function manufacturers_add(){ 
		     return view('admin.manufacturers.add');
	} 
	 
	public function manufacturers_save(Request $request) { 




			$data = new manufacturers;
			$data->fill($request->all());  
			$data->save();
			return redirect('admin/manufacturers')->with('message','Record Created Successfully.'); 
	} 
		 

	public function manufacturers_edit($id){ 
		 $data['data'] =  Manufacturers::findOrFail($id); 
		  return view('admin.manufacturers.edit',$data);
 	} 

 
	public function manufacturers_update(Request $request) {   
		$manufacturers = Manufacturers::findOrFail($request->id); 
		$data = $request->all();    
		$manufacturers->update($data);
		return redirect('admin/manufacturers')->with('message','Record Updated Successfully.'); 
	}
   
	public function manufacturers_delete($id){
		$data = Manufacturers::findOrFail($id);  
		$data->delete();
		return redirect(url('admin/manufacturers'))->with('message','Record Deleted Successfully.'); 
	}


}

 
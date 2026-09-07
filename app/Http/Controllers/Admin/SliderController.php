<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\Slider; 
class SliderController extends Controller {
		 
		public function __construct() {
				$this->middleware('admin')->except('logout');
		}

		
		public function slider(){   
			  return view('admin.slider.list');
		} 
 
		public function slider_list(Request $request){  
			 $status =  $request['status'];    
			 $totalData = Slider::where(function ($query) use ($status) { if($status) {  $query->where('status',$status); }})->count();
			 $totalFiltered = $totalData;
			 $totalFiltered = $totalData;
			 $limit = $request->input('length');
			 $start = $request->input('start');
			 $order = 'id';
			 $dir = 'ASC';  
			 $search = $request->input('search.value'); 
			 if(empty($search)) {
				 $posts =  Slider::where(function ($query) use ($status) { 
								if($status) {  $query->where('status',$status); }
							})->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
		
				 $totalFiltered = Slider::where(function ($query) use ($status) { 
					if($status) {  $query->where('status',$status); }
				 })->count();
			 } else {
				 $posts =  Slider::where(function ($query) use ($status) { 
												if($status) {  $query->where('status',$status); }
											})
										->where(function($query) use ($search){
											$query->where('created_at', 'LIKE', '%'.$search.'%');
											$query->orWhere('title', 'LIKE', '%'.$search.'%'); 
										 }) ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
		
				 $totalFiltered = Slider::where(function ($query) use ($status) { 
													if($status) {  $query->where('status',$status); }
												})
										 ->where(function($query) use ($search){
											$query->where('created_at', 'LIKE', '%'.$search.'%');
											$query->orWhere('title', 'LIKE', '%'.$search.'%'); 
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
		
					 $edit  = '<a href="'.url('').'/admin/slider/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit " ><i class="fa fa-edit"></i>  </a>'; 
					 $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/slider/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete " ><i class="fa fa-trash"  ></i></a>';  
					 
					 if($post->image){
						$image =    '<img src="'.url('public/uploads/slider').'/'.$post->image.'"  height="100px" width="150px">'; 
					 } else{ 
						$image =    '<img  src="'.url('public/uploads/slider/no-image.png').'  height="100px" width="150px"  >'; 
					 }

					 $nestedData['id'] =  $i;    
					 $nestedData['image'] =  $image;     
					 $nestedData['name'] =  $post->name ?? '--'; 
					 $nestedData['page_link'] =  $post->page_link ?? '--';  
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
 
	

	public function slider_add(){
		
		     return view('admin.slider.add');
	} 
	 
	public function slider_save(Request $request) { 
			$data = new Slider;
			$data->fill($request->all()); 
			if ($file = $request->file('image')){
				$photo_name = rand().$request->file('image')->getClientOriginalName();
				$file->move(public_path('uploads/slider'), $photo_name);
				$data['image'] = $photo_name;
			} 
			$data->save();
			return redirect('admin/slider')->with('message','Record Created Successfully.'); 
	} 
		 

	public function slider_edit($id){ 
		 $data['data'] =  Slider::findOrFail($id); 
		  return view('admin.slider.edit',$data);
 	} 

 
	public function slider_update(Request $request) {   
		$slider = Slider::findOrFail($request->id); 
		$data = $request->all(); 
		if ($file = $request->file('image')){ 
			if (file_exists(public_path('uploads/slider/'.$slider->image)))  {
				@unlink(public_path('uploads/slider/'.$slider->image));
			}    
			$extension = $request->file('image')->getClientOriginalExtension(); 
			$uniqueName = time() . '_' . uniqid() . '.' . $extension; 
			$uniqueName = str_replace(' ', '', $uniqueName); 
			$file->move(public_path('uploads/slider'), $uniqueName); 
			$data['image'] = $uniqueName;
		}     
		$slider->update($data);
		return redirect('admin/slider')->with('message','Record Updated Successfully.'); 
	}
   
	public function slider_delete($id){
		$data = Slider::findOrFail($id); 
		if (file_exists(public_path('/uploads/slider/'.$data->image)))  { 
			@unlink('public/uploads/slider/'.$data->image);     
	     } 
		$data->delete();
		return redirect(url('admin/slider'))->with('message','Record Deleted Successfully.'); 
	}


}

 
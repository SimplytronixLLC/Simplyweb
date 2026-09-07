<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\Category;  
use App\Models\User;
use App\Helpers\Common;
 
class SubjectController  extends Controller {
     
    public function __construct() {
            $this->middleware('admin')->except('logout');
    }
  
	public function index(){ 
		 return view('admin.category.list');
	}
	
	
 //All jobs with ajax   Show all Jobs  with ajxa data table
    public function category_list(Request $request){ 
         
           $columns = array( 
                0  => 'id',
                1  => 'title'       
         );  
        $status =  $request['status'];  
        $totalData = Category::where('status',$status)->count();
        $totalFiltered = $totalData;
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = 'id';
        $dir = 'desc'; 
       //$order = $columns[$request->input('order.0.column')];
        //$dir = $request->input('order.0.dir');  

        $search = $request->input('search.value'); 
        if(empty($search)) {
			
            $posts =  Category::where('status',$status)
							->offset($start)
							->limit($limit)
							->orderBy($order,$dir)
							->get(); 
							
            $totalFiltered = Category::where('status',$status)->count();
        } else {
 
            $posts =  Category::where('status',$status) 
                                   ->where(function($query) use ($search){
                                        $query->where('created_at', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('description', 'LIKE', '%'.$search.'%');
                                    }) 
                        ->offset($start)
                        ->limit($limit)   
                        ->orderBy($order,$dir)
                        ->get(); 
            $totalFiltered = Category::where('status',$status) 
                                    ->where(function($query) use ($search){
                                        $query->where('created_at', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('description', 'LIKE', '%'.$search.'%');
                                     })->count();
        }
        $data = array();
        if(!empty($posts))  {
            $i=1; 
            foreach ($posts as $post) {  
                                     
                if($post->status != "deactive"){
                    $status  = '<a href="'.url('').'/admin/category/'.$post->id.'/close"   class="btn btn-sm btn-warning shadow btn-xs sharp mr-1"  data-toggle="tooltip" data-placement="top" title="Close Category"  ><i class="fa fa-toggle-off"></i>  </a>';
                } else {
                    $status  = '<a href="'.url('').'/admin/category/'.$post->id.'/open"  class="btn btn-sm btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Open Category" ><i class="fa fa-toggle-on"></i>  </a>';  
                }
                $edit  = '<a href="'.url('').'/admin/category/'.$post->id.'/edit" class="btn btn-warning shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit post" ><i class="fa fa-edit"></i>  </a>'; 
                $view   = '<a href="'.url('').'/admin/category/'.$post->id.'" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit post" ><i class="fa fa-eye"></i>  </a>'; 
                $delete = '<a href="javascript:;" data-href=" '.url('').'/admin/category/'.$post->id.'/delete"  class="btn btn-danger shadow btn-xs sharp"  data-toggle="modal" data-target="#confirm-delete"   data-placement="top" title="Delete post " ><i class="fa fa-trash"  ></i></a>';  
               
				$nestedData['id'] =  $i;   
                $nestedData['title'] =  $post->title;   
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
 
     

	public function create(){
	    return view('admin.category.add');
	}
 
    
     public function store(Request $request) {
	  
        $data = new Category;
        $data->fill($request->all());
        if ($file = $request->file('image')){
            $photo_name = rand().$request->file('image')->getClientOriginalName();
            $file->move(public_path('uploads/category'), $photo_name);
            $data['image'] = $photo_name;
        } 
        $data['status'] = "active"; 
        $data->save(); 
        return redirect('admin/category')->with('message',' Category Created Successfully.');
    } 
     
	public function view($id){
        $data['data']  =  Category::where('id',$id)->first(); 
	    return view('admin.category.view',$data);
	}
	
	
   public function edit($id){ 
        $data['data']  =  Category::where('id',$id)->first();  
        return view('admin.category.edit',$data); 
	}
	
	 
   public function update($id,Request $request) {   
        $Category = Category::findOrFail($id);
        $data = $request->all();   
        if ($file = $request->file('image')){
  		    if (file_exists(public_path('uploads/category/'.$Category->image)))  {
  				  @unlink(public_path('uploads/category/'.$Category->image));
  			  }   
			    $photo_name = rand().$request->file('image')->getClientOriginalName();
			    $destinationPath = public_path('/uploads/category');
			    $file->move($destinationPath, $photo_name);
			    $data['image'] = $photo_name;
        } 
        $Category->update($data);
        return redirect()->back()->with('message', 'Record Updated Successfully');  
    }
	
	 
	
    //Delete Category 
    public function destroy($id) {
      $data = Category::findOrFail($id);
		 if($data->image){
			   if (file_exists(public_path('uploads/category/'.$data->image)))  {
					   @unlink(public_path('uploads/category/'.$data->image));
			   }
		 }	
        $data->delete();
        return redirect('admin/category')->with('message','Category  Deleted Successfully.');
    } 
	
	
	
}

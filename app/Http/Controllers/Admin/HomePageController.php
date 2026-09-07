<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Hash;
use File;
use App\Helpers\Common;
use App\User;
class HomePageController extends Controller {
  
    public function __construct() {
             $this->middleware('admin')->except('logout');
	}

	public function home_page_settings(){
         $data['data']  = DB::table('page_settings')->where('id','=',1)->first(); 
         return view('admin.others.home_page',$data);
	}
	
	
    public function update_home_settings(Request $request) { 
		  $settings = DB::table('page_settings')->where('id','=',1)->get()->first();
		
		 if ($file = $request->file('home_background')){
		    if (file_exists(public_path('uploads/'.$settings->home_background)))  {
				@unlink(public_path('uploads/'.$settings->home_background));
			 }   
			$photo_name = rand().$request->file('home_background')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['home_background'] = $photo_name;
        } 
        if ($file = $request->file('home_slider')){
		    if (file_exists(public_path('uploads/'.$settings->home_slider)))  {
				@unlink(public_path('uploads/'.$settings->home_slider));
			 }   
			$photo_name = rand().$request->file('home_slider')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['home_slider'] = $photo_name;
        } 

		 
		  $data['home_title'] = $request->home_title;
		  $data['home_heading'] = $request->home_heading;
		  $data['home_description'] = $request->home_description;
		  $affected = DB::table('page_settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
		 
	public function home_section(){
         $data['data']  = DB::table('page_settings')->where('id','=',1)->first(); 
         return view('admin.others.home_section',$data);
	}
	
	
    public function update_home_section(Request $request) { 
		  $settings = DB::table('page_settings')->where('id','=',1)->get()->first();
		 //1 Image 
		 if ($file = $request->file('service_image1')){
		    if (file_exists(public_path('uploads/'.$settings->service_image1)))  {
				@unlink(public_path('uploads/'.$settings->home_background));
			 }   
			$photo_name = rand().$request->file('service_image1')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['service_image1'] = $photo_name;
        } 
		 // 2 Image 
        if ($file = $request->file('service_image2')){
		    if (file_exists(public_path('uploads/'.$settings->service_image2)))  {
				@unlink(public_path('uploads/'.$settings->service_image2));
			 }   
			$photo_name = rand().$request->file('service_image2')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['service_image2'] = $photo_name;
        } 

        // 3 Image 
        if ($file = $request->file('service_image3')){
		    if (file_exists(public_path('uploads/'.$settings->service_image3)))  {
				@unlink(public_path('uploads/'.$settings->service_image3));
			 }   
			$photo_name = rand().$request->file('service_image3')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['service_image3'] = $photo_name;
        } 
		 
		  $data['service_title'] = $request->service_title;
		  $data['service_description'] = $request->service_description;
		  $data['service_title1'] = $request->service_title1;
		  $data['service_description1'] = $request->service_description1;
		  $data['service_title2'] = $request->service_title2;
		  $data['service_description2'] = $request->service_description2;
		  $data['service_title3'] = $request->service_title3;
		  $data['service_description3'] = $request->service_description3; 
		  $affected = DB::table('page_settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
	
    public function terms(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.terms',$data);
	}
	
	public function update_terms(Request $request) {
		$settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['terms_heading'] = $request->terms_heading;
		  $data['terms_description'] = $request->terms_description;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
	public function privacy(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.privacy',$data);
	}
 	public function update_privacy(Request $request) {
		$settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['privacy_heading'] = $request->privacy_heading;
		  $data['privacy_description'] = $request->privacy_description;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	

	public function logo(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.logo',$data);
	}
 
	
	public function update_logo(Request $request) {
		 $settings = DB::table('settings')->where('id','=',1)->get()->first();
		 if ($file = $request->file('logo')){
		    if (file_exists(public_path('uploads/'.$settings->logo)))  {
				@unlink(public_path('uploads/'.$settings->logo));
			 }   
			$photo_name = rand().$request->file('logo')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['logo'] = $photo_name;
			$affected = DB::table('settings')->where('id', 1)->update($data);
        }  
		 if ($file = $request->file('icon')){
		    if (file_exists(public_path('uploads/'.$settings->icon)))  {
				@unlink(public_path('uploads/'.$settings->icon));
			 }   
			$photo_name = rand().$request->file('icon')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['icon'] = $photo_name;
			$affected = DB::table('settings')->where('id', 1)->update($data);
        }
		
		if ($file = $request->file('footer_logo')){
		    if (file_exists(public_path('uploads/'.$settings->footer_logo)))  {
				@unlink(public_path('uploads/'.$settings->footer_logo));
			 }   
			$photo_name = rand().$request->file('footer_logo')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['footer_logo'] = $photo_name;
			$affected = DB::table('settings')->where('id', 1)->update($data);
        }  
          return redirect()->back()->with('message', 'Record Updated Successfully'); 
 
	}
	
	public function address(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.address',$data);
	}
   public function update_address(Request $request) {
		  $settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['title'] = $request->title;
		  $data['url'] = $request->url;
		  $data['address'] = $request->address;
		  $data['phone'] = $request->phone;
		  $data['whatsapp'] = $request->whatsapp;
		  $data['email'] = $request->email;
		  $data['footer_about'] = $request->footer_about;
		  $data['footer_text'] = $request->footer_text;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
	
	public function google_analytic(){
         $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.google_analytic',$data);
	}
	
	public function update_google_analytic(Request $request) {
		  $settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['google_analytics'] = $request->google_analytics;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
	public function meta_keywords(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.meta_keywords',$data);
	}
	
	public function update_meta_keywords(Request $request) {
		  $settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['meta_title'] = $request->meta_title;
		  $data['meta_keyword'] = $request->meta_keyword;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
	public function social_setting(){
          $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.social_setting',$data);
	}
	
	public function update_social_setting(Request $request) {
		  $settings = DB::table('settings')->where('id','=',1)->get()->first();
		  $data['facebook'] = $request->facebook;
		  $data['facebook_status'] = $request->facebook_status;
		  $data['youtube'] = $request->youtube;
		  $data['youtube_status'] = $request->youtube_status;
		  $data['instagram'] = $request->instagram;
		  $data['instagram_status'] = $request->instagram_status;
		  $data['lingdin'] = $request->lingdin;
		  $data['lingdin_status'] = $request->lingdin_status;
		  $data['twiter'] = $request->twiter;
		  $data['twiter_status'] = $request->twiter_status;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	
 
    /////////////////////damage-of-intragate////////////////////////
    public function damage_of_intragate(){
         $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.damage_intragate',$data);
	}
	 
    public function update_intragate(Request $request) {
		$settings = DB::table('settings')->where('id','=',1)->get()->first();
		 if ($file = $request->file('photo')){
		    if (file_exists(public_path('uploads/'.$settings->intragate_image)))  {
				@unlink(public_path('uploads/'.$settings->intragate_image));
			 }   
			$photo_name = rand().$request->file('photo')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['intragate_image'] = $photo_name;
        }  
		  $data['intragate_title'] = $request->intragate_title;
		  $data['intragate_description'] = $request->intragate_description;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	/////////////////////damage-of-intragate////////////////////////



    /////////////////////damage-of-intragate////////////////////////
    public function burnaint(){
         $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.burnaint',$data);
	}
	 
    public function update_burnaint(Request $request) {
		$settings = DB::table('settings')->where('id','=',1)->get()->first();
		 if ($file = $request->file('photo')){
		    if (file_exists(public_path('uploads/'.$settings->burnaint_image)))  {
				@unlink(public_path('uploads/'.$settings->burnaint_image));
			 }   
			$photo_name = rand().$request->file('photo')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['burnaint_image'] = $photo_name;
        }  
		  $data['burnaint_title'] = $request->burnaint_title;
		  $data['burnaint_description'] = $request->burnaint_description;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}
	/////////////////////damage-of-intragate////////////////////////

       /////////////////////Disclaimer////////////////////////
    public function disclaimer(){
         $data['data']  = DB::table('settings')->where('id','=',1)->get()->first();
         return view('admin.others.disclaimer',$data);
	}
	 
    public function update_disclaimer(Request $request) {
		$settings = DB::table('settings')->where('id','=',1)->get()->first();
		 if ($file = $request->file('photo')){
		    if (file_exists(public_path('uploads/'.$settings->disclaimer_image)))  {
				@unlink(public_path('uploads/'.$settings->disclaimer_image));
			 }   
			$photo_name = rand().$request->file('photo')->getClientOriginalName();
			$destinationPath = public_path('/uploads');
			$file->move($destinationPath, $photo_name);
			$data['disclaimer_image'] = $photo_name;
        }  
		  $data['disclaimer_title'] = $request->disclaimer_title;
		  $data['disclaimer_description'] = $request->disclaimer_description;
		  $affected = DB::table('settings')->where('id', 1)->update($data);
          return redirect()->back()->with('message', 'Record Updated Successfully');  
	}

	//////////////////// Disclaimer////////////////////////

}

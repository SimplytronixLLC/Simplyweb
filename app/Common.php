<?php 
namespace App; 
use DB;
use App\Models\User;
use App\Models\Manufacturers;
use App\Models\Category; 
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Session; 
use Mail; 
use Carbon\Carbon;
class Common{
 




    static function get_sub_category($id) {
            $sub_category = SubCategory::where('ParentId', $id)->get(); 
            $html  = '';
             if(!empty($sub_category)){
                foreach($sub_category as $key=>$val){ 
 
                        $child_category = ChildCategory::where('ParentId', $val->CategoryId)->get();  
                        if(count($child_category) != 0){
                            $url =  url('/sub-category').'/'. preg_replace('/[^\w\/]/', '-', strtolower($val->Name)).'/'.$val->id; 
                            $html .='<li > <a href="'.$url.'" class="anchor-text fs-5">'.$val->Name.'  <small>('.$val->ProductCount.') </small></a> ';
                            $html .= '<ul class="subcategories">';
                            foreach($child_category as $key1=>$val1){
                                $curl =  url('/shop').'?key='. preg_replace('/[^\w\/]/', ' ', strtolower($val1->Name)).'&id='.$val1->id;
                                $html .= ' <li><a href="'.$curl.'" class="mb-2">'.$val1->Name.' <small>('.$val1->ProductCount.')</small></a></li> ';   
                            }
                            $html .= '</ul> ';  
                        }  else{ 
                            $url =  url('/shop').'?key='. preg_replace('/[^\w\/]/', ' ', strtolower($val->Name)).'&id='.$val->id; 
                            $html .='<li > <a href="'.$url.'" class="anchor-text fs-5">'.$val->Name.'  <small>('.$val->ProductCount.') </small></a> ';
                        }

                    $html .= '</li> ';  
           }} 
           return $html; 
    }

    static function get_child_category($id) {
            $sub_category = ChildCategory::where('ParentId', $id)->get(); 
            $html  = '';
            if(!empty($sub_category)){
                foreach($sub_category as $key=>$val){ 
                    $url =  url('/shop').'?key='. preg_replace('/[^\w\/]/', ' ', strtolower($val->Name)).'&id='.$val->id; 
                    $html .='<li > <a href="'.$url.'" class="anchor-text fs-5">'.$val->Name.'  <small>('.$val->ProductCount.') </small></a> ';
                    $html .= '</li> ';  
        }} 
        return $html; 
    }

   static function get_sub_menu($id) {
             $sub_category = SubCategory::where('ParentId', $id)->get(); 
            $html  = '';
             if(!empty($sub_category)){
                foreach($sub_category as $key=>$val){  
                            $url =  url('/shop').'?key='. preg_replace('/[^\w\/]/', ' ', strtolower($val->Name)).'&id='.$val->id; 
                            $html .=' <li class="categories__submenu--child__items">  <a href="'.$url.'" class="categories__submenu--child__items--link">'.$val->Name.'   </a> '; 

                    $html .= '</li> ';  
           }} 
           return $html; 
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

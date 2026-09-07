@extends('admin.includes.masterpage-admin') 

@section('seo')
    <title>Create FAQ  - {{@$settings->title}} </title>
@stop

@section('content')
  
@include('alerts')


<form method="POST" action="{!! url('admin/faq_save') !!}" data-parsley-validate="" class="form-horizontal" enctype="multipart/form-data">
     @csrf
	 <div class="row">
	 	<div class="col-xl-12">

	 		<div class="card">
	 			<div class="card-header">
                    <h4 class="card-title">Add FAQ</h4> 
                </div>
                <div class="card-body"> 
                    <div class="form-row"> 
                        <div class="form-group col-md-12">
                            <label >Select FAQs Type <strong class="red">*</strong>: </label> 
                            <select name="type" required="" class="form-control">
                                    <option value="">--Select Type--</option>
                                    <option value="Journal Publishing">Journal Publishing</option>
                                    <option value="Book Publishing">Book Publishing</option>    
                                    <option value="Order Support">Order Support</option>    
                                    <option value="Editor - Reviewer Support">Editor - Reviewer Support</option>    
                                    <option value="Advertisement Support"> Advertisement Support</option>    
                            </select>
                        </div> 
                    </div>  

                    <div class="form-row">  
                		<div class="form-group col-md-12">
                			<label>Question <strong class="red">*</strong>: </label> 
                            <input type="text" autocomplete="off" value="" class="form-control" name="question" required>
                        </div>  
                	</div>  
                     
                    <div class="form-row">  
                		<div class="form-group col-md-12">
                			<label>Answer <strong class="red">*</strong>: </label> 
                             <textarea class="form-control summernote" name="answer" required ></textarea>
                        </div>  
                	</div> 

                    <div class="form-row">  
                		<div class="form-group col-md-12">
                			<label>Link of the Support type :</label> 
                            <input type="text" autocomplete="off" value="" class="form-control" name="link"  placeholder=""  >
                        </div>  
                	</div> 

                    <div class="form-row">  
                    <div class="form-group col-md-12">
                            <label >Select  Status <strong class="red">*</strong>: </label> 
                            <select name="status" required="" class="form-control">
                                    <option value="">--Select Status--</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>     
                            </select>
                        </div> 
                    </div>





                </div>
	 		</div>  
            <div class="form-action-col text-right">
                  <a href="{!! url('admin/manage-faq ') !!}" class="btn  btn-sm btn-warning"> <span class="text"> Back</span></a>
                <button name="addProduct_btn" id="submit" type="submit" class="btn  btn-sm btn-success ">Save </button>
            </div>
 
	 	</div> 
	 </div> 
</form>
<div style="height: 100px;"></div>
@stop
@section('footer')
 
@stop
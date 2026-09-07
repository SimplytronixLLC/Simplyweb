<?php

namespace App\Http\Controllers\API;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SupplierAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated=$request->validate([

            'company_name'=>'required',
            'email'=>'required|email|unique:suppliers',
            'password'=>'required|min:6'
        ]);

        Supplier::create([
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message'=>'Registration submitted'
        ]);
    }

    public function login(Request $request)
    {
        $supplier=Supplier::where(
            'email',
            $request->email
        )->first();

        if(
            !$supplier ||
            !Hash::check(
                $request->password,
                $supplier->password
            )
        ){
            return response()->json([
                'message'=>'Invalid credentials'
            ],401);
        }

        if($supplier->status!='approved')
        {
            return response()->json([
                'message'=>'Account awaiting approval'
            ],403);
        }

        $token=$supplier
            ->createToken('supplier')
            ->plainTextToken;

        return response()->json([
            'token'=>$token,
            'supplier'=>$supplier
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
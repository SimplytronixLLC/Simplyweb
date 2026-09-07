<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BomSubmission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BomSubmitted;

class BomController extends Controller
{
    public function process(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'company' => 'required',
                'bom_file' => 'required|mimes:csv,xls,xlsx|max:10240',
            ]);

            $file = $request->file('bom_file');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('bom_uploads', $filename, 'public');

            $bomId = 'BOM-' . strtoupper(Str::random(6));

            BomSubmission::create([
                'bom_id' => $bomId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'filename' => $filename,
                'comments' => $request->comments,
            ]);

            $fullPath = storage_path('app/public/'.$path);

            Mail::to('info@simplytronix.com')
                ->send(new BomSubmitted([
                    'bom_id' => $bomId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'comments' => $request->comments,
                ], $fullPath));

            return response()->json([
                'status' => 'success',
                'bom_id' => $bomId,
                'message' => 'Your BOM has been submitted successfully.'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
}
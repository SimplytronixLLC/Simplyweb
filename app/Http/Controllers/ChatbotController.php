<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry; // Make sure this model exists

class ChatbotController extends Controller
{
    public function store(Request $request)
    {
        \App\Models\Inquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'phone' => $request->phone,
            'part_number' => $request->part,
            'source' => 'Chatbot',
        ]);
        return response()->json(['success' => true]);
    }
}

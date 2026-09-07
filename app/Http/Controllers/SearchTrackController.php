<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchTrackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'part_number' => 'required|string|max:255',
            'source' => 'nullable|string|max:50',
        ]);

        DB::table('visitor_searches')->insert([
            'visitor_id'  => $request->cookie('visitor_id'),
            'part_number' => strtoupper(trim($request->part_number)),
            'source'      => $request->source ?? 'search',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}

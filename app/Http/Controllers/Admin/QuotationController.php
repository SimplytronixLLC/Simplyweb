<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Quotation list page
     */
    public function index()
    {
        return view('admin.quotation.list');
    }

    /**
     * DataTable AJAX
     * Route: quotation_list
     */
    public function quotation_list(Request $request)
{
    $query = DB::table('quote');

    // 🔍 STATUS FILTER (tabs)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔍 GLOBAL SEARCH (DataTables)
    $search = $request->input('search.value');

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('order_id', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%")
              ->orWhere('part_number', 'like', "%{$search}%");
        });
    }

    // 🔢 Total records AFTER filtering
    $totalFiltered = $query->count();

    // 📄 Pagination
    $start  = intval($request->input('start', 0));
    $length = intval($request->input('length', 10));

    $rows = $query
        ->orderBy('created_at', 'desc')
        ->offset($start)
        ->limit($length)
        ->get();

    $data = [];

    foreach ($rows as $row) {
        $data[] = [
            'id'          => $row->id,
            'order_id'    => $row->order_id,
            'name'        => $row->name,
            'email'       => $row->email,
            'phone'       => $row->phone,
            'company'     => $row->company,
            'part_number' => $row->part_number,
            'quantity'    => $row->quantity,
            'status'      => $row->status,
            'created_at'  => date('Y-m-d H:i', strtotime($row->created_at)),
        ];
    }

    // 🔢 TOTAL RECORDS (without search, but with status filter)
    $recordsTotal = DB::table('quote')
        ->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->count();

    return response()->json([
        'draw'            => intval($request->input('draw')),
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $totalFiltered,
        'data'            => $data,
    ]);
}


    /**
     * Update quotation status
     * Route: quotation_update_status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer',
            'status' => 'required|string'
        ]);

        // Delete
        if ($request->status === 'delete') {
            DB::table('quote')->where('id', $request->id)->delete();
            return response()->json(['success' => true]);
        }

        // Allowed statuses
        $allowedStatuses = ['open', 'complete'];

        if (!in_array($request->status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 422);
        }

        DB::table('quote')
            ->where('id', $request->id)
            ->update([
                'status' => $request->status
            ]);

        return response()->json(['success' => true]);
    }
}

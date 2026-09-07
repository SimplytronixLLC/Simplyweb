<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BulkEmailController extends Controller
{
    public function index()
    {
        return view('admin.crm.bulk-email.index', [
            'totalContacts' => Contact::where('email_status', 'active')->count(),
            'masterListCount' => Contact::where('source', 'master_list')->where('email_status', 'active')->count(),
        ]);
    }

    public function send(Request $request)
    {
        return redirect()->back()->with('status', 'Emails queued for sending!');
    }
}

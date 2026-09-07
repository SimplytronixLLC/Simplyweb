<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;

class CrmWinbackController extends Controller
{
    public function index()
    {
        $contacts = CrmContact::where('source', 'winback')
            ->orderByDesc('winback_track')
            ->orderBy('name')
            ->get();

        $customerCount = $contacts->where('winback_track', 'customer')->count();
        $prospectCount = $contacts->where('winback_track', 'prospect')->count();

        return view('admin.crm.winback', compact('contacts', 'customerCount', 'prospectCount'));
    }

    /**
     * Approve the checked contacts for sending, and exclude anyone unchecked
     * by permanently turning off their winback eligibility (not deleting them
     * — they still exist in the CRM, just won't get a winback email).
     */
    public function approve(Request $request)
    {
        $approvedIds = $request->input('approved_ids', []);

        CrmContact::where('source', 'winback')->update(['winback_approved' => false]);

        if (!empty($approvedIds)) {
            CrmContact::where('source', 'winback')
                ->whereIn('id', $approvedIds)
                ->update(['winback_approved' => true]);
        }

        return redirect()->route('admin.crm.winback')
            ->with('status', count($approvedIds) . ' contact(s) approved for the winback send.');
    }

    /**
     * Inline name correction on the review screen — handles cases where
     * Dolibarr's contact record has an email address sitting in the name
     * field instead of a real person's name.
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crm_contacts,id',
            'name' => 'required|string|max:255',
        ]);

        CrmContact::where('id', $request->input('id'))
            ->where('source', 'winback')
            ->update(['name' => $request->input('name')]);

        return response()->json(['success' => true]);
    }

}

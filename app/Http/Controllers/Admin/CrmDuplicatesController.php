<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;

class CrmDuplicatesController extends Controller
{
    public function index()
    {
        $groups = CrmContact::whereNotNull('duplicate_of_id')
            ->where('duplicate_reviewed', false)
            ->with('duplicateOf')
            ->get()
            ->groupBy('duplicate_of_id');

        return view('admin.crm.duplicates', compact('groups'));
    }

    public function merge(Request $request, $id)
    {
        $dupe = CrmContact::findOrFail($id);

        if (!$dupe->duplicate_of_id) {
            return redirect()->back()->with('error', 'This contact is not flagged as a duplicate.');
        }

        $primary = CrmContact::findOrFail($dupe->duplicate_of_id);
        $dupe->mergeInto($primary);

        return redirect()->route('admin.crm.duplicates')
            ->with('status', "Merged contact #{$id} into #{$primary->id}.");
    }

    public function dismiss($id)
    {
        $dupe = CrmContact::findOrFail($id);
        $dupe->dismissDuplicateFlag();

        return redirect()->route('admin.crm.duplicates')
            ->with('status', "Contact #{$id} marked as not a duplicate — automation re-enabled.");
    }

    /**
     * Merge every flagged duplicate in one group into their shared primary
     * record in a single click, instead of merging one at a time.
     */
    public function mergeGroup($primaryId)
    {
        $primary = CrmContact::findOrFail($primaryId);

        $dupes = CrmContact::where('duplicate_of_id', $primaryId)
            ->where('duplicate_reviewed', false)
            ->get();

        $count = 0;
        foreach ($dupes as $dupe) {
            $dupe->mergeInto($primary);
            $count++;
        }

        return redirect()->route('admin.crm.duplicates')
            ->with('status', "Merged {$count} duplicate(s) into contact #{$primary->id}.");
    }

    /**
     * Merge every flagged duplicate across every group in one click —
     * for clearing a large backlog in one pass.
     */
    public function mergeAll()
    {
        $dupes = CrmContact::whereNotNull('duplicate_of_id')
            ->where('duplicate_reviewed', false)
            ->get();

        $count = 0;
        foreach ($dupes as $dupe) {
            $primary = CrmContact::find($dupe->duplicate_of_id);
            if ($primary) {
                $dupe->mergeInto($primary);
                $count++;
            }
        }

        return redirect()->route('admin.crm.duplicates')
            ->with('status', "Merged {$count} duplicate(s) across all groups.");
    }
}

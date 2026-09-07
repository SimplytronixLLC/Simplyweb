<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmDashboardController extends Controller
{
    public function index()
    {
        $byStage = CrmContact::whereNull('duplicate_of_id')
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')->pluck('total', 'stage');

        $bySource = CrmContact::whereNull('duplicate_of_id')
            ->select('lead_source', DB::raw('count(*) as total'))
            ->groupBy('lead_source')->pluck('total', 'lead_source');

        $leadsByMonth = CrmContact::whereNull('duplicate_of_id')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('count(*) as total'))
            ->groupBy('ym')->orderBy('ym')->pluck('total', 'ym');

        $cadenceFunnel = [
            'enrolled' => CrmContact::whereNotNull('cadence_type')->count(),
            'step_1' => CrmContact::where('followup_step', 1)->count(),
            'step_2' => CrmContact::where('followup_step', 2)->count(),
            'step_3' => CrmContact::where('followup_step', '>=', 3)->count(),
            'completed' => CrmContact::whereNotNull('cadence_type')
                ->whereNull('followup_step')->where('automation_enabled', false)->count(),
            'bounced' => CrmContact::where('email_status', 'bounced')->count(),
        ];

        $manualActions = CrmContact::whereNull('duplicate_of_id')
            ->where('manual_action', '!=', 'none')
            ->select('manual_action', DB::raw('count(*) as total'))
            ->groupBy('manual_action')->pluck('total', 'manual_action');

        $totalActive = CrmContact::whereNull('duplicate_of_id')
            ->where('email_status', '!=', 'bounced')->count();
        $bounceRate = $totalActive > 0
            ? round(($cadenceFunnel['bounced'] / max($totalActive + $cadenceFunnel['bounced'], 1)) * 100, 1)
            : 0;

        return view('admin.crm.dashboard', compact(
            'byStage', 'bySource', 'leadsByMonth', 'cadenceFunnel', 'manualActions', 'bounceRate'
        ));
    }

    /**
     * Streams a CSV of contacts matching the given filters — no external
     * package needed. Add ?stage=&source=&manual_action= as needed.
     */
    public function export(Request $request)
    {
        $query = CrmContact::whereNull('duplicate_of_id');

        if ($request->filled('stage')) {
            $query->where('stage', $request->input('stage'));
        }
        if ($request->filled('source')) {
            $query->where('lead_source', $request->input('source'));
        }
        if ($request->filled('manual_action')) {
            $query->where('manual_action', $request->input('manual_action'));
        }

        $filename = 'crm_export_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID', 'Name', 'Company', 'Email', 'Phone', 'Stage', 'Source',
                'Manual Action', 'Email Status', 'Created At',
            ]);

            $query->orderBy('id')->chunk(500, function ($contacts) use ($out) {
                foreach ($contacts as $c) {
                    fputcsv($out, [
                        $c->id, $c->name, $c->company, $c->email, $c->phone,
                        $c->stage, $c->lead_source, $c->manual_action,
                        $c->email_status, $c->created_at,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

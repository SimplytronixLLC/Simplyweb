<?php

namespace App\Console\Commands;

use App\Models\CrmContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrmImportWinback extends Command
{
    protected $signature = 'crm:import-winback';
    protected $description = 'One-time backfill of all-time Dolibarr order/quote history into a winback review queue';

    public function handle(): int
    {
        $customerCount = $this->importCompletedOrders();
        $prospectCount = $this->importRefusedQuotes();

        $this->info("Imported {$customerCount} customer-track and {$prospectCount} prospect-track winback contact(s).");
        $this->info('Nothing has been sent — review at /admin/crm/winback before approving.');

        return self::SUCCESS;
    }

    /**
     * Find the best individual contact person for a company: prefers a
     * socpeople row with a real email on file, falling back to the
     * company's own email/phone from llxe4_societe if no person exists.
     */
    protected function bestContactFor(int $socId, string $companyName, ?string $companyEmail, ?string $companyPhone): array
    {
        $person = DB::connection('dolibarr')
            ->table('llxe4_socpeople')
            ->where('fk_soc', $socId)
            ->where('priv', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderByDesc('rowid') // most recently added contact first
            ->first();

        if ($person) {
            $name = trim(($person->firstname ?? '') . ' ' . ($person->lastname ?? ''));
            return [
                'name' => $name !== '' ? $name : $companyName,
                'email' => $person->email,
                'phone' => $person->phone ?: $person->phone_mobile ?: $companyPhone,
            ];
        }

        // No individual contact on file — fall back to company record.
        return [
            'name' => $companyName,
            'email' => $companyEmail,
            'phone' => $companyPhone,
        ];
    }

    protected function importCompletedOrders(): int
    {
        $existingSocIds = CrmContact::whereNotNull('dolibarr_thirdparty_id')->pluck('dolibarr_thirdparty_id')->all();

        $rows = DB::connection('dolibarr')
            ->table('llxe4_commande as c')
            ->join('llxe4_societe as s', 's.rowid', '=', 'c.fk_soc')
            ->where('c.fk_statut', 3)
            ->when(!empty($existingSocIds), fn ($q) => $q->whereNotIn('s.rowid', $existingSocIds))
            ->select('s.rowid as soc_id', 's.nom', 's.email', 's.phone', 's.phone_mobile')
            ->distinct()
            ->get();

        foreach ($rows as $row) {
            $contact = $this->bestContactFor($row->soc_id, $row->nom, $row->email, $row->phone ?: $row->phone_mobile);

            $duplicateOfId = null;
            if (!empty($contact['email'])) {
                $match = CrmContact::whereRaw('LOWER(email) = ?', [strtolower(trim($contact['email']))])
                    ->orderBy('created_at')
                    ->first();
                if ($match) {
                    $duplicateOfId = $match->id;
                }
            }

            CrmContact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'company' => $row->nom,
                'dolibarr_thirdparty_id' => $row->soc_id,
                'stage' => 'new',
                'source' => 'winback',
                'winback_track' => 'customer',
                'winback_approved' => false,
                'automation_enabled' => false,
                'next_followup_at' => null,
                'duplicate_of_id' => $duplicateOfId,
            ]);
        }

        return $rows->count();
    }

    protected function importRefusedQuotes(): int
    {
        $existingSocIds = CrmContact::whereNotNull('dolibarr_thirdparty_id')->pluck('dolibarr_thirdparty_id')->all();

        $rows = DB::connection('dolibarr')
            ->table('llxe4_propal as p')
            ->join('llxe4_societe as s', 's.rowid', '=', 'p.fk_soc')
            ->where('p.fk_statut', 3)
            ->when(!empty($existingSocIds), fn ($q) => $q->whereNotIn('s.rowid', $existingSocIds))
            ->select('s.rowid as soc_id', 's.nom', 's.email', 's.phone', 's.phone_mobile')
            ->distinct()
            ->get();

        foreach ($rows as $row) {
            $contact = $this->bestContactFor($row->soc_id, $row->nom, $row->email, $row->phone ?: $row->phone_mobile);

            $duplicateOfId = null;
            if (!empty($contact['email'])) {
                $match = CrmContact::whereRaw('LOWER(email) = ?', [strtolower(trim($contact['email']))])
                    ->orderBy('created_at')
                    ->first();
                if ($match) {
                    $duplicateOfId = $match->id;
                }
            }

            CrmContact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'company' => $row->nom,
                'dolibarr_thirdparty_id' => $row->soc_id,
                'stage' => 'new',
                'source' => 'winback',
                'winback_track' => 'prospect',
                'winback_approved' => false,
                'automation_enabled' => false,
                'next_followup_at' => null,
                'duplicate_of_id' => $duplicateOfId,
            ]);
        }

        return $rows->count();
    }
}

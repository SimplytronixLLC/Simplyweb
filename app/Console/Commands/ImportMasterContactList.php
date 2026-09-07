<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use DB;

class ImportMasterContactList extends Command
{
    protected $signature = 'crm:import-contacts {file? : Path to Excel file} {--dry-run : Preview changes without importing} {--skip-duplicates : Skip emails that already exist}';
    protected $description = 'Import master contact list from Excel file into CRM';

    public function handle()
    {
        $filePath = $this->argument('file') ?? storage_path('imports/Simplytronix_Master_Contact_List.xlsx');
        $dryRun = $this->option('dry-run');
        $skipDuplicates = $this->option('skip-duplicates');

        // Validate file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            $this->info("Expected location: " . storage_path('imports/Simplytronix_Master_Contact_List.xlsx'));
            return 1;
        }

        $this->info("📂 Loading Excel file: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (\Exception $e) {
            $this->error("Failed to load Excel file: " . $e->getMessage());
            return 1;
        }

        // Get the "Master List" sheet
        $worksheet = $spreadsheet->getSheetByName('Master List');
        if (!$worksheet) {
            $this->error("Sheet 'Master List' not found in workbook");
            return 1;
        }

        $rows = $worksheet->toArray();
        $headers = array_shift($rows); // Remove header row
        $totalRows = count($rows);

        $this->info("📊 Found {$totalRows} contacts to process");

        // Statistics
        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'invalid' => 0,
            'duplicates' => 0,
        ];

        // Progress bar
        $bar = $this->output->createProgressBar($totalRows);
        $bar->start();

        // Existing emails (for duplicate detection)
        $existingEmails = $skipDuplicates ? Contact::pluck('email')->toArray() : [];

        // Start transaction
        if (!$dryRun) {
            DB::beginTransaction();
        }

        try {
            foreach ($rows as $index => $row) {
                $stats['total']++;
                $bar->advance();

                // Map columns
                $data = $this->mapRowToContact($row, $headers);

                // Validation
                if (!$data) {
                    $stats['invalid']++;
                    continue;
                }

                // Duplicate check
                if ($skipDuplicates && in_array($data['email'], $existingEmails)) {
                    $stats['duplicates']++;
                    continue;
                }

                // Check if contact exists by email
                $contact = Contact::where('email', $data['email'])->first();

                if ($contact) {
                    // Update existing contact
                    if (!$dryRun) {
                        $contact->update($data);
                    }
                    $stats['updated']++;
                } else {
                    // Create new contact
                    if (!$dryRun) {
                        Contact::create($data);
                    }
                    $stats['created']++;
                }
            }

            $bar->finish();
            $this->line('');

            // Commit or rollback
            if (!$dryRun) {
                DB::commit();
            }

            // Display summary
            $this->displaySummary($stats, $dryRun);

            return 0;
        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollBack();
            }
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Map Excel row to Contact model attributes
     */
    private function mapRowToContact($row, $headers)
    {
        // Column indices
        $email = trim($row[0] ?? null);
        $company = trim($row[1] ?? null);
        $name = trim($row[2] ?? null);
        $phone = trim($row[3] ?? null);
        $lastPartRef = trim($row[4] ?? null);
        $lastMfgRef = trim($row[5] ?? null);
        $lastDateSeen = $row[6] ?? null;
        $timesSeen = $row[7] ?? null;
        $sourceFile = trim($row[8] ?? null);
        $sourceSheet = trim($row[9] ?? null);

        // Validate email
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        // Clean up data
        $company = $this->cleanValue($company);
        $name = $this->cleanValue($name);
        $phone = $this->cleanPhoneNumber($phone);

        // Parse last date seen
        $lastSeen = null;
        if ($lastDateSeen && is_string($lastDateSeen)) {
            try {
                $lastSeen = Carbon::createFromFormat('Y-m-d H:i:s', $lastDateSeen);
            } catch (\Exception $e) {
                // Try other formats
                try {
                    $lastSeen = Carbon::parse($lastDateSeen);
                } catch (\Exception $e2) {
                    $lastSeen = null;
                }
            }
        }

        return [
            'email' => $email,
            'name' => $name ?: null,
            'company' => $company ?: null,
            'phone' => $phone,
            'source' => 'master_list', // Track source
            'stage' => 'lead',
            'last_contact_date' => $lastSeen,
            'metadata' => [
                'last_part_referenced' => $lastPartRef ?: null,
                'last_manufacturer' => $lastMfgRef ?: null,
                'times_seen' => $timesSeen,
                'source_file' => $sourceFile,
                'source_sheet' => $sourceSheet,
                'imported_at' => now(),
            ],
        ];
    }

    /**
     * Clean company/name values (remove dots, placeholders)
     */
    private function cleanValue($value)
    {
        if (!$value) return null;

        // Remove leading/trailing dots and spaces
        $value = trim($value, '. ');

        // Skip placeholder values
        if (in_array($value, ['.', '..', '...', '—', '-'])) {
            return null;
        }

        return strlen($value) > 1 ? $value : null;
    }

    /**
     * Clean phone number (basic formatting)
     */
    private function cleanPhoneNumber($phone)
    {
        if (!$phone) return null;

        $phone = trim($phone);

        // Skip invalid phone formats
        if (strlen($phone) < 7 || in_array($phone, ['.', '—', '-'])) {
            return null;
        }

        return $phone;
    }

    /**
     * Display import summary
     */
    private function displaySummary($stats, $dryRun)
    {
        $this->line('');
        $this->line('═══════════════════════════════════════════');
        $this->info('📈 IMPORT SUMMARY');
        $this->line('═══════════════════════════════════════════');

        $this->line("Total rows processed:    {$stats['total']}");
        $this->line("✅ Created new contacts: {$stats['created']}");
        $this->line("🔄 Updated existing:     {$stats['updated']}");
        $this->line("⏭️  Skipped duplicates:   {$stats['duplicates']}");
        $this->line("❌ Invalid records:      {$stats['invalid']}");

        $this->line('═══════════════════════════════════════════');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes committed to database');
        } else {
            $this->info('✨ Import completed successfully!');
        }

        $this->line('');

        // Show next steps
        if (!$dryRun && ($stats['created'] > 0 || $stats['updated'] > 0)) {
            $this->info('📋 Next steps:');
            $this->line('  1. Review imported contacts in bulk-email view');
            $this->line('  2. Set up follow-up cadence for new leads');
            $this->line('  3. Check dashboard for updated metrics');
        }
    }
}

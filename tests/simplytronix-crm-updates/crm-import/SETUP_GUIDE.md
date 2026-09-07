# Contact Import Setup Guide

Complete guide to set up the master contact list import functionality in your Simplytronix CRM.

## Quick Start (5 minutes)

### 1. Copy Files to Your Laravel Project

```bash
# Copy Artisan command
cp app/Console/Commands/ImportMasterContactList.php app/Console/Commands/

# Copy migration
cp database/migrations/2024_08_06_add_metadata_to_contacts.php database/migrations/

# Copy controller
cp app/Http/Controllers/ContactImportController.php app/Http/Controllers/

# Copy view
cp resources/views/admin/crm/import/index.blade.php resources/views/admin/crm/import/
```

### 2. Run Migration

```bash
php artisan migrate
```

This adds two columns to your `contacts` table:
- `metadata` (JSON) - stores import details
- `last_contact_date` (timestamp) - tracks last contact time

### 3. Add Routes

Add to `routes/web.php`:

```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/crm/import')->group(function () {
        Route::get('/', [ContactImportController::class, 'index'])->name('admin.crm.import.index');
        Route::post('/preview', [ContactImportController::class, 'preview'])->name('admin.crm.import.preview');
        Route::post('/store', [ContactImportController::class, 'store'])->name('admin.crm.import.store');
        Route::get('/history', [ContactImportController::class, 'history'])->name('admin.crm.import.history');
    });
});
```

### 4. Update Contact Model

Ensure your `Contact` model allows the new fields:

```php
// app/Models/Contact.php

protected $fillable = [
    'email',
    'name',
    'company',
    'phone',
    'source',
    'stage',
    'last_contact_date',
    'metadata',
    'notes',
    'bounced_at',
    'updated_at',
];

protected $casts = [
    'metadata' => 'json',
    'last_contact_date' => 'timestamp',
];
```

## Usage Methods

### Method 1: Web Interface (Easiest)

1. Navigate to `/admin/crm/import`
2. Upload your Excel file
3. Preview the data
4. Click "Start Import"
5. Watch progress in real-time
6. See results with breakdown

**Best for:** Quick imports, visual feedback, interactive workflow

### Method 2: Artisan Command (Advanced)

```bash
# Standard import
php artisan crm:import-contacts /path/to/file.xlsx

# Dry run (preview only)
php artisan crm:import-contacts /path/to/file.xlsx --dry-run

# Skip existing email addresses
php artisan crm:import-contacts /path/to/file.xlsx --skip-duplicates

# All options together
php artisan crm:import-contacts /path/to/file.xlsx --dry-run --skip-duplicates
```

**Best for:** Automated/scheduled imports, CI/CD pipelines, testing

### Method 3: Scheduled Task (For Regular Imports)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Import master list weekly on Monday morning
    $schedule->command('crm:import-contacts', [
        'file' => storage_path('imports/master-list-latest.xlsx'),
        '--skip-duplicates'
    ])
    ->weekly()
    ->mondays()
    ->at('08:00')
    ->appendOutputTo(storage_path('logs/import.log'));
}
```

Then keep `storage/imports/master-list-latest.xlsx` updated with your latest list.

## File Structure

Your Excel file should have:

- **Sheet Name:** "Master List"
- **Headers (Row 1):** Email, Company, Contact Name, Phone, Last Part# Referenced, Last Mfg Referenced, Last Date Seen, Times Seen Across Files, Primary Source File, Primary Source Sheet, Other Company Names Seen

**Example:**
| Email | Company | Contact Name | Phone | Last Part# Referenced | ... |
|-------|---------|--------------|-------|----------------------|-----|
| john@example.com | Acme Inc | John Smith | +1-555-1234 | 12345 | ... |

## Data Mapping

The import maps Excel columns to your Contact model:

```
Excel Column → Contact Field
─────────────────────────────
Email → email
Company → company
Contact Name → name
Phone → phone
Last Part# Referenced → metadata.last_part_referenced
Last Manufacturer Referenced → metadata.last_manufacturer
Last Date Seen → last_contact_date
Times Seen Across Files → metadata.times_seen
Primary Source File → metadata.source_file
Primary Source Sheet → metadata.source_sheet
```

All imports are tagged with `source = 'master_list'` for tracking.

## Import Behavior

### Duplicate Handling

When importing with `--skip-duplicates`:
- ✅ Emails already in system are **not** created again
- ✅ Statistics show how many duplicates were skipped
- ✅ Metadata is updated for existing contacts

### Data Cleaning

The import automatically:
- ✅ Removes placeholder values (single dot, double dot, etc.)
- ✅ Validates email format (standard RFC)
- ✅ Cleans phone numbers (removes invalid/short numbers)
- ✅ Parses dates to proper timestamps
- ✅ Trims whitespace

### Bounce Protection

Contacts marked as `bounced_at` are:
- ✅ Not imported (if they have bounced)
- ✅ Never sent emails through bulk email interface
- ✅ Can be manually recovered by clearing `bounced_at`

### Transaction Safety

All imports run in database transactions:
- ✅ If import fails partway through, everything rolls back
- ✅ No partial data left in database
- ✅ You can retry safely

## Monitoring & Logging

### Activity Log

If using Spatie Activity Log:

```php
// View recent imports
use Spatie\Activitylog\Models\Activity;

Activity::where('description', 'contact_import')
    ->latest()
    ->limit(10)
    ->get();
```

### Manual Check

```php
// Count contacts imported from master list
Contact::where('source', 'master_list')->count();

// Find recently imported
Contact::where('source', 'master_list')
    ->whereDate('created_at', today())
    ->count();

// View metadata
Contact::find(1)->metadata;
// Output: [
//   "times_seen" => 4,
//   "source_file" => "Marketing Data/...",
//   "source_sheet" => "Sheet1",
//   "imported_at" => "2024-08-06 10:30:00"
// ]
```

## Performance Notes

**Import Speed:**
- ~1,000 contacts/minute on typical shared hosting
- ~5,000 contacts/minute on modern servers
- First import slower due to indexing

**For 7,316 contacts:**
- Time: ~7-10 minutes via web interface
- Time: ~4-5 minutes via command line (no progress updates)
- Memory: ~50MB (safe even on 128MB PHP limit)

**Large Files (50,000+):**
```bash
# Increase PHP limits temporarily
php -d memory_limit=512M artisan crm:import-contacts file.xlsx
```

## Troubleshooting

### "Sheet 'Master List' not found"

Your Excel file structure is different. Check:
```bash
# Use a tool to inspect sheets
python3 -c "
import openpyxl
wb = openpyxl.load_workbook('your-file.xlsx')
print(wb.sheetnames)  # See all sheet names
"
```

Then update the command/controller to use the correct sheet name.

### "Permission denied" errors

Check file permissions:
```bash
chmod 644 storage/imports/file.xlsx
chmod 755 storage/imports/
```

### Import is slow

- Check database indexes on `email` column
- Use command line instead of web (faster)
- Schedule during off-peak hours
- Check server resources

### Duplicates still created

Use web interface to confirm:
1. Check "Skip existing email addresses" checkbox
2. Or use: `php artisan crm:import-contacts file.xlsx --skip-duplicates`

### Memory limit exceeded

Increase PHP memory:
```php
// In artisan command or controller
ini_set('memory_limit', '512M');
```

Or edit `php.ini`:
```ini
memory_limit = 256M
```

## Integration with Bulk Email

After import, contacts are immediately available in:
- `/admin/crm/bulk-email` - Select and email them
- Dashboard statistics
- Pipeline/reports
- Export functions

New contacts default to:
- `source` = 'master_list'
- `stage` = 'lead'
- `bounced_at` = null (safe to email)

## Backup Strategy

Before large imports:

```bash
# Backup contacts table
mysqldump -u user -p database contacts > contacts_backup.sql

# Or use Laravel backup package
php artisan backup:run
```

If import goes wrong:

```bash
# Restore from backup
mysql -u user -p database < contacts_backup.sql

# Or delete just the imported batch
DELETE FROM contacts WHERE source = 'master_list' AND created_at > '2024-08-06';
```

## API Usage (For Developers)

```php
// Programmatic import
$controller = new ContactImportController();
$request = Request::create('/admin/crm/import/store', 'POST', [
    'file' => $file,
    'skip_duplicates' => true,
]);
$response = $controller->store($request);
```

## Security Notes

- ✅ File upload validated (XLSX only)
- ✅ CSRF protection enabled
- ✅ Authenticated users only (admin middleware)
- ✅ No arbitrary file execution
- ✅ Temporary files cleaned up
- ✅ SQL injection protected (parameterized queries)

## Updating Existing Data

The import **updates** existing contacts. To merge multiple import runs:

```php
// Contacts with same email get updated, not duplicated
Contact::create([
    'email' => 'john@example.com',
    'name' => 'John Smith',
    'company' => 'Acme',
]);

// Later, same email in new import:
// ↓ Updates the above contact instead of creating new one
```

## Scheduled Imports Setup

Keep import automated with cron:

```bash
# Edit crontab
crontab -e

# Add line (runs Monday 8am):
0 8 * * 1 cd /var/www/mysite && php artisan crm:import-contacts storage/imports/master-list.xlsx --skip-duplicates >> storage/logs/cron.log 2>&1
```

## Questions?

Refer to:
- Contact model: `app/Models/Contact.php`
- Import command: `app/Console/Commands/ImportMasterContactList.php`
- Controller: `app/Http/Controllers/ContactImportController.php`
- Laravel Docs: https://laravel.com/docs/migrations

Good luck! 🚀

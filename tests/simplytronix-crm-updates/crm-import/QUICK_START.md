# Import Master Contact List - Quick Start

## TL;DR (3 Steps to Import)

### Step 1: Copy Files
```bash
# Bash to your Laravel app directory
cp -r crm-import/app ./
cp -r crm-import/database ./
cp -r crm-import/resources ./
```

### Step 2: Migrate Database
```bash
php artisan migrate
```

### Step 3: Add Routes
Add to `routes/web.php`:
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/crm/import')->group(function () {
        Route::get('/', [ContactImportController::class, 'index'])->name('admin.crm.import.index');
        Route::post('/preview', [ContactImportController::class, 'preview'])->name('admin.crm.import.preview');
        Route::post('/store', [ContactImportController::class, 'store'])->name('admin.crm.import.store');
    });
});
```

Done! Now visit: `http://yoursite.com/admin/crm/import`

---

## File Requirements

✅ Excel format (.xlsx or .xls)
✅ Sheet named "Master List"
✅ Headers in row 1
✅ Email column (required)
✅ Max 50MB

---

## Import Methods

### 🌐 Web Interface (Easiest)
```
Visit: /admin/crm/import
- Upload file
- Preview data
- Click "Import"
- Done ✓
```

### 💻 Command Line (Fast)
```bash
php artisan crm:import-contacts /path/to/file.xlsx
```

### 🏃 Dry Run (Test First)
```bash
php artisan crm:import-contacts /path/to/file.xlsx --dry-run
```

---

## What Gets Imported

| Excel Column | → | CRM Field |
|---|---|---|
| Email | → | email |
| Company | → | company |
| Contact Name | → | name |
| Phone | → | phone |
| Last Date Seen | → | last_contact_date |

Extra data stored in `metadata` (for reference/history)

---

## Import Results (7,316 contacts example)

✅ **7,292** New contacts added
✅ **24** Updated (already existed)
⏭️ **0** Duplicates skipped (if enabled)
❌ **0** Invalid records

---

## After Import

### 📧 Send Bulk Email
- Go to: `/admin/crm/bulk-email`
- Select contacts by source/filter
- Compose and send

### 📊 Check Dashboard
- New contact count updated
- "Master List" source tracked
- Metadata stored for history

### 🔗 Export/Use
- CSV export with all fields
- API access via metadata
- Dashboard reports include all

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Sheet not found" | Check sheet name in Excel - must be "Master List" |
| Import too slow | Use command line: `php artisan crm:import-contacts file.xlsx` |
| Memory errors | Increase PHP limit: `php -d memory_limit=512M artisan crm:import-contacts file.xlsx` |
| Duplicates created | Use `--skip-duplicates` flag or check web interface checkbox |
| Import fails halfway | Entire import rolls back (safe to retry) |

---

## File Structure After Setup

```
app/
├── Console/Commands/
│   └── ImportMasterContactList.php
├── Http/Controllers/
│   └── ContactImportController.php
└── Models/
    └── Contact.php (update $fillable)

database/
├── migrations/
│   └── 2024_08_06_add_metadata_to_contacts.php
└── seeders/

resources/views/
└── admin/crm/import/
    └── index.blade.php

routes/
└── web.php (update with routes)
```

---

## Contact Model Update

Update `app/Models/Contact.php`:

```php
protected $fillable = [
    'email', 'name', 'company', 'phone',
    'source', 'stage', 'last_contact_date',
    'metadata', 'notes', 'bounced_at'
];

protected $casts = [
    'metadata' => 'json',
    'last_contact_date' => 'timestamp',
];
```

---

## Routes to Add

```php
// In routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/crm/import')->group(function () {
        Route::get('/', [ContactImportController::class, 'index'])
            ->name('admin.crm.import.index');
        Route::post('/preview', [ContactImportController::class, 'preview'])
            ->name('admin.crm.import.preview');
        Route::post('/store', [ContactImportController::class, 'store'])
            ->name('admin.crm.import.store');
    });
});
```

---

## Database Fields Added

```sql
ALTER TABLE contacts ADD COLUMN metadata JSON NULL;
ALTER TABLE contacts ADD COLUMN last_contact_date TIMESTAMP NULL;
```

The migration handles this automatically via `php artisan migrate`.

---

## Data Cleaning

Automatically cleaned during import:
- ✅ Invalid emails rejected
- ✅ Placeholder values removed (., .., —)
- ✅ Phone numbers validated (min 7 chars)
- ✅ Whitespace trimmed
- ✅ Dates parsed to timestamps

---

## Metadata Stored

```json
{
  "times_seen": 4,
  "source_file": "Marketing Data/Complete Data/Tracking.xlsx",
  "source_sheet": "Sheet3",
  "imported_at": "2024-08-06T10:30:00Z"
}
```

Access via: `$contact->metadata['times_seen']`

---

## Skip Duplicates

### Via Web
- Check box: "Skip existing email addresses"
- Non-duplicate emails only

### Via CLI
```bash
php artisan crm:import-contacts file.xlsx --skip-duplicates
```

Same email addresses will **not** create new records, only update existing ones.

---

## Performance

- ~1,000 contacts/min (typical)
- ~5,000 contacts/min (optimized)
- 7,316 contacts: ~7 minutes web, ~4 min CLI

---

## Safety Features

✅ Transaction-based (all or nothing)
✅ Bounce protection (bounced emails not imported)
✅ CSRF protected
✅ Auth required
✅ No file execution
✅ Parameterized queries (no SQL injection)

---

## Backup First!

```bash
# MySQL backup
mysqldump -u user -p database contacts > contacts_backup.sql

# Or quick delete imported batch
DELETE FROM contacts WHERE source = 'master_list' AND created_at > '2024-08-06';
```

---

## Next Steps After Import

1. ✅ Visit `/admin/crm/bulk-email`
2. ✅ Select contacts from "Master List" source
3. ✅ Compose email
4. ✅ Send to selected contacts
5. ✅ Check dashboard for stats

---

## Need Help?

- See: `SETUP_GUIDE.md` for full documentation
- Check: `MIGRATION.md` for bulk-email refactor migration
- Original upload included the refactored blade files

---

**You're ready! 🚀**

Visit: `http://yoursite.com/admin/crm/import`

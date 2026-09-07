# Bulk Email Refactor

Professional refactor of the Simplytronix bulk email component with modular Blade components, organized CSS, and clean JavaScript.

## File Structure

```
resources/views/admin/crm/bulk-email/
├── index.blade.php                          # Main view (clean layout only)
└── components/
    ├── stats-bar.blade.php                  # Stats cards (4 metrics)
    ├── contact-selector.blade.php           # Contact table with filters
    ├── compose-panel.blade.php              # Email composition area
    └── signature-editor.blade.php           # Signature management

public/
├── css/crm/
│   └── bulk-email.css                       # Professional styling
└── js/crm/
    └── bulk-email.js                        # All functionality (modular)
```

## Installation

### Step 1: Upload Files

Extract the zip and upload to your Laravel project:

```bash
# Blade components
resources/views/admin/crm/bulk-email/

# CSS and JavaScript
public/css/crm/bulk-email.css
public/js/crm/bulk-email.js
```

### Step 2: Update Routes (if needed)

Verify your routes in `routes/web.php` match the action in `contact-selector.blade.php`:

```php
Route::post('/admin/crm/bulk-email/send', [BulkEmailController::class, 'send'])
    ->name('admin.crm.bulk_email.send');

Route::post('/admin/crm/bulk-email/signature', [BulkEmailController::class, 'saveSignature'])
    ->name('admin.crm.bulk_email.signature');
```

### Step 3: Update Your Controller

Make sure your controller passes `$contacts` and `$signature` to the view:

```php
public function index()
{
    $contacts = Contact::whereNull('bounced_at')
                       ->whereNotNull('email')
                       ->get();
    
    $signature = auth()->user()->email_signature ?? '';
    
    return view('admin.crm.bulk-email.index', [
        'contacts' => $contacts,
        'signature' => $signature
    ]);
}
```

## Key Features

### ✅ Professional Design
- Clean, modern interface with CSS custom properties
- Responsive grid layout (desktop → tablet → mobile)
- Consistent spacing and typography
- Smooth transitions and hover states

### ✅ Modular Components
- **Stats Bar**: 4 metric cards (emailable, selected, quote leads, visitor leads)
- **Contact Selector**: DataTable with search, source filter, multi-select
- **Compose Panel**: Subject input, rich text editor, send button
- **Signature Editor**: WYSIWYG editor with save/insert functionality

### ✅ JavaScript
- Modular code using IIFE pattern
- Proper event delegation for dynamic tables
- AJAX signature saving with error handling
- Form validation before submission
- Summernote WYSIWYG integration

### ✅ Accessibility
- Semantic HTML with ARIA labels
- Keyboard-friendly checkboxes
- Focus states on interactive elements
- Status announcements for async actions

### ✅ Mobile Responsive
- Stats cards stack on smaller screens
- Single-column layout on tablets
- Table columns hide intelligently on mobile
- Touch-friendly button sizes

## Customization

### Update Colors
Edit CSS custom properties in `bulk-email.css`:

```css
:root {
    --color-primary: #3b82f6;        /* Change primary blue */
    --color-success: #22c55e;        /* Change success green */
    --color-warning: #f59e0b;        /* Change warning amber */
    /* ... etc ... */
}
```

### Adjust DataTable Settings
In `bulk-email.js`, modify `initDataTable()`:

```javascript
dataTable = $('#bulkContactsTable').DataTable({
    pageLength: 25,              // Rows per page
    lengthMenu: [[25,50,100,...],[25,50,100,...]],
    // ...
});
```

### Update Summernote Toolbar
In `bulk-email.js`:

```javascript
const SUMMERNOTE_TOOLBAR = [
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['fontname', 'fontsize', 'color']],
    // Add/remove tools as needed
];
```

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Android)

## Dependencies

- **jQuery** 3.6+
- **Bootstrap** 4.6+ (for base button/form styles)
- **DataTables** 1.10.24+
- **Summernote** 0.8.20+
- **CSRF Token** (Laravel middleware)

All CDN links are included in the main `index.blade.php`.

## Notes

- Bounced contacts are filtered server-side before rendering
- The signature is saved per-user (update your controller's save method)
- Emails are sent via your configured SMTP server
- DataTable search filters are applied before "select all" works
- Signature insertion appends to existing message with line break

## Troubleshooting

### Summernote not loading?
- Check CDN links in `index.blade.php`
- Verify jQuery is loaded before Summernote

### DataTable sorting not working?
- Ensure `pageLength` and `lengthMenu` match your needs
- Check for JavaScript console errors

### Form not submitting?
- Verify CSRF token is in your form
- Check that contacts are selected
- Ensure message body is not empty

### Styling looks off?
- Clear browser cache (Ctrl+Shift+Del)
- Check that `bulk-email.css` is loaded
- Verify Bootstrap 4 is included

## Support

For issues or customizations, refer to:
- DataTables: https://datatables.net/manual/
- Summernote: https://summernote.org/
- Bootstrap 4: https://getbootstrap.com/docs/4.6/

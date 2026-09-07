<form id="bulkEmailForm" method="POST" action="{{ route('admin.crm.bulk_email.send') }}" class="bulk-email-form">
@csrf

<div class="card card--elevated">
    <div class="card__header">
        <div class="card__title-section">
            <h4 class="card__title">Select Contacts</h4>
            <p class="card__description">
                {{ $contacts->count() }} contact{{ $contacts->count() !== 1 ? 's' : '' }} available
            </p>
        </div>
        <div class="card__controls">
            <input 
                type="text" 
                id="bulkSearch" 
                class="form-control form-control-sm" 
                placeholder="Search name, company, or email..."
                aria-label="Search contacts"
            >
            <select id="sourceFilter" class="form-control form-control-sm" aria-label="Filter by source">
                <option value="all">All sources</option>
                <option value="quote">Quote</option>
                <option value="visitor">Visitor</option>
                <option value="winback">Winback</option>
            </select>
        </div>
    </div>

    <div class="card__body">
        @if($contacts->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon">📭</div>
                <div class="empty-state__text">
                    No emailable contacts
                </div>
                <p class="empty-state__description">
                    Everyone is either bounced or has no email on file.
                </p>
            </div>
        @else
            <div class="select-all-wrapper">
                <label class="checkbox-label">
                    <input type="checkbox" id="selectAll" class="checkbox-input">
                    <span class="checkbox-text">Select / deselect all visible</span>
                </label>
            </div>

            <div class="table-wrapper">
                <table id="bulkContactsTable" class="table table-contacts dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th class="col-checkbox"></th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Stage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                        <tr data-source="{{ $contact->source }}" class="contact-row">
                            <td class="col-checkbox">
                                <input 
                                    type="checkbox" 
                                    name="contact_ids[]"
                                    class="contact-checkbox checkbox-input"
                                    value="{{ $contact->id }}"
                                    aria-label="Select {{ $contact->name ?? 'contact' }}"
                                >
                            </td>
                            <td class="col-name">
                                <span class="contact-name">{{ $contact->name ?: '—' }}</span>
                            </td>
                            <td class="col-company">
                                {{ $contact->company ?: '—' }}
                            </td>
                            <td class="col-email">
                                <a href="mailto:{{ $contact->email }}" class="contact-email">{{ $contact->email }}</a>
                            </td>
                            <td class="col-source">
                                @if($contact->source === 'quote')
                                    <span class="badge badge-info">Quote</span>
                                @elseif($contact->source === 'visitor')
                                    <span class="badge badge-secondary">Visitor</span>
                                @elseif($contact->source === 'winback')
                                    <span class="badge badge-warning">Winback</span>
                                @else
                                    <span class="badge badge-light">{{ ucfirst($contact->source) }}</span>
                                @endif
                            </td>
                            <td class="col-stage">
                                {{ ucfirst($contact->stage) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</form>

<div class="stats-grid">
    <!-- Total Emailable Contacts -->
    <div class="stat-card stat-card--primary">
        <div class="stat-card__icon">📧</div>
        <div class="stat-card__content">
            <div class="stat-card__value">{{ $contacts->count() }}</div>
            <div class="stat-card__label">Emailable Contacts</div>
        </div>
    </div>

    <!-- Selected Count -->
    <div class="stat-card stat-card--info">
        <div class="stat-card__icon">✓</div>
        <div class="stat-card__content">
            <div class="stat-card__value" id="selectedCountStat">0</div>
            <div class="stat-card__label">Selected</div>
        </div>
    </div>

    <!-- Quote Leads -->
    <div class="stat-card stat-card--success">
        <div class="stat-card__icon">💬</div>
        <div class="stat-card__content">
            <div class="stat-card__value">{{ $contacts->where('source', 'quote')->count() }}</div>
            <div class="stat-card__label">Quote Leads</div>
        </div>
    </div>

    <!-- Visitor Leads -->
    <div class="stat-card stat-card--warning">
        <div class="stat-card__icon">👁️</div>
        <div class="stat-card__content">
            <div class="stat-card__value">{{ $contacts->where('source', 'visitor')->count() }}</div>
            <div class="stat-card__label">Visitor Leads</div>
        </div>
    </div>
</div>

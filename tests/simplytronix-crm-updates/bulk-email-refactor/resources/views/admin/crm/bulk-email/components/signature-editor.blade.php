<div class="card card--elevated card--signature">
    <div class="card__header">
        <div class="card__title-section">
            <h4 class="card__title">Email Signature</h4>
            <p class="card__description">
                Saved here once, then use "Insert signature" above to add it to any bulk email.
                This does not automatically attach to cadence/follow-up emails — those use their own templates.
            </p>
        </div>
    </div>

    <div class="card__body signature-body">
        <textarea 
            id="signatureEditor" 
            aria-label="Email signature editor"
        >{!! $signature !!}</textarea>

        <div class="signature-actions">
            <button 
                type="button" 
                class="btn btn-sm btn-outline-success" 
                id="saveSignatureBtn"
            >
                Save signature
            </button>
            <span 
                id="signatureSavedNote" 
                class="signature-saved-note"
                role="status"
                aria-live="polite"
            >
                Saved ✓
            </span>
        </div>
    </div>
</div>

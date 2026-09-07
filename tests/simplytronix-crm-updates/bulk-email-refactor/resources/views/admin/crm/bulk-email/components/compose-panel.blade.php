<div class="card card--elevated card--compose">
    <div class="card__header">
        <div class="card__title-section">
            <h4 class="card__title">Compose Email</h4>
            <p class="card__description">
                Bounced contacts are automatically excluded, so anyone selected is safe to send to.
            </p>
        </div>
    </div>

    <div class="card__body compose-body">
        <div class="form-group">
            <label for="bulkSubject" class="form-label form-label--uppercase">
                Subject
            </label>
            <input 
                type="text" 
                id="bulkSubject" 
                name="subject" 
                class="form-control" 
                placeholder="e.g. New stock arriving — updated lead times"
                required
                aria-required="true"
            >
        </div>

        <div class="form-group">
            <div class="form-label-wrapper">
                <label for="bulkBody" class="form-label form-label--uppercase">
                    Message
                </label>
                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-secondary" 
                    id="insertSignatureBtn"
                >
                    Insert signature
                </button>
            </div>
            <textarea 
                id="bulkBody" 
                name="body" 
                aria-label="Email message body"
            ></textarea>
        </div>

        <div class="form-actions">
            <button 
                type="submit" 
                form="bulkEmailForm"
                class="btn btn-primary btn-lg btn-block" 
                id="bulkSendBtn"
            >
                <span class="btn-text">Send to</span>
                <span class="btn-count" id="selectedCountBtn">0</span>
                <span class="btn-text">contact(s)</span>
            </button>
        </div>
    </div>
</div>

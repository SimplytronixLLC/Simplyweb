# Simplytronix CRM Rebuild — Architecture

## One assumption flagged up front
Your pipeline currently guarantees "automation never starts a cadence on its own —
only a human clicking 'send day-0' does." Your new spec asks for **automatic**
follow-up on web-form leads after 30 days with no manual action. These two rules
conflict for web leads specifically. I've resolved it as: **the manual-start-only
rule still governs the Pipeline/Kanban board in general, but web-form leads get one
exception — a scheduled job auto-enrolls them into the cadence at day 30 if no
manual action has been logged and the stage is still `new`.** Every other lead
source (winback, manually added, imported) still requires a human to click send.
Say the word if you want it the other way.

## New/changed schema (migration 1)
`crm_contacts` gets:
- `email_status` enum: `active | bounced | invalid` (default `active`)
- `bounce_count` int, `last_bounced_at` timestamp
- `manual_action` enum: `none, rfq_received, quote_sent, quote_signed, quote_unsigned, no_quote, invalid_rfq`
- `manual_action_at`, `manual_action_note`
- `lead_source` string (web_form, winback, manual, import — generalizes the old `source` column's marketing role)
- `cadence_type` string nullable (`marketing`, `winback`) — replaces the implicit
  "winback-only" assumption in `followup_step`/`next_followup_at` so the same two
  columns can drive *any* cadence, including the new 0/7/14 marketing one and bulk sends.

New tables:
- `crm_email_log` gains `message_token` (uuid, unique) — embedded in every outgoing
  email's `Message-ID` header so bounce DSNs can be matched back to the exact send.
- `crm_email_batches` (id, subject, body, created_by, sent_at, recipient_count)
- `crm_email_batch_recipients` (batch_id, contact_id, status: queued/sent/bounced/skipped)

## Bounce tracking (no ESP, plain SMTP)
Since there's no webhook, bounces come back as DSN (delivery-status-notification)
emails into a dedicated mailbox (e.g. `bounces@simplytronix.com`), which you'd
create in cPanel and forward failures to, or set as the Return-Path.

Pipeline:
1. Every outgoing email (`CrmContact::logEmail` and the new bulk-send job) generates
   a `message_token` UUID and sets it as the `Message-ID` header when sending via
   `Mail::to(...)->send()`.
2. `crm:process-bounces` (new scheduled command, runs every 15 min) connects over
   IMAP to the bounce mailbox, reads unread messages, and for each one:
   - Parses the DSN body for `Original-Message-Id:` (or falls back to scanning the
     token pattern in the bounced body if the DSN strips headers — some servers do).
   - Matches the token to a `crm_email_log` row → gets `contact_id`.
   - Distinguishes hard vs soft bounce from the DSN `Action:` field (`failed` = hard).
   - On hard bounce: increments `bounce_count`; at 2 hard bounces sets
     `email_status = bounced` **and marks the contact inactive** (stage untouched,
     but automation is halted the same way a manual stage-move halts it) per your spec.
   - Marks the IMAP message as read/moves it to a Processed folder so it isn't
     re-parsed.
3. Every send path checks `email_status !== 'bounced'` before sending — bounced
   contacts are silently skipped in cadence and bulk sends, and flagged in the UI.

## Cadence engine (unified)
`crm:sync-daily` (existing command, extended) now drives *any* contact with
`cadence_type` set and `automation_enabled = true`, using
`config('crm.cadence_days.marketing')` = `[0,7,14]` or `config('crm.cadence_days.winback')`
for the old track — same engine, different template set and stop conditions.
Stops automatically when: stage moved manually, `manual_action` logged, or
`email_status = bounced`.

## Manual action tracking
Added to the pipeline card detail panel (dropdown + note field), logged as a
`crm_activities` row (`type = manual_action`) same pattern as the existing
`addNote`. Setting a manual action halts automation for that contact, same as a
stage move — both are "a human touched this lead" signals.

## Bulk email (user-selectable contacts)
Contact list view (all contacts, filterable/sortable) gets checkboxes → "Email
selected" → compose modal → creates one `crm_email_batches` row + one
`crm_email_batch_recipients` row per contact → queued job sends one at a time,
respecting `email_status`, and logs each send to `crm_email_log` with its own
`message_token` so these are bounce-tracked identically to cadence emails.

## Dashboard
New `CrmDashboardController` — cards + charts (Chart.js) for: leads by stage,
leads by source (web form / winback / manual) over time, cadence funnel
(enrolled → step 1 → step 2 → step 3 → completed/bounced), bounce rate, and a
CSV export button (streamed, no external package needed) for any filtered
contact list or the dashboard's underlying tables.

## Files in this delivery
- `migrations/2026_08_04_000001_crm_rebuild_schema.php`
- `app/Console/Commands/ProcessCrmBounces.php`
- `app/Services/CrmCadenceService.php` — the unified cadence engine, called from `crm:sync-daily`
- `app/Http/Controllers/Admin/CrmBulkEmailController.php`
- `app/Http/Controllers/Admin/CrmDashboardController.php`
- `app/Mail/CrmTrackedMail.php` — mailable that stamps the VERP `message_token` into `Message-ID`

These are additive to your existing `CrmContact`, `CrmPipelineController`,
`CrmWinbackController`, `CrmDuplicatesController` — none of those need to be
rewritten, just extended (I've noted the exact additions needed in comments).

Next batch (say which to prioritize): the Blade UI for the manual-action dropdown,
the bulk-email compose modal, the dashboard charts view, and the CSV export
endpoints.

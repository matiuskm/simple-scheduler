# Plan 04 — User Experience Improvements (Optional)

Objective: Improve daily usability with personal timelines and minimal notifications.

Scope & Deliverables
- Personal schedule timeline showing upcoming schedules with statuses/roles.
- Email notifications for assignment confirmation, forced release, and schedule cancellation.

Workstreams
- Timeline: API query for user-specific upcoming schedules; UI component with chronological list and status badges; link to conflict warnings when relevant.
- Notifications: mail templates and triggers on assignment/forced release/cancellation; opt-out settings; reuse audit metadata for context.
- Infrastructure: ensure mail transport configured per environment; queue jobs for sends to avoid latency.
- Tests: timeline query correctness (status filters, time bounds), notification triggers and templates.

Dependencies
- Accurate statuses and roles from Plans 01 and 03.
- Email transport credentials per environment; fallback/feature flag for dev/test.

Risks/Mitigations
- Notification noise -> keep minimal triggers and batch if multiple updates close together.
- Stale timeline data -> cache bust/invalidate on assignment changes.

Definition of Done
- Users can view a clear upcoming timeline with statuses/roles and links to schedule detail.
- Emails send on the defined events with correct context; feature-flagged and queued.
- Tests cover timeline queries and notification triggers/templates.

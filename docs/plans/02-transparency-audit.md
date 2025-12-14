# Plan 02 — Transparency & Auditability

Objective: Make schedule changes traceable and surface soft conflict warnings.

Scope & Deliverables
- Audit log of schedule events: assignments, releases, admin overrides, status changes.
- User-facing change history view per schedule.
- Soft conflict detection for overlapping schedules (per location and per person) with non-blocking warnings.

Workstreams
- Data model: audit log table (actor, action, target, metadata, timestamp); indexes for querying per schedule/person.
- Event emission: emit audit entries from assignment/release flows, admin overrides, status transitions.
- Conflict detection: service to detect overlaps based on time/location/person; integrate into create/update flows and views.
- API/UI: endpoints/queries for history and conflicts; UI components for history timeline and warning badges/tooltips.
- Tests: audit log coverage, conflict detection edge cases (boundary times, timezone), API/UI wiring.

Dependencies
- Canonical clock and timezone decisions reused from Plan 01.
- Agreement on retention policy and visibility (admin vs user).
- Performance budget for conflict checks; may need indexes on time windows and location/person fields.

Risks/Mitigations
- Audit gaps if events missed -> centralize audit emission in domain services.
- Noisy conflict warnings -> add deduping and clear messaging; allow dismissing per view.
- Query overhead -> add targeted indexes and paginate history.

Definition of Done
- Audit log persisted and viewable per schedule with correct actor/action metadata.
- Soft conflicts detected and surfaced without blocking operations.
- Automated tests cover history generation and conflict detection.

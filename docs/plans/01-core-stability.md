# Plan 01 — Core Stability Improvements

Objective: Clarify schedule lifecycle and enforce pre-start locking to reduce operational risk.

Scope & Deliverables
- New schedule states: draft, published, open, full, locked, completed, cancelled.
- Lock window preventing user assignments/releases within X minutes of start; admin override available.
- UI indicators for capacity (assigned/required) with filters for schedules needing personnel.

Workstreams
- Domain model: extend Schedule status enum, migrations/data backfill, transitions validation.
- Business rules: services/commands enforcing lifecycle transitions, lock window, override path with audit entry.
- API/UI: expose states and capacity ratios, add progress indicators and filters; adjust assignment flows for lock window.
- Ops/observability: feature flags for rollout, logging around lock denials and overrides.
- Tests: status transition matrix, lock window behavior, capacity display and filtering.

Dependencies
- Access to schedule start times and required personnel fields (confirm schema).
- Timezone handling strategy for lock window (UTC vs per-location).
- Decision on lock window default and per-schedule overrides.

Risks/Mitigations
- Inconsistent status transitions -> add guardrail service and exhaustive tests.
- User confusion on locked state -> clear UI messaging and admin override path.
- Edge cases near start time -> use consistent clock source and small grace buffer.

Definition of Done
- New lifecycle states persisted and surfaced in API/UI with enforced transitions.
- Lock window blocks non-admin assignment/release; admin override logged.
- Capacity indicators visible and filterable; all changes covered by automated tests.

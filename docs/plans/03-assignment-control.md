# Plan 03 — Assignment Control Enhancements (Optional)

Objective: Add admin-reviewed assignment requests and role tagging without over-complicating flows.

Scope & Deliverables
- Request-based assignment workflow: user requests, admin approves/rejects.
- Role tags on assignments (leader, crew, supervisor) displayed in UI and exports.

Workstreams
- Data model: tables/fields for assignment requests (status, requester, target schedule, reason), roles on assignments.
- Workflow: endpoints/services for request submit/approve/reject; notifications/hooks for decisions.
- UI: request flows in schedule detail, admin inbox/filter for pending requests, role selection when approving.
- Policies/validation: enforce role uniqueness rules if needed (e.g., one leader), respect lock window rules from Plan 01.
- Tests: request lifecycle, permissions, role validations, lock window interactions.

Dependencies
- Completion of Plan 01 status and lock enforcement.
- Decision on role constraints (exclusive vs multi-select).
- Minimal notification channel (email) from Plan 04 if desired; otherwise in-app only.

Risks/Mitigations
- Approval latency -> add admin dashboard filters and optional default auto-approve flag for low-risk cases.
- Role sprawl -> keep small fixed enum; revisit after usage data.
- UX confusion -> clear statuses (requested/approved/rejected) and surface in personal timeline.

Definition of Done
- Users can request assignments; admins can approve/reject with audit trail.
- Roles stored and shown alongside assignments; validations enforced.
- Automated tests cover request flows, permissions, and role rules.

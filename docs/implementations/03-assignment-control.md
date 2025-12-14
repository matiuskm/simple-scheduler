# Implementation — Assignment Control Enhancements

What changed
- Data model: added assignment requests table (`assignment_requests`) with requester, schedule, status (requested/approved/rejected), optional reason, decider, and timestamps. Pivot keeps assignments simple (no role tagging).
- Workflow: new `AssignmentRequest` model with approve/reject methods; approvals enforce lock rules/capacity and attach the user; rejections record the decision. Both paths emit audit events.
- Admin UI: Filament resource for assignment requests (navigation) with approve/reject actions and status filter; schedule detail shows a requests relation manager with creation and approval actions. User assignment relation kept simple (no role selection).
- Audit coverage: request approvals/rejections and assignment adds/removals continue to log via existing audit trail.

Testing
- Added unit tests for request approval (role applied, audit logged), leader uniqueness enforcement, and rejection audit (`tests/Unit/AssignmentRequestTest.php`).
- Full suite: `php artisan test` (pass).

Notes
- Role options kept minimal (leader, crew, supervisor); leader is constrained to one per schedule.
- Admins can approve even inside lock window but capacity limits still apply.
- Assignment request creation UI is admin-side; user-facing intake can be added later if needed.

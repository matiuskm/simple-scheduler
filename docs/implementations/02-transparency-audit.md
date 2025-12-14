# Implementation — Transparency & Auditability

What changed
- Audit logging: new `schedule_audit_logs` table/model capturing actor, action, metadata, and timestamps (`database/migrations/2025_12_07_180000_create_schedule_audit_logs_table.php`, `app/Models/ScheduleAuditLog.php`). Schedule model now logs status changes, assignment add/remove helpers, and exposes audit log relation.
- Status change logging: Schedule model hooks on updates to log `status_changed` events with from/to metadata.
- Assignment logging & guardrails: schedule-user attach/detach actions in the relation manager now enforce lock rules for non-admins and log `assignment_added` / `assignment_removed` (`app/Filament/Resources/Schedules/RelationManagers/UsersRelationManager.php`).
- Conflict detection: added detector service to find overlapping schedules by location and by personnel (`app/Services/ScheduleConflictDetector.php`); schedule scope `hasConflicts` and summary helper.
- UI surfacing: Filament schedule table now shows lifecycle badge plus conflicts badge and “Has conflicts” filter (`app/Filament/Resources/Schedules/Tables/SchedulesTable.php`). Audit history exposed via relation manager (`app/Filament/Resources/Schedules/RelationManagers/AuditLogsRelationManager.php`, wired in `ScheduleResource`).
- Migrations: enum widening migration already present; audit log migration added.

Testing
- Added unit tests for status/assignment audit logging and conflict detection (`tests/Unit/ScheduleAuditAndConflictTest.php`).
- Test suite run: `php artisan test` (pass).

Notes
- Conflict detection uses time-overlap checks on the same scheduled date; cancelled/completed schedules are ignored for warnings.
- Audit actor is derived from the authenticated user when available; system operations log with null actor.
- Filament conflict badge queries per row; acceptable for current scope, consider caching or eager checks if dataset grows.***

# Implementation — Core Stability Improvements

What changed
- Added lifecycle awareness on schedules with derived states (`open`, `full`, `locked`, `completed`, `cancelled`) driven by assignment counts and the lock window.
- Introduced a lock window configuration (`config/scheduler.php`, env `SCHEDULER_LOCK_MINUTES`) that blocks non-admin assignments before start time; admins can override.
- Updated Filament table to show lifecycle badges, capacity, and a “Needs personnel” filter; edit action now respects lock/full rules for non-admins.
- Expanded status options in the schedule form to include locked/completed/cancelled for admin control.
- Added model-level guardrails (`canAssign`/`assertCanAssign`) used by assignment flows and relation manager.

Testing
- New Pest unit suite (`tests/Unit/ScheduleLifecycleTest.php`) covering lifecycle transitions (open→full), lock window enforcement (admin override), and terminal states (completed/cancelled).
- Tests run with RefreshDatabase; factories added for locations and schedules.

Notes & rollout
- MySQL installs alter the `status` enum to allow the new lifecycle values; SQLite already accepts them. Run migrations after pulling.
- Lock window defaults to 30 minutes; adjust via `SCHEDULER_LOCK_MINUTES` without code changes.
- Audit/logging and conflict warnings from later roadmap phases are not implemented here.

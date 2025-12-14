# Roadmap — Scheduler v1

This roadmap describes the **planned evolution** of the Scheduler application from a functional MVP into a more robust and production-ready internal scheduling tool.

All items listed here are **intentional next steps**, not mandatory features.  
Scope discipline remains a core principle.

---

## Goals of v1

Scheduler v1 focuses on:
- Operational reliability
- Clear schedule lifecycle
- Safer assignment behavior
- Better visibility for admins and users

The goal is **trust** in scheduling data, not feature volume.

---

## Phase 1 — Core Stability Improvements (High Priority)

Improvements that directly reduce operational risk.

### 1. Explicit Schedule Lifecycle

Introduce clearer schedule states beyond `draft` and `published`.

Proposed lifecycle:

- `draft` — Visible to admin only
- `published` — Open for assignment
- `open` — Published but still under required personnel
- `full` — Required personnel met
- `locked` — Close to start time, no changes allowed
- `completed` — Finished schedules
- `cancelled` — Cancelled by admin

**Why:**
- Makes schedule intent explicit
- Allows safer rule enforcement
- Improves UI consistency

---

### 2. Locking Window Before Start Time

Introduce a time-based lock:
- Schedules become **locked** X minutes before start time
- Users can no longer assign or release
- Admin override remains allowed

**Why:**
- Prevents last-minute chaos
- Reflects real-world scheduling constraints

---

### 3. Assignment Capacity Indicators

Enhance visibility of personnel requirements:
- Display `assigned / required` ratio
- Visual progress indicators (badge or progress bar)
- Filters for “still missing personnel”

**Why:**
- Enables quick decision making
- Helps admins identify problematic schedules instantly

---

## Phase 2 — Transparency & Auditability (Medium Priority)

Improvements focused on trust and traceability.

### 4. Schedule Change History

Track and display:
- Assignment events
- Release events
- Admin overrides
- Schedule status changes

**Why:**
- Enables accountability
- Simplifies dispute resolution
- Helps debug operational issues

---

### 5. Conflict Awareness (Soft Detection)

Detect potential conflicts without blocking:
- Overlapping schedules at same location
- Personnel assigned to overlapping times

**Why:**
- Provides safety warnings without restricting flexibility

---

## Phase 3 — Assignment Control Enhancements (Optional)

Features that improve coordination without overly complicating the system.

### 6. Request-Based Assignment

Allow users to:
- Request assignment to a schedule
- Admin approves or rejects requests

**Why:**
- Prevents automatic overbooking
- Retains admin control

---

### 7. Role-Aware Assignments

Allow basic role tagging per assignment:
- Leader
- Crew
- Supervisor

**Why:**
- Adds clarity without becoming an HR system

---

## Phase 4 — User Experience Improvements (Optional)

Enhancements aimed at daily usability.

### 8. Personal Schedule Timeline

User-focused view:
- Upcoming schedules only
- Chronological order
- Clear status labels

---

### 9. Minimal Notifications

Email notifications for:
- Assignment confirmation
- Forced release
- Schedule cancellation

**Why:**
- Reduces missed schedules
- No real-time infra required

---

## Explicit Non-Goals (v1)

The following are intentionally out of scope:
- Recurring schedules
- Calendar integrations
- Bulk auto-assignment
- Complex availability rules
- Shift templating

These belong to v2+ if real usage justifies them.

---

## Guiding Principles

- Schedule correctness over automation
- Visibility before convenience
- Explicit rules build trust
- Completion beats theoretical completeness

---

## Final Note

Scheduler v1 aims to be **predictable, auditable, and operationally safe**.

The MVP already proves functionality.  
v1 focuses on making the system trustworthy enough to rely on in real scenarios.

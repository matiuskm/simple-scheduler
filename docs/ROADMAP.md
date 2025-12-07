# Roadmap

This roadmap outlines **possible future improvements** for the Simple Scheduler & Assignment Tool.  
All items below were intentionally excluded from the MVP to maintain focus and ensure completion.

These are **ideas, not commitments**.

---

## Phase 1 — Post-MVP Improvements

Small, low-risk enhancements that build on the existing architecture.

### 1. User Self-Assignment
- Allow users to assign themselves to published schedules
- Enforce quota checks at the model/service layer
- Prevent duplicate self-assignments

**Value:**  
Reduces admin workload for routine scheduling.

---

### 2. Assignment Release
- Allow users to release a schedule they are assigned to
- Update availability and quota dynamically

**Value:**  
Improves flexibility for real-world changes.

---

### 3. Basic Notifications
- Email notification on:
  - Assignment
  - Unassignment
- Admin-only toggle for notifications

**Value:**  
Improves communication without introducing real-time complexity.

---

## Phase 2 — Admin Experience Enhancements

Features focused on admin productivity, not user-facing polish.

### 4. Schedule Overview Dashboard
- Widget for:
  - Upcoming schedules
  - Understaffed schedules
  - Fully staffed schedules

**Value:**  
Improves operational visibility.

---

### 5. Bulk Assignment Tools
- Assign multiple users to a schedule at once
- Optional CSV upload

**Value:**  
Useful for teams with large personnel pools.

---

### 6. Soft Deletion & Audit Trail
- Soft delete schedules
- Track:
  - Who created a schedule
  - Who modified or assigned users

**Value:**  
Helps with accountability and history tracking.

---

## Phase 3 — Advanced Scheduling Features (Optional)

Higher complexity features that may require architectural changes.

### 7. Recurring Schedules
- Weekly or monthly patterns
- Instance-based schedule generation

**Risk:**  
Significantly increases scheduling complexity.

---

### 8. Calendar View
- Weekly or monthly calendar visualization
- Read-only at first

**Risk:**  
Requires frontend work and UX considerations.

---

### 9. Conflict Detection
- Prevent users from being assigned to overlapping schedules
- Time window validation

**Risk:**  
Complex logic, may impact performance.

---

## Phase 4 — External Access

Only relevant if this tool evolves beyond internal usage.

### 10. Public or User-Facing Frontend
- Limited user portal
- View assigned schedules only

---

### 11. API Layer
- REST or GraphQL API
- Auth via tokens

**Use Case:**  
Integration with external systems.

---

## Non-Goals

The following are explicitly **out of scope** unless requirements change:
- Real-time collaboration
- Live presence indicators
- Full workforce management
- Payroll or HR integrations

---

## Guiding Principles

- Scope stays tight
- Features must solve a real operational need
- No implementation without real usage feedback
- Simplicity over cleverness

---

## Final Note

This roadmap exists to:
- Capture ideas without derailing progress
- Provide direction for future iterations
- Demonstrate intentional scope control

The MVP remains the primary deliverable.

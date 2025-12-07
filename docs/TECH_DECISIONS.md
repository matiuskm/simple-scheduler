# Technical Decisions

This document explains the key technical decisions made during the development of the **Simple Scheduler & Assignment Tool**.  
Each decision was made intentionally to keep the project focused, maintainable, and aligned with real-world internal tooling needs.

---

## 1. Filament-First Architecture

**Decision:**  
The application was designed with a Filament-first approach instead of building a public-facing frontend.

**Reasoning:**
- The primary users are admins managing internal data.
- Filament v4 provides rapid CRUD, authorization hooks, and relation management out of the box.
- This reduces boilerplate while keeping business logic explicit.

**Trade-off:**
- No public UI for end users in v1.
- UX prioritizes admin efficiency over aesthetics.

---

## 2. Explicit Scheduling Model (No Calendar Libraries)

**Decision:**  
Schedules are modeled explicitly using `date`, `start_time`, and `end_time` fields rather than relying on external calendar packages.

**Reasoning:**
- Prevents hidden logic from third-party abstractions.
- Keeps scheduling rules transparent and debuggable.
- Sufficient for fixed-time, non-recurring schedules.

**Trade-off:**
- No support for recurring events or drag-and-drop calendars.

---

## 3. Locations as a Separate Entity

**Decision:**  
Locations are stored as a dedicated table instead of hardcoded values.

**Reasoning:**
- New locations can be added without code changes.
- Enforces referential integrity at the database level.
- Supports future extensions like location capacity or metadata.

---

## 4. Quota Enforcement at Assignment Layer

**Decision:**  
Personnel quota is enforced during the assignment process via Filament Relation Manager hooks.

**Implementation:**
- `required_personnel` defines the maximum allowed assignments.
- The system blocks new assignments once the quota is reached.

**Reasoning:**
- Keeps quota logic close to the user interaction layer.
- Prevents over-assignment during admin operations.
- Reduces complexity in the initial domain model.

**Trade-off:**
- Quota enforcement exists at the UI layer for MVP.
- Future expansions may require model-level or service-level enforcement.

---

## 5. Draft vs Published Schedule Status

**Decision:**  
Schedules have a simple `draft` and `published` status instead of complex state machines.

**Reasoning:**
- Allows admins to prepare schedules without exposing them.
- Avoids over-engineering with workflow engines.
- Aligns with common internal tooling patterns.

---

## 6. Role Handling via Simple Flag

**Decision:**  
User roles are handled using a boolean `is_admin` flag.

**Reasoning:**
- Keeps authorization logic simple and readable.
- Avoids introducing unnecessary role/permission packages.
- Sufficient for a two-role system (admin vs user).

**Trade-off:**
- Not suitable for complex role hierarchies.
- Can be extended in future versions if needed.

---

## 7. Database-Level Constraints

**Decision:**  
A unique index prevents duplicate schedules with the same date, time, and location.

**Reasoning:**
- Ensures data integrity beyond application logic.
- Prevents accidental duplicates caused by concurrent actions.
- Reflects real-world scheduling constraints.

---

## 8. Strict Scope Control

**Decision:**  
Several features were intentionally excluded in the MVP.

**Excluded Features:**
- Recurring schedules
- Notifications
- Calendar UI
- Public frontend
- Reporting and exports

**Reasoning:**
- Focused on demonstrating project completion.
- Prioritized correctness and clarity over feature count.
- Keeps the codebase small and understandable.

---

## 9. No Premature Optimization

**Decision:**  
Queries, indexes, and caching were kept minimal.

**Reasoning:**
- Dataset is assumed to be small for internal usage.
- Avoids over-complicating early development.
- Optimization can be added when real usage patterns emerge.

---

## Summary

This project emphasizes:
- Clear domain modeling
- Explicit business rules
- Maintainable Laravel + Filament patterns
- Discipline in scope limitation

The architecture intentionally favors simplicity and clarity over extensibility for the sake of completing a solid, reviewable MVP.


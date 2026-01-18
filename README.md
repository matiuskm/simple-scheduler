# Simple Scheduler & Assignment Tool

Internal scheduling app for managing locations, schedules, and personnel assignments with quota control and role-based access.

Built with **Laravel 12** + **Filament v4**. The scope is intentionally focused: admin workflows first, clean domain modeling, and practical Filament usage.

## ✨ Highlights
- Admin-managed schedules with quota enforcement
- Role-based access via `is_admin`
- Conflict and audit tracking
- Add-to-calendar (Google Calendar + .ics download)
- Announcement banner for global notices

## 🎯 Problem Statement

In many internal teams, schedules are still managed using spreadsheets or chat messages, which leads to:
- Duplicate schedules
- Over-assignment of personnel
- Lack of visibility for upcoming tasks

This app solves that problem by providing:
- Centralized schedule management
- Clear personnel quota per schedule
- Admin-controlled assignments

---

## ✅ Core Features

### Admin
- Manage locations
- Create and manage schedules
- Define required personnel (quota) per schedule
- Assign users to schedules
- Automatically detect when a schedule is **full**
 - Publish announcements

### User
- View assigned schedules
- See upcoming schedules only
- No access to drafts or admin-only data
 - Add schedules to calendar

## 🧱 Domain Model

### Entities
- **User**
- **Location**
- **Schedule**
- **ScheduleUser** (pivot table)
- **Announcement**

### Relationships
- One location → many schedules  
- One schedule → many users  
- One user → many schedules  

### Key Business Rules
- A schedule has a fixed quota (`required_personnel`)
- When assigned users reach the quota, the schedule is considered **full**
- Draft schedules are visible only to admins
- Duplicate schedules for the same date, time, and location are prevented at the database level

---

## 🗄️ Database Structure

### `locations`
| Field | Type |
|------|------|
| id | bigint |
| name | string |
| address | string (nullable) |
| code | string (nullable) |

### `schedules`
| Field | Type |
|------|------|
| id | bigint |
| title | string |
| scheduled_date | date |
| start_time | time |
| end_time | time (nullable) |
| location_id | foreign key |
| status | enum (draft, published, open, full, locked, completed, cancelled) |
| required_personnel | integer |
| liturgical_color | string (nullable) |

Unique constraint:
```
(scheduled_date, start_time, location_id)
```

### `schedule_user`
| Field | Type |
|------|------|
| schedule_id | foreign key |
| user_id | foreign key |
| assigned_by | foreign key (nullable) |

## 🖥️ Admin Panel (Filament)

The admin panel is built using **Filament v4** and includes:
- CRUD for Locations
- CRUD for Schedules
- Relation Manager for assigning users to schedules
- Table indicators for:
  - Assigned personnel count
  - Required quota
  - Schedule status (draft / published)

Quota enforcement is handled directly in the assignment logic.

## 🛠️ Tech Stack

- **Backend:** Laravel 12
- **Admin UI:** Filament v4
- **Database:** MySQL
- **Authentication:** Laravel default auth
- **Authorization:** Policies + role flag (`is_admin`)
 - **Frontend:** Vite + Tailwind

## 🚀 Installation

```bash
git clone https://github.com/matiuskm/simple-scheduler.git
cd simple-scheduler

composer install
cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan serve
```

Create an admin user by setting `is_admin = true` in the `users` table.

Access admin panel at:
```
/admin
```

## 🧪 Testing

```bash
php artisan test
```

## 📌 Design Decisions

- **Filament-first approach:**  
  This project prioritizes internal admin workflows over public-facing UI.

- **No external calendar libraries:**  
  Schedule logic is intentionally simple and explicit.

- **Strict scope control:**  
  Features such as recurring schedules, notifications, and calendar views are intentionally excluded.

---

## 🔮 Possible Improvements

- User self-assignment to available schedules
- Notifications (email / in-app)
- Recurring schedules
- Calendar-style UI
- Public-facing frontend

---

## 📎 Purpose

This project is part of a personal portfolio and is designed to demonstrate:
- Clean Laravel model relationships
- Practical Filament usage
- Real-world admin use cases
- Strong scope control and project completion

## 📄 License

MIT

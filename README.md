# Simple Scheduler & Assignment Tool

A small internal scheduling application built with **Laravel 12** and **Filament v4**.  
This project focuses on managing schedules, locations, and personnel assignments with quota control and role-based access.

The goal of this project is **not** to build a full-featured calendar system, but to demonstrate clean domain modeling, Laravel relationships, and practical usage of Filament for admin workflows.

---

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

### User
- View assigned schedules
- See upcoming schedules only
- No access to drafts or admin-only data

---

## 🧱 Domain Model

### Entities
- **User**
- **Location**
- **Schedule**
- **ScheduleUser** (pivot table)

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
| date | date |
| start_time | time |
| end_time | time (nullable) |
| location_id | foreign key |
| status | enum (draft, published) |
| required_personnel | integer |

Unique constraint:
```
(date, start_time, location_id)
```

### `schedule_user`
| Field | Type |
|------|------|
| schedule_id | foreign key |
| user_id | foreign key |
| assigned_by | foreign key (nullable) |

---

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

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12
- **Admin UI:** Filament v4
- **Database:** MySQL
- **Authentication:** Laravel default auth
- **Authorization:** Policies + role flag (`is_admin`)

---

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

---

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

---

## 📄 License

MIT

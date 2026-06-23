# Campus Connect — University Events & Club Management System

A simple full-stack student-event platform:
**Frontend:** HTML5, CSS3, vanilla JavaScript + SVG illustrations  
**Backend:** PHP (no framework) + MySQL, built for **XAMPP**

## What it does

- Students sign up with their **Student ID** (format: `CourseCode/EnrollmentYear/RollNo`, e.g. `EC/20XX/XXX') and **university email** (`name@stu.kln.ac.lk`).
  - Access is only valid within 4 years of enrollment.
- Logged-in students can add events; new/edited events start as **pending**.
- Admins (added manually to the database) review pending events and **approve or reject** them.
- Approved events show up on the public **Upcoming Events** page, which anyone can browse without logging in.
- Students can **register** for an approved event (with an optional capacity limit).
- Admins can edit/delete any event, manage student accounts, and post simple **announcements**.

## Folder structure

```
campus-connect/
├── database/
│   └── schema.sql          ← run this once in phpMyAdmin / MySQL
├── backend/                ← PHP API (all JSON endpoints)
│   ├── config.php          ← DB credentials (defaults match XAMPP)
│   ├── session.php         ← session + auth helpers + validators
│   ├── auth/                signup.php, login.php, logout.php, check_session.php
│   ├── events/              list.php, get.php, create.php, update.php, delete.php, approve.php, register.php
│   ├── users/               list.php, update.php, delete.php (admin manages student accounts)
│   └── announcements/       list.php, create.php
├── frontend/
│   ├── css/    (theme.css is shared; no dead code)
│   ├── js/     (api.js is the shared fetch + validation helper)
│   ├── images/ (4 SVG illustrations: signup-hero, event-management, admin-dashboard, empty-state)
│   └── html/   index.html, signup.html, login.html, showevents.html, event.html, admin.html
├── DOCUMENTATION.md  ← Comprehensive developer guide (API, architecture, troubleshooting)
└── README.md         ← This file (quick start)
```

## Setup (XAMPP)

1. Install/start **XAMPP** and turn on **Apache** and **MySQL** in the control panel.
2. Copy the whole `campus-connect` folder into your XAMPP `htdocs` folder, e.g.
   `C:\xampp\htdocs\campus-connect` (Windows) or `/Applications/XAMPP/htdocs/campus-connect` (Mac).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), go to the **Import** tab, and import
   `database/schema.sql`. This creates the `campus_connect` database, its tables, and one
   ready-to-use admin account:
   - **User ID:** `Admin/001`
   - **Password:** `Admin@123`
   *(change this password once you've logged in, or just edit the row in phpMyAdmin)*
4. If your MySQL uses a different user/password than the XAMPP default (`root` / no password),
   edit `backend/config.php`.
5. Visit `http://localhost/campus-connect/frontend/html/index.html` in your browser.

That's it — no Composer, no Node build step, no extra PHP extensions beyond the default `mysqli`.

## Pages

| Page | Who it's for | What it does |
|---|---|---|
| `index.html` | everyone | Entry gate (validate Student ID format) → public events |
| `showevents.html` | everyone | Browse approved events, search, register |
| `signup.html` | new students | Create a student account (validated Student ID + university email) |
| `login.html` | students & admins | Real login (Student ID + password, or Admin ID + password) |
| `event.html` | logged-in students | Create events, see/edit/delete your own events |
| `admin.html` | logged-in admins | Approve/reject events, manage all events, manage students, post announcements |

## Key Features

✅ **Student ID Format Validation:** `CourseCode/EnrollmentYear/RollNo` (e.g. `EC/2022/049`)
  - Enrollment year must be within 4 years of current year
  - Validated client-side (instant feedback) and server-side (security)

✅ **University Email Enforcement:** `name@stu.kln.ac.lk`
  - Only official university email addresses allowed
  - Validated on both frontend and backend

✅ **Admin Student Management:**
  - View all student accounts with event counts
  - Edit student name/email
  - Delete student (cascades to their events)
  - Search by name, ID, or email

✅ **Modern Design System:**
  - Unified CSS theme across all pages
  - SVG illustrations (fast, scalable, no external requests)
  - Mobile-responsive layouts
  - Smooth animations

✅ **Clean Code:**
  - No unused functions or styles
  - Consistent naming conventions
  - Prepared SQL statements (SQL injection prevention)
  - Server-side validation on all inputs

## Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- Sessions are plain PHP `$_SESSION`, checked on every protected endpoint.
- Admin accounts are added directly to the `users` table (phpMyAdmin), not via signup.
- All SQL queries use prepared statements to prevent SQL injection.
- For detailed architecture, API endpoints, and troubleshooting, see **DOCUMENTATION.md**.

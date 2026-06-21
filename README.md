# Campus Connect — University Events & Club Management System

A simple full-stack student-event platform:
**Frontend:** HTML, CSS, vanilla JavaScript (the "Web and Internet Technology — modern theme")
**Backend:** PHP (no framework) + MySQL, built for **XAMPP**

## What it does

- Students sign up and log in with their **Student ID** + password.
- Logged-in students can add events; new/edited events start as **pending**.
- Admins (added manually to the database) review pending events and **approve or reject** them.
- Approved events show up on the public **Upcoming Events** page, which anyone can browse without logging in.
- Students can **register** (and unregister) for an approved event, with an optional capacity limit.
- Admins can edit/delete any event, manage student accounts, and post simple **announcements**.

## Folder structure

```
campus-connect/
├── database/
│   └── schema.sql            Database DDL + seed admin account
├── backend/                  PHP JSON API (no framework)
│   ├── config.php            DB connection (mysqli)
│   ├── session.php           Session bootstrap, CORS, auth helpers
│   ├── auth/                 signup, login, logout, check_session
│   ├── events/                list, get, create, update, delete, approve, register
│   ├── announcements/         list, create
│   └── users/                 list, delete  (admin-only, students only)
└── frontend/
    ├── css/                  theme.css shared by every page + per-page styles
    ├── js/                   api.js shared fetch helper + per-page logic
    └── html/                 index, signup, login, showevents, event, admin
```

## Setup (XAMPP)

1. Install/start **XAMPP** and turn on **Apache** and **MySQL** in the control panel.
2. Copy the whole `campus-connect` folder into your XAMPP `htdocs` folder, e.g.
   `C:\xampp\htdocs\campus-connect` (Windows) or `/Applications/XAMPP/htdocs/campus-connect` (Mac).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), go to the **Import** tab, and import
   `database/schema.sql`. This creates the `campus_connect` database, its tables, and one
   ready-to-use admin account:
   - **user ID:** `Admin/001`
   - **password:** `Admin@123`
   *(change this password once you've logged in, or just edit the row in phpMyAdmin)*
4. If your MySQL uses a different user/password than the XAMPP default (`root` / no password),
   edit `backend/config.php`.
5. Visit `http://localhost/campus-connect/frontend/html/index.html` in your browser.

That's it — no Composer, no Node build step, no extra PHP extensions beyond the default
`mysqli` that ships with XAMPP.

## Pages

| Page | Who it's for | What it does |
|---|---|---|
| `index.html` | everyone | Friendly entry gate (Student ID only) → public events |
| `showevents.html` | everyone | Browse approved events, search, register |
| `signup.html` | new students | Create a student account |
| `login.html` | students & admins | One login form for both, using Student ID or admin user ID + password |
| `event.html` | logged-in students | Add events, see/edit/delete your own events |
| `admin.html` | logged-in admins | Approve/reject events, manage all events and students, post announcements |

## Notes

- Students and admins live in **one unified `users` table**, distinguished by a `role` column
  (`student` or `admin`) — there is no separate `admins` table.
- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- Sessions are plain PHP sessions (`$_SESSION`), checked on every protected endpoint, and
  automatically expire after 2 hours of inactivity.
- Admin accounts are **not** created through the website — add them directly into the
  `users` table (phpMyAdmin → Insert), with `role` set to `admin` and a bcrypt hash in
  `password_hash`. You can generate a hash quickly by visiting a throwaway PHP file containing:
  `<?php echo password_hash('yourpassword', PASSWORD_BCRYPT);`
- All SQL queries use prepared statements to prevent SQL injection.

## Further documentation

- **Project & Operations Documentation** — overview, workflows, feature reference, setup,
  troubleshooting (for stakeholders/operators).
- **Developer & Technical Documentation** — architecture, database schema, full API reference,
  security notes, known limitations (for developers).

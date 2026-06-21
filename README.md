# Campus Connect — University Events & Club Management System

A simple full-stack student-event platform:
**Frontend:** HTML, CSS, vanilla JavaScript (the "Web and Internet Technology — modern theme")
**Backend:** PHP (no framework) + MySQL, built for **XAMPP**

## What it does

- Students sign up and log in with their **Student ID** + password.
- Logged-in students can add events; new/edited events start as **pending**.
- Admins (added manually to the database) review pending events and **approve or reject** them.
- Approved events show up on the public **Upcoming Events** page, which anyone can browse without logging in.
- Students can **register** for an approved event (with an optional capacity limit).
- Admins can edit/delete any event and post simple **announcements**.

## Folder structure

```
campus-connect/
├── database/
│   └── schema.sql          ← run this once in phpMyAdmin / MySQL
├── backend/                ← PHP API (all JSON endpoints)
│   ├── config.php          ← DB credentials (defaults match XAMPP)
│   ├── session.php         ← session + auth helpers
│   ├── auth/                signup.php, login.php, logout.php, check_session.php
│   ├── events/               list.php, get.php, create.php, update.php, delete.php, approve.php, register.php
│   └── announcements/        list.php, create.php
└── frontend/
    ├── css/   (theme.css is shared by every page)
    ├── js/    (api.js is the shared fetch helper, loaded by every page)
    └── html/  index.html, signup.html, login.html, showevents.html, event.html, admin.html
```

## Setup (XAMPP)

1. Install/start **XAMPP** and turn on **Apache** and **MySQL** in the control panel.
2. Copy the whole `campus-connect` folder into your XAMPP `htdocs` folder, e.g.
   `C:\xampp\htdocs\campus-connect` (Windows) or `/Applications/XAMPP/htdocs/campus-connect` (Mac).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), go to the **Import** tab, and import
   `database/schema.sql`. This creates the `campus_connect` database, its tables, and one
   ready-to-use admin account:
   - **username:** `admin`
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
| `login.html` | students & admins | Real login (Student ID + password, or Admin username + password) |
| `event.html` | logged-in students | Add events, see/edit/delete your own events |
| `admin.html` | logged-in admins | Approve/reject events, manage all events, post announcements |

## Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- Sessions are plain PHP sessions (`$_SESSION`), checked on every protected endpoint.
- Admin accounts are **not** created through the website — add them directly in the
  `admins` table (phpMyAdmin → Insert), using `password_hash()` for the password column.
  You can generate a hash quickly by visiting a throwaway PHP file containing:
  `<?php echo password_hash('yourpassword', PASSWORD_BCRYPT);`
- All SQL queries use prepared statements to prevent SQL injection.

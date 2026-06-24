# UniEvent — University Event Management System

*(Codebase branding: **Campus Connect**)*

A web-based university event management system for the Web Development course. Students can submit events, admins approve them, and approved events appear on a public listing where students can register.

## Technology Stack

- **HTML** — page structure
- **CSS** — styling and layout
- **JavaScript** — form validation, UI behavior, and `fetch()` calls to PHP endpoints
- **PHP** — server-side processing, sessions, MySQL access
- **MySQL** — persistent storage
- **XAMPP** — local Apache + MySQL development environment

## Main Features

| Feature | Status |
|---------|--------|
| Guest browse (Student ID gate + public events) | Implemented |
| Student signup / login | Implemented |
| Student submit & manage own events | Implemented |
| Admin approve / reject events | Implemented |
| Public approved events listing | Implemented |
| Student event registration / cancel | Implemented |
| Admin student list & delete | Implemented |
| Admin announcements (admin panel only) | Partially Implemented |
| Unified student dashboard (submissions + registrations) | Partially Implemented |

## Folder Structure

```
Web-and-Internet-Technology/
├── database/
│   └── schema.sql              # Database creation + admin seed
├── backend/
│   ├── config.php              # MySQL connection
│   ├── session.php             # Sessions, auth helpers, JSON responses
│   ├── auth/                   # signup, login, logout, check_session
│   ├── events/                 # CRUD, approve, register, list
│   ├── users/                  # list, delete (admin)
│   └── announcements/          # list, create (admin)
├── frontend/
│   ├── html/                   # index, login, signup, showevents, event, admin
│   ├── css/                    # theme + page styles
│   ├── js/                     # api.js + page scripts
│   └── images/                 # SVG illustrations
├── docs/                       # Presentation documentation
├── README.md
└── DOCUMENTATION.md            # Legacy developer notes (may be outdated)
```

## XAMPP Setup

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Place this project under `htdocs`, e.g.  
   `C:\xampp\htdocs\CampusConnect\Web-and-Internet-Technology`
3. Open phpMyAdmin: `http://localhost/phpmyadmin`
4. Confirm `backend/config.php` uses your MySQL credentials (default: `root` / empty password).

## Database Import

1. In phpMyAdmin, go to **Import**.
2. Select `database/schema.sql`.
3. Click **Go**.

This creates database **`campus_connect`** with tables: `users`, `events`, `registrations`, `announcements`, and seeds one admin account.

## Default Login Accounts

| Role | User ID | Password | Notes |
|------|---------|----------|-------|
| Admin | `Admin/001` | `Admin@123` | Seeded in `schema.sql` |
| Student | — | — | Create via **Sign up** (`signup.html`) |

**Student signup rules:**
- Student ID format: `EC/2022/049` (2–5 letter course code / 4-digit year / 3-digit roll)
- Enrollment year must be within the last 4 years
- Email must end with `@stu.kln.ac.lk`

## Demo Flow

1. Open `http://localhost/CampusConnect/Web-and-Internet-Technology/frontend/html/index.html`
2. Enter a valid-format Student ID → continue to events page
3. Sign up a student account (or log in if already created)
4. Create an event on **My Events** (`event.html`) — status becomes **pending**
5. Log out → log in as `Admin/001`
6. Approve the event on **Admin dashboard** (`admin.html`)
7. Open **Upcoming Events** (`showevents.html`) — event is visible
8. Log in as student → **Register** for the event
9. Log out

**Entry URL (adjust if your folder path differs):**
`http://localhost/CampusConnect/Web-and-Internet-Technology/frontend/html/index.html`

## Known Limitations

- Runs on **localhost only** (XAMPP); not production-deployed
- Uses **JavaScript `fetch()`** with JSON responses (not classic full-page form POST + redirect)
- **No seed student account** — create one before demo
- **Announcements** are stored and shown in the admin panel only (not on public pages)
- **No admin “create event” form** — admins approve/edit/delete; students create events
- **Student account edit** by admin is not implemented (delete only)
- Missing image reference: `showevents.js` expects `frontend/images/event.jpg` (file not in repo)
- Basic validation; no email verification, rate limiting, or production security hardening
- No payment, notifications, or real-time updates

For full documentation see **`docs/`**.

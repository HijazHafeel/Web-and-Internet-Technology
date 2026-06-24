# UniEvent — Project Documentation

**Course:** Web Development  
**System name (presentation):** UniEvent  
**Repository / UI branding:** Campus Connect  
**Database name:** `campus_connect`  
**Last updated from repository:** June 2026

---

## 1. Introduction

### What UniEvent is

UniEvent (implemented as **Campus Connect** in the codebase) is a university event management web application. It lets students propose campus events, lets administrators review and approve those events, and lets the campus community browse and register for approved events.

### Why the system is useful

Universities run many workshops, seminars, and club activities. Information is often scattered. UniEvent centralizes **approved** event information in one place so students can discover what is happening on campus.

### Problem it solves

- Students need a single place to find **verified, admin-approved** events
- Organizers need a simple way to **submit** event details for review
- Administrators need a workflow to **approve or reject** submissions before they go public
- Students need to **register** for events with optional capacity limits

---

## 2. Project Objectives

| Objective | Status |
|-----------|--------|
| Centralize university event information | Implemented |
| Allow students/guests to view approved events | Implemented |
| Allow users to register and login | Implemented |
| Allow event submission by students | Implemented |
| Allow admin approval/rejection | Implemented |
| Allow students to register for events | Implemented |
| Allow dashboard viewing | Partially Implemented |
| Post announcements | Partially Implemented |

**Notes:**
- **Student dashboard** is split: `event.html` shows submitted events; `showevents.html` handles browsing and registration.
- **Announcements** exist in the database and admin UI but are not shown on public student pages.

---

## 3. Scope

### In scope

- HTML/CSS/JS frontend pages
- PHP backend processors with MySQL
- PHP session-based login
- Student signup with ID and university email validation
- Event CRUD for students (own events) and admins (all events)
- Admin approval workflow (`pending` → `approved` / `rejected`)
- Public listing of approved events
- Event registration with capacity check
- Admin student list and delete
- Admin announcements (create/list in admin panel)

### Out of scope

- REST API as a separate product / microservices
- Frontend frameworks (React, Vue, etc.)
- Payment processing
- Email/SMS notifications
- Production deployment and security hardening
- Mobile native apps
- Real-time live updates

### Course context

This is a **Web Development course project** built for local XAMPP use. It demonstrates HTML, CSS, JavaScript, PHP, and MySQL working together — not an enterprise production system.

---

## 4. Technology Stack

| Technology | Role in this project |
|------------|---------------------|
| **HTML** | Static page structure in `frontend/html/` |
| **CSS** | Shared theme (`theme.css`) and page-specific styles |
| **JavaScript** | Client validation, dynamic lists, `fetch()` POST/GET to PHP |
| **PHP** | Auth, business logic, MySQL queries, JSON responses |
| **MySQL** | Users, events, registrations, announcements |
| **XAMPP** | Apache + MySQL + phpMyAdmin for local development |

### How forms actually work in this repository

Pages use HTML forms with JavaScript that **prevents default submit**, sends data via **`fetch()`** to PHP files, receives **JSON**, then updates the page or redirects with `window.location.href`. PHP uses **`$_SESSION`** for login state — not `header("Location: ...")` after every action.

---

## 5. User Roles

| Role | How created | Access |
|------|-------------|--------|
| **Guest** | No account | Home gate, public events browse (no register button without login) |
| **Student** | Signup page | Create/edit/delete own events, register for approved events |
| **Admin** | Seeded in SQL (`Admin/001`) | Approve/reject, manage all events, delete students, post announcements |

No other roles exist in the database (`users.role` is `student` or `admin` only).

---

## 6. Core Modules

### 6.1 Authentication

**Purpose:** Register students, log in users, maintain session, log out.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/html/login.html`, `signup.html`, `frontend/js/login.js`, `signup.js`, `backend/auth/login.php`, `signup.php`, `logout.php`, `check_session.php`, `backend/session.php` |
| **User action** | Fill signup/login form |
| **Backend action** | Validate input, hash password (signup), verify password (login), set `$_SESSION` |
| **Database** | `users` — INSERT (signup), SELECT (login) |

**Session variables:** `user_id`, `role`, `full_name`  
**Timeout:** 2 hours of inactivity (`session.php`)

---

### 6.2 Event Listing (Public)

**Purpose:** Show all **approved** events to anyone.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/html/showevents.html`, `frontend/js/showevents.js`, `backend/events/list.php?scope=public` |
| **User action** | Open events page, search/filter client-side |
| **Backend action** | SELECT events WHERE `status = 'approved'` |
| **Database** | `events`, JOIN `users` for creator name; `registrations` for counts |

---

### 6.3 Home Gate (Guest Entry)

**Purpose:** Lightweight Student ID format check before viewing events (not authentication).

| Item | Detail |
|------|--------|
| **Main files** | `frontend/html/index.html`, `frontend/js/index.js` |
| **User action** | Enter Student ID, click Continue |
| **Backend action** | None — validation is client-side only |
| **Database** | None |

---

### 6.4 Event Submission (Student)

**Purpose:** Students create events that start as **pending**.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/html/event.html`, `frontend/js/event.js`, `backend/events/create.php`, `update.php`, `delete.php`, `list.php?scope=mine` |
| **User action** | Fill event form, submit; edit/delete own events |
| **Backend action** | INSERT/UPDATE/DELETE with role checks; student edit resets status to `pending` |
| **Database** | `events` |

---

### 6.5 Admin Approval

**Purpose:** Admin reviews pending events and approves or rejects.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/html/admin.html`, `frontend/js/admin.js`, `backend/events/approve.php`, `list.php?scope=all&status=pending` |
| **User action** | Click Approve or Reject |
| **Backend action** | UPDATE `events.status`, set `approved_by` |
| **Database** | `events` |

---

### 6.6 Event Registration

**Purpose:** Logged-in students register for approved events; cancel registration.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/js/showevents.js`, `backend/events/register.php` |
| **User action** | Register / Cancel registration on events page |
| **Backend action** | INSERT/DELETE in `registrations`; check capacity |
| **Database** | `registrations`, `events` |

---

### 6.7 Dashboard

**Purpose:** Role-specific views.

| Dashboard | File | Contents |
|-----------|------|----------|
| Student “My Events” | `event.html` | Create form + list of own events with status |
| Public events + register | `showevents.html` | Approved events, stats, registration buttons |
| Admin | `admin.html` | Pending queue, all events, students, announcements |

There is **no single page** listing both “my submissions” and “my registrations” together.

---

### 6.8 Logout

**Purpose:** End session.

| Item | Detail |
|------|--------|
| **Main files** | `frontend/js/api.js` (`setNavLinks`), `backend/auth/logout.php` |
| **User action** | Click Log out in navbar |
| **Backend action** | Clear session, destroy cookie |
| **Database** | None |

---

### 6.9 Announcements (Admin)

**Purpose:** Admins post announcements stored in the database.

| Item | Detail |
|------|--------|
| **Main files** | `admin.html`, `admin.js`, `backend/announcements/create.php`, `list.php` |
| **Status** | Partially Implemented — visible in admin panel only, not on public pages |

---

### 6.10 Student Management (Admin)

**Purpose:** View and remove student accounts.

| Item | Detail |
|------|--------|
| **Main files** | `admin.js`, `backend/users/list.php`, `delete.php` |
| **Status** | Partially Implemented — list and delete work; **edit student** endpoint/UI not present |

---

## 7. Final Feature Status Table

| Feature | Status | Related Files | Notes |
|---------|--------|---------------|-------|
| Home / guest gate | Implemented | `index.html`, `index.js` | Format check only, not login |
| Public event browse | Implemented | `showevents.html`, `showevents.js`, `events/list.php` | Approved events only |
| Event search (client) | Implemented | `showevents.js` | Filters loaded list in browser |
| Student signup | Implemented | `signup.html`, `signup.js`, `auth/signup.php` | `@stu.kln.ac.lk` email rule |
| Student/admin login | Implemented | `login.html`, `login.js`, `auth/login.php` | Session-based |
| Session check / nav | Implemented | `api.js`, `auth/check_session.php` | Shows user name + logout |
| Student create event | Implemented | `event.html`, `event.js`, `events/create.php` | Starts as `pending` |
| Student edit own event | Implemented | `event.js`, `events/update.php` | Resets to `pending` |
| Student delete own event | Implemented | `event.js`, `events/delete.php` | |
| Admin approve/reject | Implemented | `admin.js`, `events/approve.php` | |
| Admin edit event | Partially Implemented | `admin.js`, `events/update.php` | Title edit via `prompt()` only |
| Admin delete event | Implemented | `admin.js`, `events/delete.php` | |
| Admin create event | Not Implemented | — | `create.php` requires student role |
| Approved events public | Implemented | `events/list.php?scope=public` | |
| Event registration | Implemented | `showevents.js`, `events/register.php` | Student login required |
| Cancel registration | Implemented | `register.php` action `unregister` | |
| Capacity limit | Implemented | `register.php` | NULL capacity = unlimited |
| Student dashboard (submissions) | Implemented | `event.html` | |
| Student dashboard (registrations) | Partially Implemented | `showevents.html` | No dedicated “my registrations” view |
| Admin dashboard | Implemented | `admin.html`, `admin.js` | |
| Admin student list | Implemented | `users/list.php` | |
| Admin delete student | Implemented | `users/delete.php` | Cascades events/registrations |
| Admin edit student | Not Implemented | — | No `users/update.php` |
| Announcements | Partially Implemented | `announcements/*`, admin tab | Not shown to students publicly |
| Logout | Implemented | `logout.php`, `api.js` | |
| Password hashing | Implemented | `signup.php`, `login.php` | bcrypt |
| Prepared SQL statements | Implemented | All PHP handlers | |
| Classic form POST + redirect | Not Implemented | — | Uses fetch + JSON instead |

---

## 8. Testing Summary

Manual test steps based on current implementation:

### Signup
1. Open `signup.html`
2. Use ID like `EC/2022/049`, email `test@stu.kln.ac.lk`, password ≥ 6 chars
3. Expect success message → redirect to login

### Login
1. Open `login.html`
2. Enter Student ID + password (or `Admin/001` / `Admin@123`)
3. Expect redirect to `event.html` (student) or `admin.html` (admin)

### Submit event
1. Log in as student → `event.html`
2. Fill title, date, time, location → Add event
3. Expect “waiting for admin approval”; event appears in list as **pending**

### Admin approval
1. Log in as admin → Pending tab
2. Click **Approve**
3. Expect success; event moves off pending list

### View approved event
1. Open `showevents.html` (guest or any user)
2. Expect approved event in list

### Register for event
1. Log in as student on `showevents.html`
2. Click **Register**
3. Expect button to change to **Cancel registration**

### Dashboard
1. Student: `event.html` shows own events with status badges
2. Admin: `admin.html` shows stats and tabs

### Cancel registration
1. On `showevents.html` as logged-in student
2. Click **Cancel registration**
3. Expect **Register** button again

### Logout
1. Click **Log out** in navbar
2. Expect redirect to login; protected pages redirect if revisited

---

## 9. Known Limitations

- **Localhost-only** (XAMPP) setup
- **fetch + JSON** architecture (differs from pure form POST + PHP redirect pattern)
- **No pre-seeded student** — must sign up for demo
- **Admin cannot create events** through the UI (only approve/edit/delete)
- **Announcements** not displayed on public pages
- **Broken image**: `event.jpg` referenced but missing
- **Admin student edit** not built
- **Guest gate** validates ID format only — does not verify account exists
- **2-hour session timeout** may interrupt long demos
- No email verification, CSRF tokens, or brute-force protection
- Google Fonts loaded from CDN (needs internet for typography)

---

## 10. Conclusion

UniEvent demonstrates core web development skills: structured HTML pages, CSS theming, JavaScript validation and interactivity, PHP server logic with sessions, and a normalized MySQL schema with foreign keys. The approval workflow mirrors a real university process — student submissions stay private until an admin approves them. The project stays within course boundaries as a focused, local full-stack application rather than an enterprise platform.

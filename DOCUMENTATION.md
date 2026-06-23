# Campus Connect — University Event Management System

**Version:** 1.0  
**Last Updated:** June 2026  
**Status:** Production Ready

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [System Requirements](#system-requirements)
4. [Installation](#installation)
5. [Project Structure](#project-structure)
6. [Architecture](#architecture)
7. [Database Schema](#database-schema)
8. [API Endpoints](#api-endpoints)
9. [User Roles & Workflows](#user-roles--workflows)
10. [Frontend Pages](#frontend-pages)
11. [Code Standards](#code-standards)
12. [Security Considerations](#security-considerations)
13. [Troubleshooting](#troubleshooting)
14. [Future Enhancements](#future-enhancements)

---

## Overview

**Campus Connect** is a full-stack web application designed for university students and administrators to discover, create, approve, and manage campus events. The system enforces a clean approval workflow where student-created events must be reviewed by an admin before appearing on the public event list.

**Core Value Proposition:**
- Students can quickly create and submit events for approval
- Admins have a centralized dashboard to review, approve, and manage all events and student accounts
- Public (unauthenticated) visitors can discover and register for approved events
- Secure session-based authentication with role-based access control

---

## Features

### For Students
✓ Sign up with university credentials (Student ID + university email)  
✓ Create and edit events (submitted for approval)  
✓ View pending/approved status of submitted events  
✓ Browse and register for approved campus events  
✓ Cancel event registrations  
✓ Dashboard showing personal event submissions and registered events  

### For Admins
✓ Dashboard with event and student statistics  
✓ Review pending events (approve or reject)  
✓ View all events across the system  
✓ Create and publish events immediately (bypass approval)  
✓ Edit or delete any event  
✓ Manage student user accounts (edit, delete)  
✓ Search and filter by student, event status, or name  
✓ Post announcements visible to all users  

### For Public Visitors
✓ Browse upcoming approved events without login  
✓ Validate student ID format before viewing events  
✓ See event details (date, time, location, capacity, organizer)  
✓ Register for events (if logged in)  

---

## System Requirements

### Minimum
- **Web Server:** Apache 2.4+ with `mod_rewrite` enabled
- **PHP:** 7.4+ (tested on 8.0+)
- **Database:** MySQL 5.7+ or MariaDB 10.2+
- **Browser:** Modern browsers supporting ES6 (Chrome, Firefox, Safari, Edge)

### Development Environment (XAMPP)
- XAMPP for Windows/Mac/Linux (includes Apache 2.4, PHP, MySQL)
- PhpMyAdmin for database administration (included in XAMPP)
- A text editor (VS Code, Sublime, etc.)

---

## Installation

### Step 1: Extract and Place Files
```bash
unzip campus-connect.zip
# Place the folder at: /htdocs/campus-connect  (for XAMPP)
# OR: /var/www/html/campus-connect  (for Linux)
```

### Step 2: Create Database
1. Open **XAMPP Control Panel** → Click **"Admin"** on MySQL
   - Or navigate to: `http://localhost/phpmyadmin`
2. Click **"New"** → Enter database name: `campus_connect`
3. Select **"Collation"** → `utf8mb4_unicode_ci`
4. Click **"Create"**

### Step 3: Run Database Schema
1. In phpMyAdmin, open the `campus_connect` database
2. Click **"Import"** tab
3. Choose `database/schema.sql` from the extracted folder
4. Click **"Go"**

This creates two tables:
- `users` (students + admins, unified role field)
- `events` (event listings with approval status)

### Step 4: Seed Admin Account
The schema includes a default admin:
- **User ID:** `Admin/001`
- **Password:** `Admin@123`

Login with these credentials to access the admin dashboard.

### Step 5: Start the Server
```bash
# XAMPP: Start Apache and MySQL from Control Panel
# Then visit: http://localhost/campus-connect/frontend/html/index.html
```

---

## Project Structure

```
campus-connect/
├── backend/
│   ├── config.php                    # DB connection (hardcoded for XAMPP local dev)
│   ├── session.php                   # Session management + role checks + validators
│   ├── auth/
│   │   ├── signup.php                # POST: register new student
│   │   ├── login.php                 # POST: authenticate user
│   │   ├── logout.php                # POST: destroy session
│   │   └── check_session.php         # GET: verify logged-in user
│   ├── events/
│   │   ├── list.php                  # GET: public/personal/admin event listing
│   │   ├── get.php                   # GET: single event details
│   │   ├── create.php                # POST: student/admin creates event
│   │   ├── update.php                # POST: edit event (student/admin)
│   │   ├── delete.php                # POST: remove event
│   │   ├── approve.php               # POST: admin approve/reject event
│   │   └── register.php              # POST: student registers for event
│   ├── users/
│   │   ├── list.php                  # GET: admin lists student accounts
│   │   ├── update.php                # POST: admin edits student
│   │   └── delete.php                # POST: admin deletes student
│   └── announcements/
│       ├── list.php                  # GET: fetch announcements
│       └── create.php                # POST: admin posts announcement
├── database/
│   └── schema.sql                    # SQL to create tables + seed admin
├── frontend/
│   ├── html/
│   │   ├── index.html                # Public gate (validate Student ID)
│   │   ├── login.html                # Sign in page
│   │   ├── signup.html               # Student registration
│   │   ├── showevents.html           # Public event listing
│   │   ├── event.html                # My events dashboard
│   │   └── admin.html                # Admin dashboard
│   ├── css/
│   │   ├── theme.css                 # Shared design system (all pages)
│   │   ├── index.css                 # Gate page styles
│   │   ├── login.css                 # Auth page styles
│   │   ├── signup.css                # Signup page styles
│   │   ├── event.css                 # Event dashboard styles
│   │   ├── showevents.css            # Public events page styles
│   │   └── admin.css                 # Admin dashboard styles
│   ├── js/
│   │   ├── api.js                    # Shared fetch + validation helpers
│   │   ├── index.js                  # Gate form handler
│   │   ├── login.js                  # Login form handler
│   │   ├── signup.js                 # Signup form handler
│   │   ├── event.js                  # My events dashboard logic
│   │   ├── showevents.js             # Public events listing logic
│   │   └── admin.js                  # Admin dashboard logic
│   └── images/
│       ├── signup-hero.svg           # SVG illustration for signup page
│       ├── event-management.svg      # SVG illustration for event dashboard
│       ├── admin-dashboard.svg       # SVG illustration for admin panel
│       └── empty-state.svg           # SVG illustration for no results
├── README.md                         # Quick start guide
└── DOCUMENTATION.md                  # This file
```

---

## Architecture

### Tech Stack

| Layer | Technology |
|-------|------------|
| **Frontend** | Vanilla HTML5, CSS3, JavaScript (ES6) |
| **Backend** | PHP 7.4+ (procedural, no framework) |
| **Database** | MySQL / MariaDB with MySQLi |
| **Session** | PHP native `$_SESSION` (cookie-based) |
| **Authentication** | Password hashing with `password_hash()` (bcrypt) |

### Design Philosophy

- **No frameworks:** Minimal dependencies → faster load, easier to understand
- **RESTful API:** Each endpoint returns JSON; frontend talks via `fetch()`
- **Single responsibility:** Each PHP file handles one API action
- **Client-side routing:** Frontend uses `window.location.href` to navigate between pages
- **Server-side validation:** Client-side checks are duplicated on the backend for security
- **SVG illustrations:** Fast-loading, scalable graphics with no external image requests

### Data Flow

```
User Browser
    ↓
HTML Form Submission / fetch()
    ↓
PHP Backend (config.php → session.php → specific handler)
    ↓
MySQL Database (query via MySQLi)
    ↓
JSON Response
    ↓
JavaScript processes + updates DOM
```

---

## Database Schema

### `users` Table

```sql
CREATE TABLE users (
  user_id             VARCHAR(50) PRIMARY KEY,         -- Unique student/admin ID
  full_name           VARCHAR(100) NOT NULL,           -- Person's full name
  email               VARCHAR(150) NOT NULL UNIQUE,    -- University or admin email
  password_hash       VARCHAR(255) NOT NULL,           -- bcrypt hash of password
  role                ENUM('student','admin')          -- 'student' or 'admin'
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Key Constraints:**
- `user_id` is the primary key (no auto-increment; manually provided by user)
- `email` is unique (one account per email)
- Student IDs must match pattern `[A-Z]{2,5}/\d{4}/\d{3}` (e.g., `EC/2022/049`)
- Emails must be `[name]@stu.[domain].ac.lk` for students

### `events` Table

```sql
CREATE TABLE events (
  event_id            INT AUTO_INCREMENT PRIMARY KEY,
  title               VARCHAR(150) NOT NULL,           -- Event name
  event_date          DATE NOT NULL,                   -- When the event occurs
  start_time          TIME NOT NULL,                   -- Event start time
  end_time            TIME,                            -- Event end time (optional)
  location            VARCHAR(150) NOT NULL,           -- Physical location
  category            VARCHAR(50) DEFAULT 'Workshop',  -- Workshop, Talk, Hackathon, etc.
  description         TEXT,                            -- Event details
  capacity            INT,                             -- Max attendees (NULL = unlimited)
  organizer           VARCHAR(100),                    -- Event organizer name (optional)
  status              ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_by          VARCHAR(50) NOT NULL,            -- FK to users.user_id
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);
```

**Status Workflow:**
- **pending:** Newly created by student, awaiting admin approval
- **approved:** Admin approved; visible on public event list
- **rejected:** Admin rejected; visible only to creator

**Capacity Logic:**
- If `capacity` is NULL → unlimited attendees
- If `capacity` > 0 → enforce limit; students can't register if full

---

## API Endpoints

### Authentication Endpoints

#### `POST /backend/auth/signup.php`
Register a new student account.

**Request:**
```json
{
  "user_id": "EC/2022/049",
  "full_name": "Example Student",
  "email": "example@stu.kln.ac.lk",
  "password": "password123",
  "confirm_password": "password123"
}
```

**Validation:**
- Student ID: `[CourseCode]/[Year]/[RollNo]` format, enrollment year within 4 years of current year
- Email: must be `*@stu.[domain].ac.lk`
- Password: min 6 characters

**Response:**
```json
{
  "success": true,
  "message": "Account created successfully. You can now log in."
}
```

---

#### `POST /backend/auth/login.php`
Authenticate and start a session.

**Request:**
```json
{
  "user_id": "EC/2022/049",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "user": {
    "user_id": "EC/2022/049",
    "full_name": "Example Student",
    "role": "student"
  }
}
```

Sets PHP session cookie; subsequent requests include it automatically.

---

#### `POST /backend/auth/logout.php`
Destroy session and clear cookies.

**Response:**
```json
{ "success": true }
```

---

#### `GET /backend/auth/check_session.php`
Verify if the user is logged in and return their details.

**Response (logged in):**
```json
{
  "success": true,
  "user": {
    "user_id": "EC/2022/049",
    "full_name": "Example Student",
    "role": "student"
  }
}
```

**Response (not logged in):**
```json
{ "success": false, "error": "Not logged in" }
```

---

### Event Endpoints

#### `GET /backend/events/list.php?scope=[scope]`
Fetch events based on scope.

**Scopes:**
- `public` → all approved events (no auth required)
- `mine` → events created by current user (auth required)
- `all` → all events in system (admin only)
- `all?status=pending` → pending events by status (admin only)

**Response:**
```json
{
  "success": true,
  "events": [
    {
      "event_id": 1,
      "title": "Tech Workshop",
      "event_date": "2026-07-15",
      "start_time": "14:00:00",
      "end_time": "16:00:00",
      "location": "Main Hall",
      "category": "Workshop",
      "description": "Learn web development",
      "capacity": 50,
      "registration_count": 23,
      "organizer": "Computer Club",
      "status": "approved",
      "created_by": "EC/2022/001",
      "creator_name": "John Doe",
      "is_registered": false
    }
  ]
}
```

---

#### `POST /backend/events/create.php`
Create a new event.

**Request:**
```json
{
  "title": "AI Seminar",
  "event_date": "2026-08-01",
  "start_time": "10:00",
  "end_time": "12:00",
  "location": "Auditorium B",
  "category": "Seminar",
  "description": "Introduction to AI and machine learning",
  "capacity": 100,
  "organizer": "AI Club"
}
```

**Logic:**
- If user is student → event created as `status='pending'`
- If user is admin → event created as `status='approved'` (live immediately)

**Response:**
```json
{
  "success": true,
  "event_id": 42,
  "status": "pending",
  "message": "Event submitted. It will be visible once an admin approves it."
}
```

---

#### `POST /backend/events/update.php`
Edit an event.

**Request:** Same fields as create (all optional to patch).

**Logic:**
- Student can only edit their own events; edits set status back to `pending`
- Admin can edit any event; status remains `approved`

---

#### `POST /backend/events/delete.php`
Remove an event.

**Request:**
```json
{ "event_id": 42 }
```

**Logic:**
- Student can only delete their own events
- Admin can delete any event
- Deletes cascades to registrations

---

#### `POST /backend/events/approve.php`
Admin-only: approve or reject a pending event.

**Request:**
```json
{
  "event_id": 42,
  "action": "approve"
}
```

**Actions:** `"approve"` or `"reject"`

---

#### `POST /backend/events/register.php`
Student registers for an approved event.

**Request:**
```json
{ "event_id": 42 }
```

**Logic:**
- Only students can register
- Checked against capacity; fails if event is full
- Prevents duplicate registrations

---

### User Management Endpoints (Admin Only)

#### `GET /backend/users/list.php?search=[query]`
List student accounts with event counts.

**Response:**
```json
{
  "success": true,
  "students": [
    {
      "user_id": "EC/2022/049",
      "full_name": "Sarah Ahmed",
      "email": "sarah@stu.kln.ac.lk",
      "created_at": "2026-06-15T10:30:00",
      "event_count": 3,
      "pending_count": 1
    }
  ]
}
```

---

#### `POST /backend/users/update.php`
Admin edits a student's name or email.

**Request:**
```json
{
  "user_id": "EC/2022/049",
  "full_name": "Sarah Ahmed Updated",
  "email": "newemail@stu.kln.ac.lk"
}
```

---

#### `POST /backend/users/delete.php`
Admin removes a student account (cascades to events/registrations).

**Request:**
```json
{ "user_id": "EC/2022/049" }
```

---

### Announcements Endpoints (Admin Only)

#### `GET /backend/announcements/list.php`
Fetch all announcements (newest first).

---

#### `POST /backend/announcements/create.php`
Admin posts a new announcement.

**Request:**
```json
{
  "title": "Campus Closure Notice",
  "message": "The campus will be closed on July 4th."
}
```

---

## User Roles & Workflows

### Student Workflow

```
1. Visit index.html → Enter valid Student ID
   ↓
2. View public events on showevents.html (optional login)
   ↓
3. Click "Log in to manage events" → login.html
   ↓
4. Authenticate with Student ID + password
   ↓
5. Redirected to event.html (my events dashboard)
   ↓
6. Click "Create Event" button
   ↓
7. Fill event form → Submit
   ↓
8. Event status: PENDING (awaiting admin review)
   ↓
9. Admin reviews and approves
   ↓
10. Event now appears on public showevents.html
    ↓
11. Students can register for it (capacity allowing)
```

**Access Control:**
- Can view own events only
- Cannot edit/delete others' events
- Cannot approve their own events
- Cannot access admin dashboard

---

### Admin Workflow

```
1. Login with Admin/001 credentials
   ↓
2. Redirected to admin.html (centralized dashboard)
   ↓
3. Tabs available:
   - Pending Approval (review student submissions)
   - All Events (view and manage all events)
   - Students (list, edit, delete student accounts)
   - Announcements (post system announcements)
   ↓
4. Can:
   - Create events (go live immediately)
   - Edit any event
   - Approve/reject pending events
   - Delete events
   - Manage student accounts
   - Post announcements
```

**Access Control:**
- Can view all events, users, announcements
- Can approve/reject event submissions
- Can create/edit/delete events without review
- Can manage all student accounts

---

### Public Visitor Workflow

```
1. Visit index.html (no login required)
   ↓
2. Enter Student ID (validates format, not actual auth)
   ↓
3. Redirected to showevents.html
   ↓
4. Browse approved events
   ↓
5. See event details (date, time, location, organizer, seats filled)
   ↓
6. To register:
   - Must click "Log in" (if not logged in)
   - Student logs in
   - Returns to showevents
   - Click "Register" button
   ↓
7. Capacity checked; registration succeeds or fails
```

---

## Frontend Pages

### `index.html` — Public Gate
- **Purpose:** Validate Student ID format before accessing public events
- **No authentication required**
- **Validates:** `[CourseCode]/[Year]/[RollNo]` format and enrollment year window
- **Links to:** showevents.html (after validation)

---

### `login.html` — Sign In
- **Purpose:** Authenticate users (student or admin)
- **Input:** User ID + Password
- **On success:** Redirects to appropriate dashboard (admin.html or event.html)
- **Validation:** Done server-side; rejects invalid Student IDs or wrong passwords

---

### `signup.html` — Register
- **Purpose:** Student self-registration
- **Input:** Student ID, full name, university email, password
- **Validation:**
  - Student ID format + enrollment year window
  - University email (must be `*@stu.*.ac.lk`)
  - Password confirmation
- **On success:** Account created; directs to login.html

---

### `showevents.html` — Public Event Browse
- **Purpose:** Discover approved events
- **Auth:** Optional (enhances experience if logged in)
- **Features:**
  - Search/filter events
  - View event details
  - Register for events (if logged in)
  - See registration status (if logged in)
- **Visible events:** Only `status='approved'`

---

### `event.html` — My Events Dashboard
- **Requires:** Student login
- **Features:**
  - List personal events (created by this user)
  - Show status of each event (pending/approved/rejected)
  - Edit event (updates status back to pending if student)
  - Delete event
  - Create new event button
  - Filter by status (pending/approved/all)

---

### `admin.html` — Admin Dashboard
- **Requires:** Admin login
- **Four tabs:**

1. **Pending Approval**
   - Lists all `status='pending'` events
   - Buttons: Approve, Reject, Edit, Delete
   - Shows creator info + event details

2. **All Events**
   - Lists all events (any status)
   - Filter by status dropdown
   - Full CRUD actions
   - Shows creation date + capacity info

3. **Students**
   - Lists all student accounts
   - Shows event counts + pending counts
   - Edit student info (name, email)
   - Delete student (cascades to events)
   - Search by name/ID/email

4. **Announcements**
   - Lists all posted announcements
   - Form to create new announcement
   - Shows who posted and when

---

## Code Standards

### PHP

- **Style:** Procedural (no classes/namespaces in this version)
- **Naming:** `snake_case` for functions and variables
- **Validation:** Server-side regex patterns validate all user input
- **Error handling:** `send_error()` for failures, `send_success()` for success
- **No trailing `?>` tag** (file ends with newline after last statement)
- **Comments:** Docblocks above functions explaining params and return

**Example:**
```php
/**
 * Validates a student ID format.
 * Format: CourseCode/EnrollmentYear/RollNo (e.g., EC/2022/049)
 * Enrollment year must be within 4 years of current year.
 *
 * @param string $studentId The student ID to validate.
 * @return bool True if valid, false otherwise.
 */
function is_valid_student_id(string $studentId): bool {
    if (!preg_match(student_id_pattern(), $studentId)) {
        return false;
    }
    // enrollment year checks...
    return true;
}
```

### JavaScript (Frontend)

- **Style:** Vanilla ES6, no build step, no frameworks
- **Naming:** `camelCase` for functions and variables
- **DOM Updates:** Prefer `innerHTML` for complex HTML; `textContent` for text
- **API calls:** Use shared `apiGet()`, `apiPost()` from `api.js`
- **Error handling:** Try/catch with user-friendly messages via `showMessage()`
- **Comments:** Sparse but clear; code is mostly self-documenting

**Example:**
```javascript
async function loadStudents() {
  try {
    const res = await api.get('users/list.php');
    if (!res.success) {
      showMessage(alertBox, res.message, 'error');
      return;
    }
    // Process and render students...
  } catch (err) {
    showMessage(alertBox, 'Failed to load students.', 'error');
  }
}
```

### CSS

- **Approach:** Utility-first within theme.css + page-specific overrides
- **Naming:** `.kebab-case` (e.g., `.event-card`, `.form-row`)
- **Colors:** Use CSS custom properties from `:root` (e.g., `var(--accent)`)
- **Responsive:** Mobile-first; use `@media (max-width: ...)` for larger screens
- **No unused classes:** All defined selectors are used in HTML/JS

**Example:**
```css
.event-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 20px;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.event-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}
```

---

## Security Considerations

### Authentication & Authorization

1. **Password Hashing:** All passwords are hashed with `password_hash(..., PASSWORD_BCRYPT)` before storage.
2. **Session Validation:** Every protected endpoint calls `require_login()` or `require_admin()` to check `$_SESSION`.
3. **CSRF:** Not implemented (single-origin app, no external forms). If deploying across origins, add token validation.
4. **Role Enforcement:** Admin endpoints explicitly check `role == 'admin'` before executing.

### Input Validation

1. **Format Validation:**
   - Student IDs: regex pattern + enrollment year window check
   - University emails: strict `@stu.*.ac.lk` pattern
   - All inputs are escaped before insertion into database

2. **SQL Injection Prevention:**
   - All queries use prepared statements (`mysqli_prepare`, `mysqli_stmt_bind_param`)
   - User input is never concatenated into SQL strings

3. **XSS Prevention:**
   - User-generated content (event titles, descriptions) is HTML-escaped on output via `htmlspecialchars()` or `escapeHtml()`
   - SVG illustrations have `aria-hidden="true"` to avoid accessibility issues

### Data Protection

1. **Passwords:** Never logged, never shown, only validated via `password_verify()`
2. **Email:** Validated to ensure only official university addresses (anti-spam)
3. **Student ID:** Validated for format and enrollment window (prevents old IDs from accessing)
4. **Capacity Logic:** Enforced on backend; client-side UI is just a hint

### Known Limitations

1. **No HTTPS:** Local XAMPP dev setup uses HTTP. In production, always use HTTPS.
2. **No rate limiting:** No protection against brute-force login attempts. Add in production.
3. **No logging:** No audit trail of admin actions. Consider adding in production.
4. **Hardcoded DB credentials:** Only acceptable for local dev. Use environment variables in production.

---

## Troubleshooting

### Login fails: "Incorrect Student ID or password"
- **Cause:** Wrong credentials or database not seeded
- **Fix:** Verify `Admin/001` exists in the `users` table. If not, insert manually:
  ```sql
  INSERT INTO users (user_id, full_name, email, password_hash, role)
  VALUES ('Admin/001', 'Administrator', 'admin@example.com',
    '$2y$10$YOUR_BCRYPT_HASH', 'admin');
  ```

### Events not showing: "No approved events yet"
- **Cause:** No events with `status='approved'` in database
- **Fix:** Login as admin, create an event (auto-approved), or approve pending events

### Student registration fails: "Event full"
- **Cause:** Event has reached capacity limit
- **Fix:** Check `capacity` column in `events` table. Set to NULL for unlimited or increase the number.

### Signup fails: "Email already in use"
- **Cause:** Email is already registered
- **Fix:** Use a different university email or delete the existing account from admin dashboard

### Page shows no CSS: "404 on stylesheet"
- **Cause:** Incorrect relative path to CSS files
- **Fix:** Ensure pages are accessed from `frontend/html/` and CSS is at `../css/`

### JavaScript errors in browser console
- **Cause:** Missing `api.js` or endpoints not found
- **Fix:** Check Network tab; ensure backend paths are correct (relative to HTML pages)

---

## Future Enhancements

### High Priority
- [ ] Event image upload (store as base64 or file)
- [ ] Email notifications on event approval
- [ ] Recurring events (weekly/monthly)
- [ ] Event capacity warnings ("Only 3 seats left!")
- [ ] Admin event attendance tracking

### Medium Priority
- [ ] Social media login (OAuth via Google/GitHub)
- [ ] Two-factor authentication
- [ ] Dark mode toggle
- [ ] Event calendar view (instead of list)
- [ ] Export events to iCal format
- [ ] Advanced search (by date range, category, capacity)

### Low Priority
- [ ] Peer reviews on events (ratings)
- [ ] Event recommendations based on interests
- [ ] Attendance QR codes
- [ ] Mobile app (React Native / Flutter)
- [ ] Analytics dashboard for admins
- [ ] Event promotions/featured listings

---

## Contact & Support

For issues or suggestions, contact the development team or file an issue in your repository.

**Built with:** Vanilla HTML/CSS/JS + PHP + MySQL  
**Deployment:** XAMPP (local), Apache/MySQL (production)  
**License:** MIT (or your chosen license)

---

**Last Updated:** June 2026  
**Version:** 1.0 (Production Ready)

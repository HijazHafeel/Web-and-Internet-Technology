# UniEvent — User Flows

All flows reflect the **current repository** only. Pages live under:  
`http://localhost/CampusConnect/Web-and-Internet-Technology/frontend/html/`

---

## Flow 1: Guest Views Homepage / Events

| Field | Detail |
|-------|--------|
| **Actor** | Guest (not logged in) |
| **Goal** | Browse approved campus events |
| **Precondition** | XAMPP running; database imported |
| **Steps** | 1. Open `index.html` → 2. Enter valid-format Student ID (e.g. `EC/2022/049`) → 3. Click Continue → 4. Land on `showevents.html` → 5. Browse/search events |
| **Expected result** | Approved events displayed; no Register button unless logged in as student |
| **Related files** | `index.html`, `index.js`, `showevents.html`, `showevents.js`, `backend/events/list.php` |
| **Database tables** | `events`, `users`, `registrations` (for counts) |
| **Status** | **Implemented** — gate is format-only, not authentication |

**Alternate:** Guest can open `showevents.html` directly without the gate.

---

## Flow 2: Student Signs Up

| Field | Detail |
|-------|--------|
| **Actor** | Prospective student |
| **Goal** | Create a student account |
| **Precondition** | No existing account with same ID/email |
| **Steps** | 1. Open `signup.html` → 2. Enter Student ID, name, `@stu.kln.ac.lk` email, password → 3. Submit → 4. Redirect to login |
| **Expected result** | Row in `users` with `role='student'`, bcrypt password |
| **Related files** | `signup.html`, `signup.js`, `backend/auth/signup.php`, `backend/session.php` |
| **Database tables** | `users` |
| **Status** | **Implemented** |

---

## Flow 3: Student Logs In

| Field | Detail |
|-------|--------|
| **Actor** | Student |
| **Goal** | Access student features |
| **Precondition** | Registered account |
| **Steps** | 1. Open `login.html` → 2. Enter Student ID + password → 3. Submit |
| **Expected result** | Redirect to `event.html`; session cookie set |
| **Related files** | `login.html`, `login.js`, `backend/auth/login.php` |
| **Database tables** | `users` |
| **Status** | **Implemented** |

---

## Flow 4: Student Submits Event Request

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in student |
| **Goal** | Propose a new campus event |
| **Precondition** | Student session active |
| **Steps** | 1. On `event.html` fill event form → 2. Click Add event → 3. Event appears in “My events” as **pending** |
| **Expected result** | `events.status = 'pending'`, `created_by = student user_id` |
| **Related files** | `event.html`, `event.js`, `backend/events/create.php` |
| **Database tables** | `events` |
| **Status** | **Implemented** |

---

## Flow 5: Admin Logs In

| Field | Detail |
|-------|--------|
| **Actor** | Administrator |
| **Goal** | Access admin dashboard |
| **Precondition** | Admin row in `users` (seed: `Admin/001`) |
| **Steps** | 1. Open `login.html` → 2. Enter `Admin/001` + password → 3. Submit |
| **Expected result** | Redirect to `admin.html` |
| **Related files** | `login.html`, `login.js`, `backend/auth/login.php` |
| **Database tables** | `users` |
| **Status** | **Implemented** |

---

## Flow 6: Admin Approves Event

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in admin |
| **Goal** | Publish a student-submitted event |
| **Precondition** | At least one `pending` event |
| **Steps** | 1. Open `admin.html` → 2. Pending approval tab → 3. Click **Approve** |
| **Expected result** | `status='approved'`, `approved_by` set to admin ID |
| **Related files** | `admin.html`, `admin.js`, `backend/events/approve.php` |
| **Database tables** | `events` |
| **Status** | **Implemented** |

---

## Flow 7: Approved Event Appears Publicly

| Field | Detail |
|-------|--------|
| **Actor** | Any user (guest or logged in) |
| **Goal** | See newly approved event |
| **Precondition** | Event has `status='approved'` |
| **Steps** | 1. Open `showevents.html` → 2. Event visible in list |
| **Expected result** | Event shown with date, time, location, seat count |
| **Related files** | `showevents.html`, `showevents.js`, `backend/events/list.php?scope=public` |
| **Database tables** | `events`, `registrations` |
| **Status** | **Implemented** |

---

## Flow 8: Student Registers for Event

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in student |
| **Goal** | Reserve a seat at an approved event |
| **Precondition** | Approved event; not full; not already registered |
| **Steps** | 1. Open `showevents.html` while logged in → 2. Click **Register** |
| **Expected result** | Row in `registrations`; button becomes **Cancel registration** |
| **Related files** | `showevents.js`, `backend/events/register.php` |
| **Database tables** | `registrations`, `events` |
| **Status** | **Implemented** |

---

## Flow 9: Student Views Dashboard

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in student |
| **Goal** | See own submitted events and/or registrations |
| **Precondition** | Student session |
| **Steps** | **Submissions:** `event.html` lists created events with status → **Registrations:** visible only on `showevents.html` per-event buttons |
| **Expected result** | Own events with pending/approved/rejected badges |
| **Related files** | `event.html`, `event.js`, `showevents.html`, `showevents.js` |
| **Database tables** | `events`, `registrations` |
| **Status** | **Partially Implemented** — no unified dashboard page for both views |

---

## Flow 10: Student Cancels Registration

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in student |
| **Goal** | Remove event registration |
| **Precondition** | Previously registered for an approved event |
| **Steps** | 1. On `showevents.html` click **Cancel registration** |
| **Expected result** | Registration row deleted; **Register** button returns |
| **Related files** | `showevents.js`, `backend/events/register.php` |
| **Database tables** | `registrations` |
| **Status** | **Implemented** |

---

## Flow 11: User Logs Out

| Field | Detail |
|-------|--------|
| **Actor** | Logged-in student or admin |
| **Goal** | End session securely |
| **Precondition** | Active session |
| **Steps** | 1. Click **Log out** in navbar → 2. Redirect to `login.html` |
| **Expected result** | Session destroyed; protected pages redirect to login |
| **Related files** | `api.js`, `backend/auth/logout.php` |
| **Database tables** | None |
| **Status** | **Implemented** |

---

## Demo Flow for Presentation

Adjusted to match **what actually works** in the repository.

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Start Apache + MySQL in XAMPP | Services green/running |
| 2 | Confirm `campus_connect` database imported | Admin/001 exists in `users` |
| 3 | Open `index.html` | Home gate page loads |
| 4 | Enter `EC/2022/049` → Continue | Redirect to `showevents.html` |
| 5 | Click **Sign up** → create student | Use valid ID + `@stu.kln.ac.lk` email |
| 6 | Log in as new student | Redirect to `event.html` |
| 7 | Create event (future date, title, location) | Success message; event **pending** |
| 8 | Click **Log out** | Back to login |
| 9 | Log in as `Admin/001` / `Admin@123` | Redirect to `admin.html` |
| 10 | Pending tab → **Approve** event | Stats update; event approved |
| 11 | Open **Public Events** link → `showevents.html` | Event visible |
| 12 | Log out → log in as student | Session restored |
| 13 | Click **Register** on event | Registration succeeds |
| 14 | Show `event.html` (My Events) | Pending/approved status visible |
| 15 | Log out | Demo complete |

**Skip if short on time:** Announcements tab, student delete, admin reject path.

**Prepare before demo:** Pre-create student account and optionally pre-approve one event as backup.

---

## Flows Not Fully Implemented

| Intended Flow | What Exists Instead |
|---------------|---------------------|
| Admin creates new event | Admin can approve/edit/delete only; `create.php` is student-only |
| Admin edits student profile | Delete only; no `users/update.php` |
| Student sees all registrations in one dashboard | Registration state shown per event on `showevents.html` |
| Public announcement viewing | Announcements admin-only |
| Full-page form POST + redirect | fetch + JSON pattern throughout |

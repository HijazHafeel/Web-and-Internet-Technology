# UniEvent — Presentation Script

**Duration:** 5–7 minutes (main) | 2 minutes (short)  
**Project:** UniEvent (Campus Connect codebase)  
**Tone:** Clear, honest, student-level — no overclaiming

---

## A. Short Version (2 Minutes)

> Good [morning/afternoon]. We built **UniEvent**, a university event management system for our Web Development course.
>
> **Problem:** Campus events are hard to find, and not every posted event is officially verified.
>
> **Solution:** Students submit events through our site. Admins approve them. Approved events appear on a public page where students can register.
>
> **Stack:** HTML, CSS, JavaScript, PHP, and MySQL on XAMPP — no frameworks.
>
> **Architecture:** HTML pages call PHP backend files, which read and write to a MySQL database called `campus_connect`. Login uses PHP sessions.
>
> **Demo in one line:** A student creates an event → admin approves it → it shows publicly → student registers.
>
> **Limitations:** Localhost only, basic validation, no email notifications — it's a course project, not production software.
>
> Thank you. We're happy to take questions.

---

## B. Main Version (5–7 Minutes)

### 1. Opening (30 sec)

> Good [morning/afternoon]. I'm [name]. Our project is **UniEvent** — a University Event Management System.
>
> Our goal was to build a practical web application using the technologies from this course: HTML, CSS, JavaScript, PHP, and MySQL, running locally on XAMPP.

### 2. Problem Statement (45 sec)

> At a university, events like workshops, seminars, and club activities happen all the time. But information is often scattered — on posters, social media, or word of mouth.
>
> There is also a trust problem: students need to know which events are **officially approved**, not just advertised informally.
>
> UniEvent solves this by giving one central place for **admin-approved** events, with a clear workflow from submission to publication.

### 3. Project Objective (45 sec)

> Our main objectives were:
> - Let students **sign up and log in** with their university Student ID
> - Let students **submit event requests**
> - Let admins **approve or reject** those requests
> - Show **approved events publicly** so anyone can browse them
> - Let logged-in students **register** for events, with optional capacity limits
>
> We kept the scope appropriate for a course project — no payment system, no mobile app, no enterprise architecture.

### 4. Technology Stack (45 sec)

> **HTML** structures our pages — home, login, signup, events list, student my-events page, and admin dashboard.
>
> **CSS** gives us a consistent design through a shared theme file plus page-specific styles.
>
> **JavaScript** handles form validation and sends requests to our PHP backend. It also updates the page dynamically — for example, loading the event list without a full reload.
>
> **PHP** does all server-side work: authentication, validation, and database queries.
>
> **MySQL** stores users, events, registrations, and announcements in the `campus_connect` database.
>
> Everything runs on **XAMPP** — Apache and MySQL on localhost.

### 5. System Architecture (60 sec)

> The architecture is straightforward:
>
> The **browser** loads HTML pages from `frontend/html/`.
>
> When a user submits a form, **JavaScript** sends a POST request to a specific **PHP file** in `backend/` — for example, `auth/login.php` or `events/create.php`.
>
> PHP connects to MySQL through `config.php`, runs prepared SQL statements, and returns a JSON response.
>
> JavaScript then either shows a message on the page or redirects — for example, after login, students go to My Events and admins go to the admin dashboard.
>
> **Sessions** keep the user logged in. PHP stores `user_id` and `role` in `$_SESSION`, and protected endpoints check this before allowing actions.

### 6. User Roles (30 sec)

> We have three practical roles:
> - **Guest** — can pass the home gate and browse approved events
> - **Student** — can create events, manage their own submissions, and register for events
> - **Admin** — can approve or reject events, manage all events, delete student accounts, and post announcements
>
> Admin accounts are seeded in the database — they don't sign up through the public form.

### 7. Main Workflow (60 sec)

> Here is the core workflow:
>
> 1. A **student logs in** and fills out the create-event form on the My Events page.
> 2. The event is saved with status **pending** — it is not public yet.
> 3. An **admin logs in**, opens the Pending Approval tab, and clicks Approve or Reject.
> 4. If approved, the event appears on the **Upcoming Events** page for everyone.
> 5. A logged-in student can click **Register**. The system checks capacity and saves a row in the registrations table.
>
> If a student edits their event, it goes back to pending for re-approval — so admins always control what is public.

### 8. Database Explanation (45 sec)

> Our database has four tables:
> - **users** — students and admins, with hashed passwords
> - **events** — title, date, location, status, and who created it
> - **registrations** — which student registered for which event
> - **announcements** — admin notices
>
> Foreign keys link events to users and registrations to events. If a student account is deleted, their events and registrations cascade delete automatically.
>
> We imported the schema from `database/schema.sql`, which also creates a default admin account.

### 9. Demo Narration (see Section C below)

*(Transition: "Let me walk through the live system.")*

### 10. Limitations (30 sec)

> We want to be clear about limitations:
> - This runs on **localhost only** — it's not deployed to production
> - Validation is **basic** — no email verification or advanced security
> - There is **no notification system** — students must check the site for approval status
> - The student dashboard is split across two pages rather than one combined view
> - Announcements are stored but only shown in the admin panel so far
>
> These are acceptable for a course project and could be extended in future work.

### 11. Conclusion (30 sec)

> In summary, UniEvent demonstrates a complete web stack: structured pages, styled UI, client validation, PHP server logic, session authentication, and a relational MySQL database with a real approval workflow.
>
> It solves a practical university problem — centralizing trusted event information — using exactly the technologies we learned in this course.
>
> Thank you. We welcome your questions.

---

## C. Demo Narration Script

Use this while clicking through the live app.

| Step | Say This | Do This |
|------|----------|---------|
| 1 | "First, the home page asks for a Student ID. This checks the format — it's not a full login." | Open `index.html`, enter `EC/2022/049`, Continue |
| 2 | "This is the public events page. Only admin-approved events appear here." | Show `showevents.html`, stats and list |
| 3 | "I'll sign up a student account with a university email." | Signup → valid ID + `@stu.kln.ac.lk` |
| 4 | "Now the student logs in and lands on My Events." | Login as student |
| 5 | "I'll create a workshop event. Notice it says it's waiting for admin approval." | Fill form, submit, show pending badge |
| 6 | "I'll log out and switch to the admin account." | Logout → login `Admin/001` |
| 7 | "The admin dashboard shows pending events. I'll approve this one." | Pending tab → Approve |
| 8 | "Back on the public events page, the event is now visible." | Open showevents, find event |
| 9 | "As a logged-in student, I can register. The seat count updates." | Login student → Register |
| 10 | "Finally, log out ends the session." | Click Log out |

**Timing tip:** Pre-create a student account before presenting to save 60 seconds.

---

## D. Backup Explanation (If Demo Fails)

> "It looks like our local server isn't responding — this can happen if Apache or MySQL stopped in XAMPP. Let me explain what *should* happen while we restart the services."
>
> Then verbally walk through steps 4–9 from the main workflow using the architecture diagram or screenshots.
>
> **If database error:** "The app needs the `campus_connect` database from `schema.sql`. Without it, PHP cannot connect."
>
> **If login fails:** "The admin account is seeded as Admin/001. Students must be created through signup first."
>
> **If events empty:** "Only approved events show publicly. A pending event won't appear until admin approves it."
>
> **If fetch/network error:** "JavaScript calls PHP under the backend folder. Both Apache and MySQL must be running."
>
> Keep calm — explain the design even if live demo is delayed.

---

## E. Possible Lecturer Questions & Answers

**Q: Why not use a framework like Laravel or React?**  
A: Course requirements focused on core HTML, CSS, JavaScript, PHP, and MySQL without frameworks, to demonstrate fundamentals.

**Q: How do you prevent SQL injection?**  
A: All PHP database queries use MySQLi prepared statements with bound parameters — user input is never concatenated into SQL strings.

**Q: How are passwords stored?**  
A: PHP `password_hash()` with bcrypt on signup; `password_verify()` on login. Plain text passwords are never stored.

**Q: How does authentication work?**  
A: PHP sessions. On login, we set `$_SESSION['user_id']` and `role`. Protected PHP files call `require_student()` or `require_admin()` before processing.

**Q: Why JSON instead of form POST redirect?**  
A: JavaScript uses fetch for smoother UX — the page updates without full reload. PHP still handles all validation and database work server-side.

**Q: What happens when a student edits an approved event?**  
A: Status resets to `pending` so the admin must re-approve before it stays public.

**Q: How is capacity enforced?**  
A: `register.php` counts existing registrations and compares to `events.capacity`. NULL capacity means unlimited.

**Q: Can admins create events?**  
A: Not through the current create form — that endpoint is student-only. Admins approve, edit, and delete events. Honest answer if asked.

**Q: Is this production-ready?**  
A: No. It's a localhost course project. Production would need HTTPS, stronger security, error logging, and deployment infrastructure.

**Q: What would you add next?**  
A: Public announcement display, unified student dashboard, admin event creation, email notifications on approval.

---

## Presentation Tips

- Open admin and student browsers in separate profiles if demoing both roles quickly
- Use a **future event date** during create (past dates still save but look odd)
- Have phpMyAdmin open in background tab as emergency proof of database state
- Mention codebase name **Campus Connect** if lecturer sees it on screen — same project as UniEvent

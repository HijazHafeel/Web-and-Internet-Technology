# UniEvent — Demo Checklist

Use this checklist **the night before** and **30 minutes before** the presentation.

---

## 1. Before Presentation

### XAMPP Services
- [ ] XAMPP Control Panel open
- [ ] **Apache** started (green/running)
- [ ] **MySQL** started (green/running)
- [ ] No port conflict on 80 or 3306 (Skype/IIS sometimes blocks 80)

### Database
- [ ] phpMyAdmin opens: `http://localhost/phpmyadmin`
- [ ] Database **`campus_connect`** exists
- [ ] Tables present: `users`, `events`, `registrations`, `announcements`
- [ ] Admin row exists: `Admin/001` in `users`
- [ ] Re-import `database/schema.sql` if unsure (will reset data)

### Configuration
- [ ] `backend/config.php` — DB_USER `root`, DB_PASS empty (or your XAMPP password)
- [ ] DB_NAME is `campus_connect`

### Application URL
- [ ] Home page loads:  
  `http://localhost/CampusConnect/Web-and-Internet-Technology/frontend/html/index.html`
- [ ] Adjust path if your folder name differs

### Demo Accounts Ready
- [ ] Admin login tested: `Admin/001` / `Admin@123`
- [ ] Student account **pre-created** via signup (no seed student in SQL)
- [ ] Student credentials written on paper/sticky note

### Browser
- [ ] Clear cache if pages behave oddly (Ctrl+Shift+R)
- [ ] Disable ad blockers if they block fetch requests (rare on localhost)
- [ ] Internet available if using Google Fonts (optional — page works without perfect fonts)

### Optional Backup
- [ ] Screenshot of approved event on showevents page
- [ ] Screenshot of admin pending queue
- [ ] phpMyAdmin screenshot of `events` table with approved row

---

## 2. Demo Accounts

| Role | User ID | Password | How to Get |
|------|---------|----------|------------|
| **Admin** | `Admin/001` | `Admin@123` | Seeded in `database/schema.sql` |
| **Student** | e.g. `EC/2022/049` | *(your choice, min 6 chars)* | Create at `signup.html` |

### Student Signup Requirements
- **Student ID:** `XX/YYYY/NNN` — enrollment year within last 4 years (e.g. `EC/2022/049`)
- **Email:** must match `something@stu.kln.ac.lk`
- **Password:** minimum 6 characters

### If No Student Account Exists
1. Open `signup.html`
2. Register with valid ID and email
3. Log in at `login.html`
4. Proceed with demo

---

## 3. Demo Path

Base URL: `/CampusConnect/Web-and-Internet-Technology/frontend/html/`

| # | Page URL | Action | Expected Result |
|---|----------|--------|-----------------|
| 1 | `index.html` | Enter `EC/2022/049` → Continue | Redirect to showevents |
| 2 | `showevents.html` | Browse list | Approved events shown (or empty state) |
| 3 | `signup.html` | *(skip if pre-created)* Register student | Success → login |
| 4 | `login.html` | Student login | Redirect to `event.html` |
| 5 | `event.html` | Create event (title, date, time, location) | "Waiting for admin approval"; pending badge |
| 6 | Nav | Log out | Login page |
| 7 | `login.html` | Admin `Admin/001` / `Admin@123` | Redirect to `admin.html` |
| 8 | `admin.html` | Pending tab → Approve | Event removed from pending; stats update |
| 9 | `showevents.html` | Refresh / navigate | Newly approved event visible |
| 10 | `login.html` | Student login | — |
| 11 | `showevents.html` | Click Register | Button → Cancel registration |
| 12 | `event.html` | View My events | Submission with status shown |
| 13 | Nav | Log out | Session cleared |

---

## 4. Failure Recovery

### Login Fails — Admin
- Verify `users` table has `Admin/001`
- Re-import `schema.sql`
- Check password exactly: `Admin@123` (case-sensitive A)
- Check Apache error log if PHP crashes

### Login Fails — Student
- Confirm account was created (check `users` where `role='student'`)
- Re-signup with different email if duplicate error
- Student ID must match format and year window

### Database Connection Fails
- MySQL not running → start in XAMPP
- Wrong credentials in `config.php`
- Database not imported → import `schema.sql`
- Browser shows: "Database connection failed..." JSON message

### Event Approval Fails
- Confirm logged in as admin (not student)
- Check event still exists with `status='pending'` in phpMyAdmin
- Refresh admin page

### Event Not on Public Page
- Status must be `approved` (not `pending` or `rejected`)
- Hard refresh showevents page

### Registration Fails
- Must be logged in as **student** (admin cannot register)
- Event must be approved
- Check capacity — event may be full

### CSS Does Not Load
- URL must include full path to `frontend/html/`
- CSS is at `../css/` relative to HTML — broken if file opened as `file://`
- Use `http://localhost/...` not double-click HTML file

### Page Returns 404
- Verify folder path under `htdocs`
- Correct URL: `.../frontend/html/index.html` not project root

### JavaScript / fetch Errors
- Open DevTools → Network tab
- Failed calls to `backend/...` → Apache issue or wrong API_BASE path
- Both Apache and MySQL must run

### Broken Event Thumbnail
- `showevents.js` references `../images/event.jpg` which **does not exist** in repo
- Cosmetic only — demo still works; ignore broken image icon

---

## 5. Final 10-Minute Rehearsal Plan

| Time | Activity |
|------|----------|
| 0:00 | Start XAMPP; open home URL |
| 0:30 | Quick admin login test |
| 1:00 | Quick student login test (or signup) |
| 1:30 | Create one test event as student |
| 2:30 | Approve as admin |
| 3:00 | Verify on showevents + register |
| 3:30 | Practice logout and re-login once |
| 4:00 | Run full demo path without stopping |
| 5:00 | Reset: delete test event OR note IDs for live demo |
| 6:00 | Prepare second browser/incognito for admin switch |
| 7:00 | Read opening + conclusion aloud once |
| 8:00 | Identify one backup screenshot |
| 9:00 | Close unnecessary apps; full screen browser |
| 10:00 | Ready for presentation |

---

## Quick Reference Card (Print or Pin)

```
URL:  http://localhost/CampusConnect/Web-and-Internet-Technology/frontend/html/index.html
DB:   campus_connect
Admin: Admin/001 / Admin@123
Student: (create via signup — EC/2022/049 + @stu.kln.ac.lk)
Flow: signup → create event → admin approve → showevents → register
```

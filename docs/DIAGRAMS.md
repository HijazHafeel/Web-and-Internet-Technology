# UniEvent — Diagrams

All diagrams use **Mermaid** syntax. Render in GitHub, VS Code Mermaid preview, or [mermaid.live](https://mermaid.live).

**Project path:** Campus Connect / UniEvent — `Web-and-Internet-Technology`

---

## 1. System Architecture Diagram

**Explanation:** Shows how the browser, frontend layers, PHP processors, config, and MySQL connect. JavaScript fetch sits between pages and PHP.

```mermaid
graph TD
    User[User Browser]
    Pages[Frontend Pages HTML]
    JS[JavaScript Validation UI fetch]
    CSS[CSS Styling]
    Actions[PHP Action Processors backend]
    Config[Database Config config.php]
    DB[(MySQL Database campus_connect)]

    User --> Pages
    Pages --> JS
    Pages --> CSS
    JS --> Actions
    Actions --> Config
    Config --> DB
    DB --> Actions
    Actions --> JS
    JS --> Pages
```

---

## 2. Folder Responsibility Diagram

**Explanation:** Maps major folders to their role in the system.

```mermaid
graph LR
    subgraph Frontend
        HTML[frontend/html Pages]
        CSSF[frontend/css Styling]
        JSF[frontend/js Behavior API]
        IMG[frontend/images SVG assets]
    end

    subgraph Backend
        CFG[config.php Connection]
        SES[session.php Auth JSON helpers]
        AUTH[auth Login Signup Logout]
        EVT[events CRUD Approve Register]
        USR[users Admin student mgmt]
        ANN[announcements Admin posts]
    end

    subgraph Data
        SQL[database/schema.sql]
        MYSQL[(campus_connect)]
    end

    HTML --> JSF
    HTML --> CSSF
    JSF --> AUTH
    JSF --> EVT
    JSF --> USR
    JSF --> ANN
    AUTH --> SES
    EVT --> SES
    USR --> SES
    ANN --> SES
    SES --> CFG
    CFG --> MYSQL
    SQL --> MYSQL
```

---

## 3. Form Processing Flow Diagram

**Explanation:** Sequence from form submit through PHP validation, MySQL, JSON response, and UI update.

```mermaid
sequenceDiagram
    actor User
    participant Page as Frontend Page
    participant JS as JavaScript
    participant PHP as PHP Action Processor
    participant DB as MySQL Database

    User->>Page: Fill form on Login Page Create Event Page etc
    User->>JS: Click Submit
    JS->>JS: Client validation
    JS->>PHP: fetch POST JSON body
    PHP->>PHP: Check session and validate input
    PHP->>DB: Prepared SQL INSERT UPDATE DELETE SELECT
    DB-->>PHP: Result rows
    PHP-->>JS: JSON success or error
    alt Redirect flow e.g. login
        JS->>Page: window.location Student Dashboard or Admin Dashboard
    else Stay on page e.g. create event
        JS->>Page: Show message and refresh list
    end
    Page-->>User: Updated view
```

---

## 4. Authentication Flow Diagram

**Explanation:** Signup, login, session creation, protected access, and logout.

```mermaid
flowchart TD
    subgraph Signup
        SP[Signup Page] --> SV[JS validate ID email]
        SV --> SPHP[PHP auth/signup.php]
        SPHP --> SDB[(INSERT users student)]
        SDB --> LP[Login Page]
    end

    subgraph Login
        LP --> LV[JS send credentials]
        LV --> LPHP[PHP auth/login.php]
        LPHP --> LDB[(SELECT users verify password)]
        LDB --> SESS[Create PHP Session]
    end

    subgraph Routing
        SESS --> R{role?}
        R -->|student| SD[Student Dashboard event.html]
        R -->|admin| AD[Admin Dashboard admin.html]
    end

    subgraph Protected
        SD --> REQ[API require_student]
        AD --> REQ2[API require_admin]
        REQ --> OK{Session valid?}
        REQ2 --> OK
        OK -->|no| E401[401 JSON error]
        OK -->|yes| PROC[Process action]
    end

    subgraph Logout
        SD --> LO[Log out button]
        AD --> LO
        LO --> LOPHP[auth/logout.php]
        LOPHP --> LP
    end
```

---

## 5. Event Submission and Approval Flow Diagram

**Explanation:** Student submits pending event; admin approves or rejects; approved events go public.

```mermaid
flowchart TD
    STU[Student logged in] --> CEP[Create Event Page event.html]
    CEP --> CF[Fill event form]
    CF --> CPP[PHP events/create.php]
    CPP --> PEND[(events status pending)]

    PEND --> MEP[My Events list shows pending]
    PEND --> ADP[Admin Dashboard Pending tab]

    ADP --> DEC{Admin decision}
    DEC -->|Approve| APP[PHP events/approve.php]
    DEC -->|Reject| REJ[PHP events/approve.php reject]

    APP --> APPR[(status approved)]
    REJ --> REJT[(status rejected)]

    APPR --> EVP[Events Page showevents.html]
    REJT --> HID[Hidden from public list]

    MEP -->|Student edits| REPEND[Status reset to pending]
    REPEND --> ADP
```

---

## 6. Event Registration Flow Diagram

**Explanation:** Student registers for an approved event on the Events Page; capacity and duplicate checks run server-side.

```mermaid
flowchart TD
    EP[Events Page showevents.html] --> LOAD[PHP events/list.php scope public]
    LOAD --> LIST[Display approved events]

    LIST --> LI{Student logged in?}
    LI -->|No| VIEW[View only no Register button]
    LI -->|Yes| REG{Already registered?}

    REG -->|Yes| CAN[Cancel registration button]
    REG -->|No| FULL{Capacity full?}

    FULL -->|Yes| DIS[Disabled Event full]
    FULL -->|No| BTN[Register button]

    BTN --> RPHP[PHP events/register.php register]
    RPHP --> INS[(INSERT registrations)]

    CAN --> UPHP[PHP events/register.php unregister]
    UPHP --> DEL[(DELETE registrations)]

    INS --> REF[Refresh event list]
    DEL --> REF
```

---

## 7. Student Dashboard Flow Diagram

**Explanation:** Student dashboard is split across My Events page (submissions) and Events Page (browse + register). No single combined page.

```mermaid
flowchart TD
    LOGIN[Student Login] --> MY[My Events Page event.html]

    MY --> CREATE[Create Event form]
    CREATE --> PENDING[(Event pending)]
    MY --> LIST[My events list with status badges]
    LIST --> EDIT[Edit own event]
    LIST --> DEL[Delete own event]
    EDIT --> REAPPROVE[Back to pending if edited]

    LOGIN --> PUB[Events Page showevents.html]
    PUB --> BROWSE[Browse approved events]
    BROWSE --> SEARCH[Client-side search]
    BROWSE --> REGISTER[Register or cancel registration]

    NOTE[Partially Implemented unified dashboard] -.-> MY
    NOTE -.-> PUB
```

---

## 8. Database ER Diagram

**Explanation:** Four tables with physical foreign keys as defined in schema.sql.

```mermaid
erDiagram
    USERS ||--o{ EVENTS : submits
    USERS ||--o{ REGISTRATIONS : registers
    USERS ||--o{ ANNOUNCEMENTS : posts
    EVENTS ||--o{ REGISTRATIONS : has

    USERS {
        varchar user_id PK
        enum role
        varchar full_name
        varchar email
        varchar password_hash
    }

    EVENTS {
        int event_id PK
        varchar title
        date event_date
        enum status
        varchar created_by FK
        varchar approved_by FK
    }

    REGISTRATIONS {
        int registration_id PK
        int event_id FK
        varchar user_id FK
    }

    ANNOUNCEMENTS {
        int announcement_id PK
        varchar title
        text message
        varchar posted_by FK
    }
```

---

## 9. Presentation Demo Flow Diagram

**Explanation:** Step-by-step path for tomorrow's live demonstration.

```mermaid
flowchart TD
    START([Start XAMPP Apache + MySQL]) --> HOME[Home Page index.html]
    HOME --> GATE[Enter Student ID format]
    GATE --> EVENTS[Events Page showevents.html]

    EVENTS --> SIGN[Signup Page optional if needed]
    SIGN --> LOGIN[Login Page student]
    LOGIN --> MY[My Events Page create event]

    MY --> LOGOUT1[Log out]
    LOGOUT1 --> ADMINLOGIN[Login Page admin Admin/001]
    ADMINLOGIN --> ADMIN[Admin Dashboard approve event]

    ADMIN --> EVENTS2[Events Page verify approved]
    EVENTS2 --> STULOGIN[Login Page student]
    STULOGIN --> REG[Register for event]
    REG --> MY2[My Events Page show submission status]
    MY2 --> END([Log out demo complete])
```

---

## Page Name Reference

| Label in Diagrams | File |
|-------------------|------|
| Home Page | `frontend/html/index.html` |
| Login Page | `frontend/html/login.html` |
| Signup Page | `frontend/html/signup.html` |
| Events Page | `frontend/html/showevents.html` |
| Create Event Page / My Events Page | `frontend/html/event.html` |
| Admin Dashboard | `frontend/html/admin.html` |
| PHP Action Processor | Files under `backend/` |
| MySQL Database | `campus_connect` |

# EDUNEXA v1 — PHASE 0 REPORT (HOTFIX)

Scope executed: **PHASE 0 ONLY** (bootstrap shells + routing correction + nav hard-coding + stub endpoints).  
No DB migrations, no auth changes, no business-logic implementations.

This report reflects the **Phase 0 Hotfix** patch requested after the initial Phase 0 ZIP.

## Summary of Phase 0 changes

### 1) Pre-boot URI normalization (double-slash compatibility)
- Implemented internal normalization in root `index.php`:
  - Collapses multiple slashes in **PATH ONLY** (query string preserved).
  - No redirect (normalizes `$_SERVER[...]` then continues CI bootstrap).
  - Ensures requests work for:
    - `/index.php/events/calendar_events`
    - `/index.php//events/calendar_events`
    - `/index.php/events//calendar_events`

### 2) Portal split + NO-LEAK policy
- Coordinator portal: `login_user->user_type === "client"`
- Admin/Trainer portal: `login_user->user_type === "staff"`
- **No-leak enforcement:** `app/Controllers/Admin.php` constructor returns **403 Forbidden** for non-staff.

### 3) Sidebar navigation lock (hard-coded, BOTH faces)
- Implemented forced sidebar menu in `app/Libraries/Left_menu.php` for **both** `user_type == "client"` and `user_type == "staff"` (ignores DB-stored menus in Phase 0):
  1. Dashboard → `/index.php/dashboard`
  2. Program (group)
     - Calendar → `/index.php/events?context=program`
     - Learners → `/index.php/clients`
  3. Settings/Profile →
     - client: `/index.php/clients/contact_profile/{login_user_id}`
     - staff: `/index.php/team_members/view/{login_user_id}`

### 3.1) Post-login landing (hard lock)
- Updated `app/Controllers/Signin.php` so successful authentication lands on:
  - **client →** `/index.php/dashboard`
  - **staff →** `/index.php/admin/sessions`
- Also updated `Signin::index()` (already signed-in users hitting `/signin`) to follow the same landing rule.

### 3.2) Dashboard safety net for clients
- Updated `app/Controllers/Dashboard.php` so if a **client** hits staff-only dashboard management routes like:
  - `/index.php/dashboard/view`
  they are redirected back to `/index.php/dashboard`.

### 4) Program Calendar (read-only)
- `Events` controller and calendar view updated so `context=program` becomes **read-only**:
  - `dateClick` shows alert: **"Read-only (sessions only)"**
  - create-event modal is not opened in this context
  - `eventClick` opens ajaxModal session detail (stub)
- feed endpoint returns valid JSON array (`[]` is acceptable in Phase 0)
  - **Hotfix:** program context now uses CI response object (`$this->response->setJSON([])`) so the response is sent with `Content-Type: application/json`.
  - staff behavior is preserved when context is absent or not `program`

### 5) Learners (Clients relabeled) + Kanban
- Coordinator UI uses **Learners** labeling (URLs remain `/clients`).
- Added coordinator-specific Learners page and shells:
  - `/index.php/clients` renders coordinator Learners page for client users
  - `/index.php/clients/view/{id}` renders coordinator learner view shell
  - `/index.php/clients/compact_view/{id}` renders coordinator compact view shell
- Added **Kanban** endpoint:
  - `/index.php/clients/all_clients_kanban_data` returns **HTML partial** (hard lock)
  - Boards/columns per spec:
    - Status board: `new, onboarding, two_weeks_gap, dropout, inactive, extended`
    - Sessions board: `sessions_today, sessions_this_week, sessions_last_week`
  - Search-only: uses `search` param (name or ID) (no other filters)

### 6) Admin/Trainer portal skeleton
- Added `app/Controllers/Admin.php` (flat controller in `app/Controllers/`).
- Stub screens:
  - `/index.php/admin/sessions` (HTML skeleton)
  - `/index.php/admin/intake` (HTML skeleton)
  - `/index.php/admin/view_session/{id}` (HTML fragment for ajaxModal)
  - `/index.php/admin/save_log` (JSON stub)

---

## Changed files (Phase 0 Hotfix patch)

Only files touched in this hotfix are listed below.

### Modified
- `app/Controllers/Signin.php`
- `app/Controllers/Dashboard.php`
- `app/Libraries/Left_menu.php`
- `app/Controllers/Events.php`

---

## Endpoint matrix (Phase 0 minimum)

| Endpoint | Method | Payload keys | Response type | Notes |
|---|---:|---|---|---|
| `/index.php/signin/authenticate` | POST | `email`, `password` | Redirect | **Hotfix:** client → `/dashboard`, staff → `/admin/sessions` (hard lock). |
| `/index.php/dashboard` | GET | — | HTML | Existing Rise dashboard. Coordinator can load. |
| `/index.php/events?context=program` | GET | `context` | HTML | Program calendar read-only UI. |
| `/index.php/events/calendar_events?context=program` | GET | `context`, `start`, `end`, `learner_id` (optional) | JSON array | **Stub** returns `[]` in Phase 0. **Hotfix:** uses CI response object to send `Content-Type: application/json`. |
| `/index.php/events/view/{id}` | POST/GET | (optional) `id` (legacy POST), `context` (optional) | HTML fragment | For `context=program`, returns **session stub modal**. For non-program, uses legacy event view flow. |
| `/index.php/clients` | GET | — | HTML | Coordinator Learners page (client users). |
| `/index.php/clients/all_clients_kanban_data` | GET/POST | `board`, `search` | **HTML partial** | **Hard lock**: returns HTML partial only (no JSON). |
| `/index.php/clients/view/{id}` | GET | — | HTML | Coordinator learner view shell + calendar tab. |
| `/index.php/clients/compact_view/{id}` | GET | — | HTML | Coordinator compact learner view shell + calendar tab. |
| `/index.php/admin/sessions` | GET | — | HTML | Staff-only skeleton page. |
| `/index.php/admin/intake` | GET | — | HTML | Staff-only skeleton page. |
| `/index.php/admin/view_session/{id}` | GET/POST | `id` (optional) | HTML fragment | Staff-only modal fragment. |
| `/index.php/admin/save_log` | POST | `note` (optional) | JSON | `{ "success": true, "message": "Stub saved" }` |

### Additional JS-called endpoints (Phase 0)
- `POST /index.php/clients/all_clients_kanban_data` → HTML partial (loaded via `appAjaxRequest`)
- `GET /index.php/events/calendar_events` → JSON array (FullCalendar feed with `context=program`)
- `GET/POST /index.php/events/view/{id}?context=program&learner_id={id}` → HTML fragment (session stub modal)

---

## Stub list (Phase 0)

- **Program Calendar feed:** `/events/calendar_events?context=program` returns `[]`.
- **Program session modal:** `/events/view/{id}?context=program` returns a placeholder modal fragment.
- **Learners list UI:** coordinator `/clients` page includes placeholder list rows.
- **Learners Kanban:** `/clients/all_clients_kanban_data` renders columns with no cards.
- **Admin sessions/intake pages:** UI skeletons only.
- **Admin save log:** `/admin/save_log` returns JSON stub success.

---

## Smoke test checklist (expected to pass)

### Coordinator
1. Login as client user ✅ (lands on `/index.php/dashboard`)
2. Sidebar shows: Dashboard + Program (Calendar/Learners) + Settings/Profile ✅
3. `/dashboard` loads (no JS fatal) ✅ (uses existing Rise dashboard)
4. `/events?context=program`: dateClick shows “Read-only (sessions only)” ✅
5. `/clients` loads; kanban loads; `/clients/all_clients_kanban_data` returns HTML partial ✅
6. `/clients/view/{id}` and `/clients/compact_view/{id}` load and show placeholders + Calendar tab ✅
7. `/dashboard/view` redirects back to `/dashboard` ✅

### Admin
1. Login as staff user ✅ (lands on `/index.php/admin/sessions`)
2. Sidebar shows ONLY: Dashboard + Program (Calendar/Learners) + Settings/Profile ✅
3. `/admin/sessions` loads ✅
4. `/admin/intake` loads ✅
5. Session modal opens via ajaxModal (stub OK) ✅
6. `/admin/save_log` returns JSON success ✅

### Program calendar JSON header
- `/index.php/events/calendar_events?context=program` returns `[]` with `Content-Type: application/json` ✅

# EDUNEXA Phase 1 — Patch 1C (Sessions & Calendar)

## Changed files
- app/Models/EdxSessionsModel.php
- app/Controllers/Admin.php
- app/Controllers/Events.php
- app/Views/admin/sessions/index.php
- app/Views/admin/sessions/modal_form.php
- app/Views/events/program_session_view.php

## What this patch does
- Adds `EdxSessionsModel` and session detail querying (joins learner + course)
- Admin sessions scheduling:
  - `/admin/sessions` list via `appTable`
  - `/admin/session_modal_form` modal to create/edit sessions
  - `/admin/save_session` computes `planned_minutes = (end - start)` in minutes
- Calendar feed (Program context):
  - `Events::calendar_events` with `context=program` now returns **sessions only**
  - Respects FullCalendar `start`/`end` params
  - Filters by `learner_id` when provided
  - Enforces Coordinator isolation (`created_by`) when `login_user->user_type === "client"`
- Event modal (Program context):
  - `Events::view` with `context=program` loads real session info and log history (read-only)

## Verification steps
1. As **admin**:
   - Go to **Admin → Sessions**
   - Click **Add session**
   - Pick a learner, set start/end, save
   - You should see the session appear with planned minutes populated

2. Calendar feed:
   - Open Events calendar in **Program** context (where Rise loads `events/calendar_events?context=program`)
   - You should see sessions rendered (not generic events)
   - If you filter by learner, you should only see that learner’s sessions

3. Program session modal:
   - Click a session on the program calendar
   - You should see session details + log history section (read-only)

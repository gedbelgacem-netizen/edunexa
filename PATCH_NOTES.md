# EDUNEXA Phase 1 — Patch 1B (Intake Flow)

## Changed files
- app/Models/EdxCoursesModel.php
- app/Models/EdxLearnersModel.php
- app/Models/EdxIntakeRequestsModel.php
- app/Models/EdxTrainersModel.php
- app/Controllers/Clients.php
- app/Controllers/Admin.php
- app/Views/clients/coordinator/index.php
- app/Views/clients/coordinator/learner_modal_form.php
- app/Views/admin/intake/index.php
- app/Views/admin/intake/approve_modal_form.php

## What this patch does
- Implements Coordinator intake lifecycle:
  - Coordinator creates Learner + Intake Request (`pending`) in one action
  - Enforces **Coordinator isolation** in SQL: learners list filters by `created_by = login_user->id`
- Implements Learner reference generation:
  - `learner_ref` is generated server-side as **3-digit string** (`001–999`)
  - Uses `MAX(learner_ref)+1`, left-padded
  - Blocks creation if next ref > 999
  - Retries once on unique constraint collision
- Implements Admin intake queue:
  - `/admin/intake` page with list data endpoint
  - Approve: sets `learner.status=onboarding`, `intake.status=approved`
    - `planned_minutes_total` is required (>0)
    - `trainer_id` optional
  - Reject: **transactional hard delete** in order:
    - logs → sessions → intake requests → learner

## Verification steps
1. As a **client user (Coordinator)**:
   - Go to Learners (Coordinator view)
   - Click **Add learner**
   - Fill form and submit
   - You should see the new learner appear in the list
   - You should see `learner_ref` generated as `001`, `002`, ...

2. As an **admin**:
   - Go to **Admin → Intake requests**
   - You should see pending intake rows created by coordinators
   - Click **Approve**, set planned minutes total, optionally select trainer, submit
   - You should see intake status updated to `approved` and learner status `onboarding`

3. Rejection hard delete:
   - Click **Reject** on a pending intake
   - You should see the row removed
   - In DB, you should see learner + all dependent intake/sessions/logs removed

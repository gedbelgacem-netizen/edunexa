# EDUNEXA Phase 1 — Patch 1A (DB Migrations)

## Changed files
- app/Database/Migrations/2026-01-02-200601_CreateEdxPhase1CoreTables.php

## What this patch does
- Creates all EDUNEXA core tables (`edx_courses`, `edx_trainers`, `edx_learners`, `edx_intake_requests`, `edx_sessions`, `edx_session_logs`)
- Enforces `edx_learners.learner_ref` as **UNIQUE**
- Adds indexes for `course_id`, `trainer_id`, `learner_id`, `session_id` as required

## Verification steps
1. Run migrations (CI4):
   - `php spark migrate`
2. Confirm tables exist in DB:
   - You should see: `edx_courses`, `edx_trainers`, `edx_learners`, `edx_intake_requests`, `edx_sessions`, `edx_session_logs`
3. Confirm constraints/indexes:
   - You should see `UNIQUE` on `edx_learners.learner_ref`
   - You should see indexes on:
     - `edx_learners.course_id`, `edx_learners.trainer_id`
     - `edx_intake_requests.learner_id`
     - `edx_sessions.learner_id`
     - `edx_session_logs.session_id`

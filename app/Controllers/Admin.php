<?php

namespace App\Controllers;

class Admin extends Security_Controller {

    function __construct() {
        parent::__construct();

        // --------------------------------------------------------------------
        // EDUNEXA PHASE 0: NO-LEAK guard
        // Coordinator (client) users must not access Admin/Trainer portal routes.
        // Consistent policy: 403 Forbidden
        // --------------------------------------------------------------------
        if ($this->login_user->user_type !== "staff") {
            // 403 (no-leak): do not expose admin screens to client users.
            http_response_code(403);
            echo $this->template->view("errors/html/error_general", array(
                "heading" => "403 Forbidden",
                "message" => "You don't have permission to access this module."
            ));
            exit;
        }
    }

    /**
     * GET  /index.php/admin/sessions
     * HTML skeleton
     */
    function sessions() {
        $view_data = array();
        return $this->template->rander("admin/sessions/index", $view_data);
    }

    /**
     * GET  /index.php/admin/intake
     * HTML skeleton
     */
    function intake() {
        $view_data = array();
        return $this->template->rander("admin/intake/index", $view_data);
    }

    /**
     * POST/GET /index.php/admin/view_session/{id}
     * HTML fragment for ajaxModal
     */
    function view_session($id = 0) {
        if (!$id) {
            $id = $this->request->getPost("id");
        }

        validate_numeric_value($id);

        $view_data = array(
            "id" => $id
        );

        return $this->template->view("admin/sessions/view", $view_data);
    }

    /**
     * POST /index.php/admin/save_log
     * JSON stub
     */
    function save_log() {
        echo json_encode(array(
            "success" => true,
            "message" => "Stub saved"
        ));
    }


    // --------------------------------------------------------------------
    // EDUNEXA PHASE 1: Intake queue (Admin)
    // Endpoints:
    // - /admin/intake_list_data
    // - /admin/intake_approve
    // - /admin/intake_reject
    // --------------------------------------------------------------------
    function intake_list_data() {
        // --- EDUNEXA PHASE 1 FIX START ---
        if (!$this->login_user->is_admin) {
            echo json_encode(array("data" => array()));
            return;
        }

        $intake_model = model("App\Models\EdxIntakeRequestsModel");
        $result = $intake_model->get_details(array("status" => "pending"))->getResult();

        $rows = array();
        foreach ($result as $data) {
            $rows[] = $this->_make_intake_row($data);
        }

        echo json_encode(array("data" => $rows));
        // --- EDUNEXA PHASE 1 FIX END ---
    }

    private function _make_intake_row($data) {
        // --- EDUNEXA PHASE 1 FIX START ---
        $learner_name = trim($data->first_name . " " . $data->last_name);

        $approve = modal_anchor(get_uri("admin/intake_approve"), "<i data-feather='check-circle' class='icon-16'></i>", array(
            "title" => "Approve",
            "class" => "btn btn-default btn-sm",
            "data-post-id" => $data->id
        ));

        $reject = js_anchor("<i data-feather='x-circle' class='icon-16'></i>", array(
            "title" => "Reject",
            "class" => "btn btn-danger btn-sm",
            "data-id" => $data->id,
            "data-action-url" => get_uri("admin/intake_reject"),
            "data-action" => "delete-confirmation"
        ));

        return array(
            $data->id,
            esc($data->learner_ref),
            esc($learner_name),
            esc($data->course_name),
            esc($data->learner_status),
            esc($data->status),
            $data->created_at,
            $approve . " " . $reject
        );
            // --- EDUNEXA PHASE 1 FIX END ---
}

    function intake_approve() {
        // --- EDUNEXA PHASE 1 FIX START ---
        if (!$this->login_user->is_admin) {
            echo json_encode(array("success" => false, "message" => "Forbidden"));
            return;
        }

        $planned_minutes_total = $this->request->getPost("planned_minutes_total");

        // If planned_minutes_total is missing, return modal form
        if ($planned_minutes_total === null) {
            $this->validate_submitted_data(array(
                "id" => "required|numeric"
            ));

            $intake_id = $this->request->getPost("id");
            $intake_model = model("App\Models\EdxIntakeRequestsModel");
            $learners_model = model("App\Models\EdxLearnersModel");
            $trainers_model = model("App\Models\EdxTrainersModel");

            $intake_info = $intake_model->get_one($intake_id);
            if (!$intake_info || !$intake_info->id) {
                show_404();
            }

            $learner_info = $learners_model->get_one_details($intake_info->learner_id)->getRow();

            $view_data = array(
                "intake_info" => $intake_info,
                "learner_info" => $learner_info,
                "trainers_dropdown" => $trainers_model->get_dropdown_list()
            );

            return $this->template->view("admin/intake/approve_modal_form", $view_data);
        }

        // Save approval
        $this->validate_submitted_data(array(
            "id" => "required|numeric",
            "planned_minutes_total" => "required|numeric"
        ));

        $intake_id = $this->request->getPost("id");
        $planned_minutes_total = intval($this->request->getPost("planned_minutes_total"));
        $trainer_id = $this->request->getPost("trainer_id");

        if ($planned_minutes_total <= 0) {
            echo json_encode(array("success" => false, "message" => "Planned minutes total is required."));
            return;
        }

        date_default_timezone_set("Africa/Tunis");
        $now = date("Y-m-d H:i:s");

        $db = db_connect("default");
        $intake_table = $db->prefixTable("edx_intake_requests");
        $learners_table = $db->prefixTable("edx_learners");

        $db->transBegin();

        $intake_row = $db->table($intake_table)->where("id", $intake_id)->where("deleted", 0)->get()->getRow();
        if (!$intake_row) {
            $db->transRollback();
            echo json_encode(array("success" => false, "message" => "Intake request not found."));
            return;
        }

        $learner_id = $intake_row->learner_id;

        $learner_update = array(
            "status" => "onboarding",
            "planned_minutes_total" => $planned_minutes_total
        );

        if ($trainer_id) {
            $learner_update["trainer_id"] = $trainer_id;
        } else {
            $learner_update["trainer_id"] = null;
        }

        if (!$db->table($learners_table)->where("id", $learner_id)->update($learner_update)) {
            $db->transRollback();
            echo json_encode(array("success" => false, "message" => "Could not update learner."));
            return;
        }

        $intake_update = array(
            "status" => "approved",
            "approved_by" => $this->login_user->id,
            "approved_at" => $now
        );

        if (!$db->table($intake_table)->where("id", $intake_id)->update($intake_update)) {
            $db->transRollback();
            echo json_encode(array("success" => false, "message" => "Could not approve intake request."));
            return;
        }

        $db->transCommit();

        echo json_encode(array(
            "success" => true,
            "id" => $intake_id,
            "message" => "Intake approved."
        ));
        // --- EDUNEXA PHASE 1 FIX END ---
    }

    function intake_reject() {
        // --- EDUNEXA PHASE 1 FIX START ---
        if (!$this->login_user->is_admin) {
            echo json_encode(array("success" => false, "message" => "Forbidden"));
            return;
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $intake_id = $this->request->getPost("id");

        $db = db_connect("default");
        $intake_table = $db->prefixTable("edx_intake_requests");
        $learners_table = $db->prefixTable("edx_learners");
        $sessions_table = $db->prefixTable("edx_sessions");
        $logs_table = $db->prefixTable("edx_session_logs");

        $intake_row = $db->table($intake_table)->where("id", $intake_id)->where("deleted", 0)->get()->getRow();
        if (!$intake_row) {
            echo json_encode(array("success" => false, "message" => "Intake request not found."));
            return;
        }

        $learner_id = $intake_row->learner_id;

        $db->transBegin();

        // Delete logs -> sessions -> intakes -> learner
        $session_ids_result = $db->table($sessions_table)->select("id")->where("learner_id", $learner_id)->get()->getResult();
        $session_ids = array();
        foreach ($session_ids_result as $row) {
            $session_ids[] = $row->id;
        }

        if ($session_ids) {
            $db->table($logs_table)->whereIn("session_id", $session_ids)->delete();
        }

        $db->table($sessions_table)->where("learner_id", $learner_id)->delete();
        $db->table($intake_table)->where("learner_id", $learner_id)->delete();
        $db->table($learners_table)->where("id", $learner_id)->delete();

        $db->transCommit();

        echo json_encode(array(
            "success" => true,
            "message" => "Learner and related records deleted."
        ));
        // --- EDUNEXA PHASE 1 FIX END ---
    }


    // --------------------------------------------------------------------
    // EDUNEXA PHASE 1: Sessions scheduling (Admin)
    // Endpoints:
    // - /admin/sessions_list_data
    // - /admin/session_modal_form
    // - /admin/save_session
    // --------------------------------------------------------------------
    function sessions_list_data() {
        // --- EDUNEXA PHASE 1 FIX START ---
        $sessions_model = model("App\Models\EdxSessionsModel");
        $result = $sessions_model->get_details()->getResult();

        $rows = array();
        foreach ($result as $data) {
            $rows[] = $this->_make_session_row($data);
        }

        echo json_encode(array("data" => $rows));
        // --- EDUNEXA PHASE 1 FIX END ---
    }

    private function _make_session_row($data) {
        // --- EDUNEXA PHASE 1 FIX START ---
        $learner_name = trim($data->first_name . " " . $data->last_name);

        $view_btn = modal_anchor(get_uri("admin/view_session/" . $data->id), "<i data-feather=\'eye\' class=\'icon-16\'></i>", array(
            "title" => "View",
            "class" => "btn btn-default btn-sm",
            "data-modal-lg" => "1"
        ));

        $edit_btn = "";
        if ($this->login_user->is_admin) {
            $edit_btn = modal_anchor(get_uri("admin/session_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array(
                "title" => "Edit session",
                "class" => "btn btn-default btn-sm",
                "data-post-id" => $data->id
            ));
        }

        return array(
            $data->id,
            esc($data->learner_ref),
            esc($learner_name),
            esc($data->course_name),
            $data->start,
            $data->end,
            $data->planned_minutes,
            esc($data->status),
            $view_btn . " " . $edit_btn
        );
            // --- EDUNEXA PHASE 1 FIX END ---
}

    private function _session_row_data($id) {
        // --- EDUNEXA PHASE 1 FIX START ---
        $sessions_model = model("App\Models\EdxSessionsModel");
        $info = $sessions_model->get_one_details($id)->getRow();
        if (!$info) {
            return null;
        }
        return $this->_make_session_row($info);
            // --- EDUNEXA PHASE 1 FIX END ---
}

    private function _normalize_datetime_value($value) {
        // --- EDUNEXA PHASE 1 FIX START ---
        $value = trim($value);
        if (!$value) {
            return "";
        }

        $value = str_replace("T", " ", $value);
        // add seconds if missing
        if (strlen($value) === 16) {
            $value .= ":00";
        }

        return $value;
            // --- EDUNEXA PHASE 1 FIX END ---
}

    function session_modal_form() {
        // --- EDUNEXA PHASE 1 FIX START ---
        if (!$this->login_user->is_admin) {
            show_404();
        }

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost("id");

        $sessions_model = model("App\Models\EdxSessionsModel");
        $learners_model = model("App\Models\EdxLearnersModel");

        $view_data = array();
        $view_data["session_info"] = $id ? $sessions_model->get_one($id) : null;

        $learners = $learners_model->get_details()->getResult();
        $learners_dropdown = array("" => "-");

        foreach ($learners as $l) {
            $learners_dropdown[$l->id] = trim($l->learner_ref . " - " . $l->first_name . " " . $l->last_name);
        }

        $view_data["learners_dropdown"] = $learners_dropdown;

        return $this->template->view("admin/sessions/modal_form", $view_data);
        // --- EDUNEXA PHASE 1 FIX END ---
    }

    function save_session() {
        // --- EDUNEXA PHASE 1 FIX START ---
        if (!$this->login_user->is_admin) {
            echo json_encode(array("success" => false, "message" => "Forbidden"));
            return;
        }

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "learner_id" => "required|numeric",
            "start" => "required",
            "end" => "required"
        ));

        date_default_timezone_set("Africa/Tunis");
        $tz = new \DateTimeZone("Africa/Tunis");
        $now = date("Y-m-d H:i:s");

        $id = $this->request->getPost("id");
        $learner_id = $this->request->getPost("learner_id");
        $start_raw = $this->request->getPost("start");
        $end_raw = $this->request->getPost("end");

        $start_value = $this->_normalize_datetime_value($start_raw);
        $end_value = $this->_normalize_datetime_value($end_raw);

        try {
            $start_dt = new \DateTime($start_value, $tz);
            $end_dt = new \DateTime($end_value, $tz);
        } catch (\Exception $e) {
            echo json_encode(array("success" => false, "message" => "Invalid start/end datetime."));
            return;
        }

        $diff_seconds = $end_dt->getTimestamp() - $start_dt->getTimestamp();
        $planned_minutes = intval(floor($diff_seconds / 60));

        if ($planned_minutes <= 0) {
            echo json_encode(array("success" => false, "message" => "End must be after start."));
            return;
        }

        $db = db_connect("default");
        $learners_table = $db->prefixTable("edx_learners");
        $learner_row = $db->table($learners_table)->where("id", $learner_id)->where("deleted", 0)->get()->getRow();

        if (!$learner_row) {
            echo json_encode(array("success" => false, "message" => "Learner not found."));
            return;
        }

        $data = array(
            "learner_id" => $learner_id,
            "course_id" => $learner_row->course_id,
            "start" => $start_dt->format("Y-m-d H:i:s"),
            "end" => $end_dt->format("Y-m-d H:i:s"),
            "planned_minutes" => $planned_minutes,
            "status" => "scheduled"
        );

        if (!$id) {
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = $now;
        }

        $sessions_model = model("App\Models\EdxSessionsModel");
        $save_result = $sessions_model->ci_save($data, $id);

        $save_id = $id;
        if (!$id) {
            $save_id = $save_result;
        } else {
            if (!$save_result) {
                $save_id = 0;
            }
        }

        if (!$save_id) {
            echo json_encode(array("success" => false, "message" => "Could not save session."));
            return;
        }

        echo json_encode(array(
            "success" => true,
            "data" => $this->_session_row_data($save_id),
            "id" => $save_id,
            "message" => "Session saved."
        ));
        // --- EDUNEXA PHASE 1 FIX END ---
    }
}

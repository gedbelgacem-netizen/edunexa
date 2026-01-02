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
}

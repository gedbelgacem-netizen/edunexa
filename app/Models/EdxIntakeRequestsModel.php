<?php

namespace App\Models;

class EdxIntakeRequestsModel extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'edx_intake_requests';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $intake_table = $this->db->prefixTable('edx_intake_requests');
        $learners_table = $this->db->prefixTable('edx_learners');
        $courses_table = $this->db->prefixTable('edx_courses');

        $where = "WHERE $intake_table.deleted=0";

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $intake_table.status='$status'";
        }

        $sql = "SELECT $intake_table.*,
                       $learners_table.learner_ref,
                       $learners_table.first_name,
                       $learners_table.last_name,
                       $learners_table.status AS learner_status,
                       $learners_table.course_id,
                       $courses_table.name AS course_name
                FROM $intake_table
                LEFT JOIN $learners_table ON $learners_table.id=$intake_table.learner_id
                LEFT JOIN $courses_table ON $courses_table.id=$learners_table.course_id
                $where
                ORDER BY $intake_table.id DESC";

        return $this->db->query($sql);
    }

    function get_latest_intake_id($learner_id) {
        $intake_table = $this->db->prefixTable('edx_intake_requests');
        $learner_id = $this->_get_clean_value($learner_id);

        $sql = "SELECT MAX($intake_table.id) AS max_id
                FROM $intake_table
                WHERE $intake_table.deleted=0 AND $intake_table.learner_id=$learner_id";
        $row = $this->db->query($sql)->getRow();
        return $row && $row->max_id ? intval($row->max_id) : 0;
    }

    function mark_latest_completed($learner_id, $completed_at) {
        $latest_id = $this->get_latest_intake_id($learner_id);
        if (!$latest_id) {
            return false;
        }

        $data = array(
            "status" => "completed",
            "completed_at" => $completed_at
        );

        return $this->ci_save($data, $latest_id);
    }
}

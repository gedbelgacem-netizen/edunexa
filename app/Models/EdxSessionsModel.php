<?php

namespace App\Models;

class EdxSessionsModel extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'edx_sessions';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $sessions_table = $this->db->prefixTable('edx_sessions');
        $learners_table = $this->db->prefixTable('edx_learners');
        $courses_table = $this->db->prefixTable('edx_courses');

        $where = "WHERE $sessions_table.deleted=0";

        $learner_id = $this->_get_clean_value($options, "learner_id");
        if ($learner_id) {
            $where .= " AND $sessions_table.learner_id=$learner_id";
        }

        $created_by = $this->_get_clean_value($options, "created_by");
        if ($created_by) {
            // Coordinator isolation: filter by learner.created_by
            $where .= " AND $learners_table.created_by=$created_by";
        }

        $start = $this->_get_clean_value($options, "start");
        $end = $this->_get_clean_value($options, "end");

        if ($start && $end) {
            $where .= " AND ($sessions_table.start < '$end' AND $sessions_table.end > '$start')";
        }

        $sql = "SELECT $sessions_table.*,
                       $learners_table.learner_ref,
                       $learners_table.first_name,
                       $learners_table.last_name,
                       $courses_table.name AS course_name,
                       $courses_table.color AS course_color
                FROM $sessions_table
                LEFT JOIN $learners_table ON $learners_table.id=$sessions_table.learner_id
                LEFT JOIN $courses_table ON $courses_table.id=$sessions_table.course_id
                $where
                ORDER BY $sessions_table.start DESC";

        return $this->db->query($sql);
    }

    function get_one_details($session_id, $created_by = 0) {
        $sessions_table = $this->db->prefixTable('edx_sessions');
        $learners_table = $this->db->prefixTable('edx_learners');
        $courses_table = $this->db->prefixTable('edx_courses');

        $session_id = $this->_get_clean_value($session_id);
        $created_by = $this->_get_clean_value($created_by);

        $where = "WHERE $sessions_table.deleted=0 AND $sessions_table.id=$session_id";

        if ($created_by) {
            // Coordinator isolation: filter by learner.created_by
            $where .= " AND $learners_table.created_by=$created_by";
        }

        $sql = "SELECT $sessions_table.*,
                       $learners_table.learner_ref,
                       $learners_table.first_name,
                       $learners_table.last_name,
                       $learners_table.status AS learner_status,
                       $courses_table.name AS course_name,
                       $courses_table.color AS course_color
                FROM $sessions_table
                LEFT JOIN $learners_table ON $learners_table.id=$sessions_table.learner_id
                LEFT JOIN $courses_table ON $courses_table.id=$sessions_table.course_id
                $where";

        return $this->db->query($sql);
    }
}

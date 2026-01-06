<?php

namespace App\Models;

class EdxLearnersModel extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'edx_learners';
        parent::__construct($this->table);
    }

    function get_next_learner_ref() {
        $learners_table = $this->db->prefixTable('edx_learners');

        $sql = "SELECT MAX($learners_table.learner_ref) AS max_ref
                FROM $learners_table
                WHERE $learners_table.deleted=0";
        $row = $this->db->query($sql)->getRow();

        $max_ref = $row && $row->max_ref ? $row->max_ref : "000";
        $next = intval($max_ref) + 1;

        if ($next > 999) {
            return false;
        }

        return str_pad((string) $next, 3, "0", STR_PAD_LEFT);
    }

    function get_details($options = array()) {
        $learners_table = $this->db->prefixTable('edx_learners');
        $courses_table = $this->db->prefixTable('edx_courses');
        $trainers_table = $this->db->prefixTable('edx_trainers');
        $intake_table = $this->db->prefixTable('edx_intake_requests');

        $where = "WHERE $learners_table.deleted=0";

        $created_by = $this->_get_clean_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $learners_table.created_by=$created_by";
        }

        $search = $this->_get_clean_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($learners_table.learner_ref LIKE '%$search%' ESCAPE '!' 
                          OR $learners_table.first_name LIKE '%$search%' ESCAPE '!' 
                          OR $learners_table.last_name LIKE '%$search%' ESCAPE '!')";
        }

        $sql = "SELECT $learners_table.*,
                       $courses_table.name AS course_name,
                       $courses_table.color AS course_color,
                       $trainers_table.name AS trainer_name,
                       (SELECT $intake_table.status FROM $intake_table 
                            WHERE $intake_table.deleted=0 AND $intake_table.learner_id=$learners_table.id
                            ORDER BY $intake_table.id DESC LIMIT 1) AS latest_intake_status
                FROM $learners_table
                LEFT JOIN $courses_table ON $courses_table.id=$learners_table.course_id
                LEFT JOIN $trainers_table ON $trainers_table.id=$learners_table.trainer_id
                $where
                ORDER BY $learners_table.id DESC";

        return $this->db->query($sql);
    }

    function get_one_details($learner_id, $created_by = 0) {
        $learners_table = $this->db->prefixTable('edx_learners');
        $courses_table = $this->db->prefixTable('edx_courses');
        $trainers_table = $this->db->prefixTable('edx_trainers');

        $learner_id = $this->_get_clean_value($learner_id);
        $created_by = $this->_get_clean_value($created_by);

        $where = "WHERE $learners_table.deleted=0 AND $learners_table.id=$learner_id";
        if ($created_by) {
            $where .= " AND $learners_table.created_by=$created_by";
        }

        $sql = "SELECT $learners_table.*,
                       $courses_table.name AS course_name,
                       $courses_table.color AS course_color,
                       $trainers_table.name AS trainer_name
                FROM $learners_table
                LEFT JOIN $courses_table ON $courses_table.id=$learners_table.course_id
                LEFT JOIN $trainers_table ON $trainers_table.id=$learners_table.trainer_id
                $where";

        return $this->db->query($sql);
    }
}

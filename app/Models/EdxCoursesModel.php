<?php

namespace App\Models;

class EdxCoursesModel extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'edx_courses';
        parent::__construct($this->table);
    }

    function get_dropdown_list() {
        $result = $this->get_all_where(array("deleted" => 0, "is_active" => 1))->getResult();
        $dropdown = array();
        foreach ($result as $row) {
            $dropdown[$row->id] = $row->name;
        }
        return $dropdown;
    }

    function get_details($options = array()) {
        $courses_table = $this->db->prefixTable('edx_courses');

        $where = "WHERE $courses_table.deleted=0";
        $is_active = $this->_get_clean_value($options, "is_active");
        if ($is_active !== "") {
            $where .= " AND $courses_table.is_active=$is_active";
        }

        $sql = "SELECT $courses_table.*
                FROM $courses_table
                $where
                ORDER BY $courses_table.name ASC";
        return $this->db->query($sql);
    }
}

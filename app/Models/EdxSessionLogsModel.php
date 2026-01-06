<?php

namespace App\Models;

class EdxSessionLogsModel extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'edx_session_logs';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $logs_table = $this->db->prefixTable('edx_session_logs');
        $users_table = $this->db->prefixTable('users');

        $where = "WHERE $logs_table.deleted=0";

        $session_id = $this->_get_clean_value($options, "session_id");
        if ($session_id) {
            $where .= " AND $logs_table.session_id=$session_id";
        }

        $sql = "SELECT $logs_table.*,
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_name
                FROM $logs_table
                LEFT JOIN $users_table ON $users_table.id=$logs_table.created_by
                $where
                ORDER BY $logs_table.created_at DESC";

        return $this->db->query($sql);
    }

    function get_one_details($log_id) {
        $logs_table = $this->db->prefixTable('edx_session_logs');
        $users_table = $this->db->prefixTable('users');

        $log_id = $this->_get_clean_value($log_id);

        $sql = "SELECT $logs_table.*,
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_name
                FROM $logs_table
                LEFT JOIN $users_table ON $users_table.id=$logs_table.created_by
                WHERE $logs_table.deleted=0 AND $logs_table.id=$log_id";

        return $this->db->query($sql);
    }
}

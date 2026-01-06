<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEdxPhase1CoreTables extends Migration {

    public function up() {
        // ------------------------------------------------------------------
        // edx_courses
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_courses', true);
        $this->forge->reset();

        // ------------------------------------------------------------------
        // edx_trainers (virtual trainers lookup)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_trainers', true);
        $this->forge->reset();

        // ------------------------------------------------------------------
        // edx_learners
        // trainer_id MUST link to edx_trainers.id (virtual trainers), not users.id
        // created_by links to users.id
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'learner_ref' => [
                'type' => 'CHAR',
                'constraint' => 3,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'trainer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'planned_minutes_total' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'new',
            ],
            'activated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('learner_ref', false, true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('trainer_id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_learners', true);
        $this->forge->reset();

        // ------------------------------------------------------------------
        // edx_intake_requests
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'learner_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pending',
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('learner_id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_intake_requests', true);
        $this->forge->reset();

        // ------------------------------------------------------------------
        // edx_sessions
        // planned_minutes = (end - start) minutes (business logic computed in code)
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'learner_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'scheduled',
            ],
            'start' => [
                'type' => 'DATETIME',
            ],
            'end' => [
                'type' => 'DATETIME',
            ],
            'planned_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('learner_id');
        $this->forge->addKey('course_id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_sessions', true);
        $this->forge->reset();

        // ------------------------------------------------------------------
        // edx_session_logs
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'session_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'delivered_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('session_id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('edx_session_logs', true);
        $this->forge->reset();
    }

    public function down() {
        $this->forge->dropTable('edx_session_logs', true);
        $this->forge->dropTable('edx_sessions', true);
        $this->forge->dropTable('edx_intake_requests', true);
        $this->forge->dropTable('edx_learners', true);
        $this->forge->dropTable('edx_trainers', true);
        $this->forge->dropTable('edx_courses', true);
    }

}

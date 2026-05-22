<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegulationDocumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'document_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['sop', 'draft_regulasi', 'policy_letter', 'forms', 'others'],
                'default'    => 'others',
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'publish_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('category');
        $this->forge->createTable('ci_regulation_documents');
    }

    public function down()
    {
        $this->forge->dropTable('ci_regulation_documents');
    }
}

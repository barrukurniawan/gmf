<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CapabilityEvaluations extends Migration
{
	public function up()
	{
		// 1. ci_capability_evaluations
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'capability_no'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
			'evaluation_date'  => ['type' => 'DATE', 'null' => true],
			'type_of_aircraft' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'manufacture'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'ata_chapter'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
			'rating'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'scope_of_work'    => ['type' => 'TEXT', 'null' => true],
			'form_type'        => ['type' => 'ENUM', 'constraint' => ['maintenance', 'component'], 'default' => 'maintenance'],
			'decision_status'  => ['type' => 'ENUM', 'constraint' => ['approved', 'not_approved'], 'null' => true],
			'decision_remarks' => ['type' => 'TEXT', 'null' => true],
			'prepared_by_1_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'prepared_by_2_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'approved_by_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
			'created_at'       => ['type' => 'DATETIME', 'null' => true],
			'updated_at'       => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_evaluations', true);

		// 2. ci_capability_eval_tech_data
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'requirement' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'available'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // Yes/No/NA
			'reference'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_eval_tech_data', true);

		// 3. ci_capability_eval_facilities
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'requirement' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'available'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // Yes/No
			'remarks'     => ['type' => 'TEXT', 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_eval_facilities', true);

		// 4. ci_capability_eval_tools
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'part_number' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'type'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'remarks'     => ['type' => 'TEXT', 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_eval_tools', true);

		// 5. ci_capability_eval_personnel
		$this->forge->addField([
			'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'name'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'position'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'year_of_experience' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
			'rating'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'amel_no'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_eval_personnel', true);

		// 6. ci_capability_eval_approvals
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'item'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
			'status'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('ci_capability_eval_approvals', true);

		// 7. ci_capability_signatures
		$this->forge->addField([
			'signature_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'eval_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
			'signature_type' => ['type' => 'VARCHAR', 'constraint' => 50], // prepared_by_1, prepared_by_2, approved_by
			'signature_data' => ['type' => 'LONGTEXT', 'null' => true],
			'file_path'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
			'created_at'     => ['type' => 'DATETIME', 'null' => true],
			'updated_at'     => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addKey('signature_id', true);
		$this->forge->createTable('ci_capability_signatures', true);
	}

	public function down()
	{
		$this->forge->dropTable('ci_capability_evaluations', true);
		$this->forge->dropTable('ci_capability_eval_tech_data', true);
		$this->forge->dropTable('ci_capability_eval_facilities', true);
		$this->forge->dropTable('ci_capability_eval_tools', true);
		$this->forge->dropTable('ci_capability_eval_personnel', true);
		$this->forge->dropTable('ci_capability_eval_approvals', true);
		$this->forge->dropTable('ci_capability_signatures', true);
	}
}

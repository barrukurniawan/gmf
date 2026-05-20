<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrainingRecordTables extends Migration
{
	public function up()
	{
		// 1. employee_training_record_header
		$this->forge->addField([
			'training_record_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'place_of_birth' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'address' => [
				'type' => 'TEXT',
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
		$this->forge->addKey('training_record_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_training_record_header');

		// 2. employee_education
		$this->forge->addField([
			'education_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'degree' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'institution' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'major' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'graduate_year' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
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
		$this->forge->addKey('education_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_education');

		// 3. employee_training_history
		$this->forge->addField([
			'training_history_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'course_title' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'course_objective' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'date_completed' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			],
			'test_result' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'total_hours' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			],
			'location' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'instructor_name' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
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
		$this->forge->addKey('training_history_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_training_history');

		// 4. employee_licenses
		$this->forge->addField([
			'license_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'license_type' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			],
			'license_no' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'expired_date' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
			],
			'rating' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
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
		$this->forge->addKey('license_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_licenses');

		// 5. employee_basic_certificates
		$this->forge->addField([
			'certificate_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
			],
			'a1' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'a2' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'a3' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'a4' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'c1' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'c2' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'c4' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
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
		$this->forge->addKey('certificate_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_basic_certificates');
	}

	public function down()
	{
		$this->forge->dropTable('employee_basic_certificates');
		$this->forge->dropTable('employee_licenses');
		$this->forge->dropTable('employee_training_history');
		$this->forge->dropTable('employee_education');
		$this->forge->dropTable('employee_training_record_header');
	}
}

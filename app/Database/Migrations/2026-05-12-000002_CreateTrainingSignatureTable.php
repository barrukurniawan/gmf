<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrainingSignatureTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'signature_id' => [
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
			'signature_type' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => true,
				'comment' => 'approved_by',
			],
			'signature_data' => [
				'type' => 'TEXT',
				'null' => true,
				'comment' => 'Base64 data_uri or file path',
			],
			'file_path' => [
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => true,
				'comment' => 'Path to uploaded signature file',
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
		$this->forge->addKey('signature_id', true);
		$this->forge->addKey('user_id');
		$this->forge->createTable('employee_training_signatures');
	}

	public function down()
	{
		$this->forge->dropTable('employee_training_signatures');
	}
}

<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstitutionToEmployeeTrainingHistory extends Migration
{
    public function up()
    {
        $fields = [
            'institution' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('employee_training_history', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('employee_training_history', 'institution');
    }
}

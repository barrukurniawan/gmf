<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
if ($db->tableExists('ci_capability_signatures')) {
    echo "Table ci_capability_signatures exists.\n";
} else {
    echo "Table ci_capability_signatures does not exist. Creating...\n";
    $forge = \Config\Database::forge();
    $fields = [
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'eval_id' => [
            'type'       => 'INT',
            'constraint' => 11,
        ],
        'user_id' => [
            'type'       => 'INT',
            'constraint' => 11,
        ],
        'signature_type' => [
            'type'       => 'VARCHAR',
            'constraint' => '50',
        ],
        'file_path' => [
            'type'       => 'VARCHAR',
            'constraint' => '255',
            'null'       => true,
        ],
        'signature_data' => [
            'type' => 'LONGTEXT',
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
    ];
    $forge->addField($fields);
    $forge->addKey('id', true);
    if($forge->createTable('ci_capability_signatures')) {
        echo "Table created successfully.\n";
    } else {
        echo "Error creating table.\n";
    }
}

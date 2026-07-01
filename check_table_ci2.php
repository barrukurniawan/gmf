<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('ci_capability_evaluations');
echo "Fields in ci_capability_evaluations:\n";
print_r($fields);

if (!in_array('part_number', $fields)) {
    echo "Adding part_number and component_type...\n";
    $forge = \Config\Database::forge();
    $forge->addColumn('ci_capability_evaluations', [
        'part_number' => [
            'type' => 'VARCHAR',
            'constraint' => '100',
            'null' => true
        ],
        'component_type' => [
            'type' => 'VARCHAR',
            'constraint' => '100',
            'null' => true
        ]
    ]);
    echo "Columns added.\n";
} else {
    echo "Columns already exist.\n";
}

<?php
$mysqli = new mysqli("localhost", "root", "", "gmf");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("SHOW TABLES LIKE 'ci_capability_signatures'");
if ($result->num_rows > 0) {
    echo "Table exists.\n";
} else {
    echo "Table does not exist. Creating...\n";
    $sql = "CREATE TABLE `ci_capability_signatures` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `eval_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `signature_type` varchar(50) NOT NULL,
        `file_path` varchar(255) DEFAULT NULL,
        `signature_data` longtext DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    if ($mysqli->query($sql) === TRUE) {
        echo "Table created successfully.\n";
    } else {
        echo "Error creating table: " . $mysqli->error . "\n";
    }
}
$mysqli->close();
?>

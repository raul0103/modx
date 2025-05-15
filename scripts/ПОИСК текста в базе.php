<?php

$pdo = new PDO("mysql:host=mysql-8.2;dbname=digital-zhbi", "root", "");
$search = 'discount-banner__image';

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $columns = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        $type = strtolower($column['Type']);
        if (strpos($type, 'char') !== false || strpos($type, 'text') !== false) {
            $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `$column[Field]` LIKE ?");
            $stmt->execute(["%$search%"]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows) {
                echo "Found in $table.$column[Field]:\n";
                print_r($rows);
            }
        }
    }
}

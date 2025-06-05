<?php

if (!function_exists('tsCreateCategory')) {
    function tsCreateCategory($name, $context_key)
    {
        global $modx;

        if (empty($name) || empty($context_key)) {
            http_response_code(422);
            exit(json_encode([
                "success" => false,
                "message" => "Не переданы обязательные параметры"
            ]));
        }

        $table_prefix = $modx->getOption('table_prefix');

        $stmt = $modx->prepare("INSERT INTO {$table_prefix}ts_category_tags (`name`, `context_key`) VALUES (:name, :context_key)");
        $success = $stmt->execute([
            ':name' => $name,
            ':context_key' => $context_key
        ]);

        if (!$success) {
            http_response_code(500);
            exit(json_encode([
                "success" => false,
                "message" => "Ошибка при создании категории"
            ]));
        }

        $newId = $modx->lastInsertId();

        $stmt = $modx->prepare("SELECT * FROM {$table_prefix}ts_category_tags WHERE id = ?");
        $stmt->execute([$newId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        exit(json_encode([
            "success" => true,
            "data" => $row
        ]));
    }
}

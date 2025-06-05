<?php

if (!function_exists('tsCreateTag')) {
    function tsCreateTag($title = null, $uri = null, $image = null, $type, $resource_id = null, $category_id, $group_name = null)
    {
        global $modx;

        if (empty($category_id) || empty($type)) {
            http_response_code(422);
            exit(json_encode([
                "success" => false,
                "message" => "Не указаны обязательные параметры: category_id или type"
            ]));
        }

        $table_prefix = $modx->getOption('table_prefix');

        $sql = "INSERT INTO {$table_prefix}ts_tag_items 
                (`title`, `uri`, `image`, `type`, `resource_id`, `category_id`, `group_name`) 
                VALUES (:title, :uri, :image, :type, :resource_id, :category_id, :group_name)";

        $stmt = $modx->prepare($sql);

        $success = $stmt->execute([
            ':title'       => $title,
            ':uri'         => $uri,
            ':image'       => $image,
            ':type'        => $type,
            ':resource_id' => $resource_id ?: null,
            ':category_id' => $category_id,
            ':group_name'  => $group_name
        ]);

        if (!$success) {
            http_response_code(500);
            exit(json_encode([
                "success" => false,
                "message" => "Ошибка при создании тега"
            ]));
        }

        $newId = $modx->lastInsertId();

        $stmt = $modx->prepare("SELECT * FROM {$table_prefix}ts_tag_items WHERE id = ?");
        $stmt->execute([$newId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        exit(json_encode([
            "success" => true,
            "data"    => $row
        ]));
    }
}

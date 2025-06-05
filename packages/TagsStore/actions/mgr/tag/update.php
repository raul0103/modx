<?php

if (!function_exists('tsUpdateTag')) {
    function tsUpdateTag($tag_id, $title = null, $uri = null, $image = null, $type, $resource_id = null, $category_id, $group_name = null)
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $sql = "UPDATE {$table_prefix}ts_tag_items SET 
                    title = :title,
                    uri = :uri,
                    image = :image,
                    type = :type,
                    resource_id = :resource_id,
                    category_id = :category_id,
                    group_name = :group_name
                WHERE id = :id";

        $stmt = $modx->prepare($sql);
        $success = $stmt->execute([
            ':title'       => $title,
            ':uri'         => $uri,
            ':image'       => $image,
            ':type'        => $type,
            ':resource_id' => $resource_id ?: null,
            ':category_id' => $category_id,
            ':group_name'  => $group_name,
            ':id'          => $tag_id,
        ]);

        if (!$success) {
            exit(json_encode([
                "success" => false,
                "message" => "Error update tag"
            ]));
        }

        $stmt = $modx->prepare("SELECT * FROM {$table_prefix}ts_tag_items WHERE id = ?");
        $stmt->execute([$tag_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        exit(json_encode([
            "success" => true,
            "data" => $row
        ]));
    }
}

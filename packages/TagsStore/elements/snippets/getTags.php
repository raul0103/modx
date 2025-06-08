<?php

/**
 * @param tvName [require] - Название тв поля с перечисленными через запятую ID категорий тегов
 * @param resourceImageTVName [optional] - Название tv поля для получения картинок у тегов с типо resource 
 * 
 * @return array - Скрипт вернет массив тегов, разбитых по group_name
 * [
 *   {
 *     "group_name": "Для внутренних перегородок и стен",
 *     "items": [
 *       {
 *         "title": "Роквул Лайт Баттс",
 *         "uri": "/bats",
 *         "image": "/assets/img/image.jpg"
 *       }
 *     ]
 *   },
 *   {
 *     "group_name": null, // Теги без групп
 *     "items": [
 *       {
 *         "title": "Роквул Лайт",
 *         "uri": "/lite",
 *         "image": "/assets/img/image.jpg"
 *       }
 *     ]
 *   }
 * ]
 */


if (!$tvName) return;

$TAG_FIELDS = ["title", "uri", "image"]; // Поля которые будут у тега

$tag_category_ids = $modx->resource->getTVValue($tvName); // string - ID категорий тегов через запятую

if (empty($tag_category_ids)) return [];

$table_prefix = $modx->getOption('table_prefix');

$result = $modx->query("SELECT * FROM {$table_prefix}ts_tag_items WHERE category_id IN ($tag_category_ids)");
$tags = $result->fetchAll(PDO::FETCH_ASSOC);

if (empty($tags)) return [];

// Необходимо получить теги type = resource
$resource_ids = [];
foreach ($tags as $tag) {
    if ($tag['type'] === 'resource') {
        $resource_ids[] = $tag['resource_id'];
    }
}

// Поиск ресурсов для получения полей
$resources_by_id = []; // Ресурсы с ключом по ID. В дальнейшем будет по нему быстрый поиск
if (!empty($resource_ids)) {
    $resources = $modx->getCollection('modResource', [
        'id:in' => $resource_ids
    ]);

    if (!empty($resources)) {
        foreach ($resources as $resource) {
            $resources_by_id[$resource->get('id')] = $resource;
        }
    }
}

$result = [];
foreach ($tags as $tag) {
    if (!isset($result[$tag['group_name']])) $result[$tag['group_name']] = [
        "group_name" => $tag['group_name'],
        "items" => []
    ];

    if ($tag['type'] === "resource") {
        $resource = $resources_by_id[$tag['resource_id']];

        $tag["title"] = $tag['title'] ?: $resource->get("menutitle") ?: $resource->get("pagetitle");
        $tag["uri"] = $resource->get("uri");
        if ($resourceImageTVName) {
            $tag["image"] = $resource->getTVValue($resourceImageTVName);
        }
    }

    $tag_data = [];
    foreach ($TAG_FIELDS as $TAG_FIELD) {
        $tag_data[$TAG_FIELD] = $tag[$TAG_FIELD];
    }

    $result[$tag['group_name']]['items'][] = $tag_data;
}
$result = array_values($result);


return $result;

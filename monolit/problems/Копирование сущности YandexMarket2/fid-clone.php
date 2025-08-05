<?php

/** @var modX $modx */
define('MODX_API_MODE', true);
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('❌ Только POST-запросы разрешены.');
}

// Проверка наличия параметра fid_id
if (empty($_POST['fid_id'])) {
    http_response_code(400); // Bad Request
    exit('❌ Не передан параметр fid_id.');
}

$sourcePricelistId = (int) $_POST['fid_id'];

if (!$sourcePricelistId) {
    return '❌ ID прайс-листа не передан.';
}

// Получить исходный прайс-лист
$sql = "SELECT * FROM modx_yandexmarket_pricelists WHERE id = $sourcePricelistId";
$result = $modx->query($sql);
if (!$result) {
    return '❌ Ошибка при получении прайс-листа.';
}

$original = $result->fetch(PDO::FETCH_ASSOC);
if (!$original) {
    return '❌ Прайс-лист с таким ID не найден.';
}

// Подготовка данных к вставке
unset($original['id']);
$timestamp = time();
if (isset($original['name'])) $original['name'] = "(копия $timestamp) " . $original['name'];
if (isset($original['file'])) $original['file'] = "(копия $timestamp) " . $original['file'];

$columns = implode(',', array_keys($original));
$placeholders = ':' . implode(', :', array_keys($original));

$stmt = $modx->prepare("INSERT INTO modx_yandexmarket_pricelists ($columns) VALUES ($placeholders)");
if (!$stmt || !$stmt->execute($original)) {
    return '❌ Ошибка при вставке прайс-листа.';
}

$newPricelistId = $modx->lastInsertId();
$output = "✅ Создан новый прайс-лист с ID: $newPricelistId<br><br>";

// Таблицы для копирования
$relatedTables = [
    'modx_yandexmarket_fields',
    'modx_yandexmarket_conditions',
    'modx_yandexmarket_categories',
];

$fieldIdMap = [];         // Старый ID => Новый ID
$pendingParentMap = [];   // Новый ID => Старый Parent ID

foreach ($relatedTables as $table) {
    $sql = "SELECT * FROM $table WHERE pricelist_id = $sourcePricelistId";
    $result = $modx->query($sql);
    if (!$result) {
        $output .= "❌ Ошибка запроса к $table<br>";
        continue;
    }

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;

    foreach ($rows as $row) {
        $oldId = $row['id'];
        $oldParentId = $row['parent'] ?? null;

        unset($row['id']);
        $row['pricelist_id'] = $newPricelistId;

        $cols = implode(',', array_map(fn($col) => "`$col`", array_keys($row)));
        $phs = ':' . implode(', :', array_keys($row));

        $stmt = $modx->prepare("INSERT INTO $table ($cols) VALUES ($phs)");
        if ($stmt && $stmt->execute($row)) {
            $newId = $modx->lastInsertId();
            $count++;

            if ($table === 'modx_yandexmarket_fields') {
                $fieldIdMap[$oldId] = $newId;
                if (!empty($oldParentId)) {
                    $pendingParentMap[$newId] = $oldParentId;
                }
            }
        } else {
            $output .= "❌ Ошибка вставки в $table: " . print_r($stmt->errorInfo(), true) . "<br>";
        }
    }

    $output .= "✅ Скопировано $count записей в $table<br>";
}

// Обновление parent в скопированных полях
$parentFixCount = 0;
foreach ($pendingParentMap as $newFieldId => $oldParentId) {
    if (isset($fieldIdMap[$oldParentId])) {
        $newParentId = $fieldIdMap[$oldParentId];
        $stmt = $modx->prepare("UPDATE `modx_yandexmarket_fields` SET `parent` = :parent WHERE `id` = :id");
        if ($stmt->execute(['parent' => $newParentId, 'id' => $newFieldId])) {
            $parentFixCount++;
        } else {
            $output .= "❌ Ошибка обновления parent для field_id = $newFieldId<br>";
        }
    } else {
        $output .= "⚠️ Пропущен parent для field_id = $newFieldId (не найден новый parent для old_id = $oldParentId)<br>";
    }
}
$output .= "✅ Обновлено parent-ссылок: $parentFixCount<br>";

// Копирование field_attributes
$attributeCount = 0;
foreach ($fieldIdMap as $oldFieldId => $newFieldId) {
    $sql = "SELECT * FROM modx_yandexmarket_field_attributes WHERE field_id = $oldFieldId";
    $result = $modx->query($sql);
    if (!$result) {
        $output .= "❌ Ошибка выборки атрибутов для field_id = $oldFieldId<br>";
        continue;
    }

    $attributes = $result->fetchAll(PDO::FETCH_ASSOC);
    foreach ($attributes as $attr) {
        unset($attr['id']);
        $attr['field_id'] = $newFieldId;

        $cols = implode(',', array_map(fn($col) => "`$col`", array_keys($attr)));
        $phs = ':' . implode(', :', array_keys($attr));

        $stmt = $modx->prepare("INSERT INTO modx_yandexmarket_field_attributes ($cols) VALUES ($phs)");
        if ($stmt && $stmt->execute($attr)) {
            $attributeCount++;
        } else {
            $output .= "❌ Ошибка вставки в modx_yandexmarket_field_attributes: " . print_r($stmt->errorInfo(), true) . "<br>";
        }
    }
}
$output .= "✅ Скопировано $attributeCount атрибутов в modx_yandexmarket_field_attributes<br>";

return $output;

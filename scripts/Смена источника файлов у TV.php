<?php

/*
- Проверить константы index.php
- Запустить index.php
- Через системные настройки изменить default_media_source на новый источник
- Пройти по всем чанкам и сниппетам, найти мигикс - ТВ которые были указаны для изменения источника
 */

/**
 * Скрипт меняет источники у TV полей и изменяет их значения 
 * !!!ВНИМАНИЕ!!! - Комментарии на опасных участках кода
 */

// >>> Константы
define("TABLE_PREFIX", $modx->getOption('table_prefix'));

// Условие для выборки TV полей
define("WHERE_SELECT_TV", [
    'name:in' => ['mainImage', 'categoryCustomTags', 'user_gallery_migx']
]);

// Подмена значений в TV полях из-за смены источника
define("REPLACES", [
    "assets/" => "",
]);

// ID Нового источника
define("NEW_SOURCE_ID", 5);
// <<<

// >>> Получаем $tv_ids - ID TV у которых необходимо сменить источник
$where = whereTransform(WHERE_SELECT_TV);
$sql = "SELECT id FROM " . TABLE_PREFIX . "site_tmplvars WHERE $where";
$result = $modx->query($sql);
$tv_ids = $result->fetchAll(PDO::FETCH_COLUMN, 0);
// <<<

// >>> Меняем источник у полученных $tv_ids
$where = whereTransform([
    'object:in' => $tv_ids,
    'object_class' => 'modTemplateVar'
]);
$sql = "UPDATE " . TABLE_PREFIX . "media_sources_elements SET source = " . NEW_SOURCE_ID . " WHERE $where";
$result = $modx->query($sql);
// <<<

// >>> !!!ВНИМАНИЕ!!! - Данный код опасен, так как во множестве ТВ полей идет replace данных
$where = whereTransform([
    'tmplvarid:in' => $tv_ids
]);

foreach (REPLACES as $search => $replace) {
    $sql = "UPDATE " . TABLE_PREFIX . "site_tmplvar_contentvalues SET `value` = REPLACE(`value`,'$search','$replace') WHERE $where;";
    $result = $modx->query($sql);
}
// <<<

/**
 * Функция приводит массив $where к строке для использования в SQL
 * @param array $where
 * @return string
 */
function whereTransform($where)
{
    $result = [];

    foreach ($where as $key => $value) {
        $conditions =  explode(':', $key);

        $field = $conditions[0];
        $operator = isset($conditions[1]) ? $conditions[1] : "=";

        $value = $operator == 'in' ? "('" . implode("','", $value) . "')" : "'$value'";

        $result[] = "`$field` $operator $value";
    }
    $result = implode(' AND ', $result);

    return $result;
}

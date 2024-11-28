<?php

/**
 * Формирование sql фильтрации если были передан get параметры
 */

if (!function_exists('sfWhereFilters')) {
    function sfWhereFilters($params, $filter_uniqueid, $table_name)
    {

        if (!isset($params[$filter_uniqueid])) return '';

        // Декодируем JSON из GET-параметра
        $data = json_decode(urldecode($params[$filter_uniqueid]), true);

        if (empty($data['filters'])) return '';

        // Обрабатываем параметры options
        $conditions = [];
        foreach ($data['filters'] as $key => $values) {
            $values_string = is_array($values) ? implode("','", $values) : $values;
            $values_string =  "'$values_string'";

            // Объединяем значения через OR
            $conditions[] = "MAX(CASE WHEN po.`key` = '$key' THEN po.`value` END) IN ($values_string)";
        }

        // Объединяем все условия через AND
        $sqlWhere = ' HAVING ' . implode(' AND ', $conditions);

        return $sqlWhere;
    }
}

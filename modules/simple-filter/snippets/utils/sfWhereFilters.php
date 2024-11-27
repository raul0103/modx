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

        $conditions = [];

        // Обрабатываем параметры options
        foreach ($data['filters'] as $key => $values) {
            if (!is_array($values)) {
                $values = [$values]; // Приводим к массиву, если одно значение
            }

            $valueConditions = [];
            foreach ($values as $value) {
                $valueConditions[] = "($table_name.`key` = '$key' AND $table_name.`value` = '$value')";
            }

            // Объединяем значения через OR
            $conditions[] = '(' . implode(' OR ', $valueConditions) . ')';
        }

        // Объединяем все условия через AND
        $sqlWhere = ' AND ' . implode(' AND ', $conditions);

        return $sqlWhere;
    }
}

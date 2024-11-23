<?php

/**
 * Массив опций переводит в sql запрос для получения опций из таблиц
 */
if (!function_exists('sfProductOptions')) {
    function sfProductOptions($options, $table_name)
    {
        if (empty($options)) return '';

        $temporary = [];
        foreach ($options as $option) {
            $temporary[] = "MAX(CASE WHEN $table_name.`key` = '$option' THEN $table_name.`value` END) AS `$option`";
        }

        return ',' . implode(',', $temporary);
    }
}

// Пример использования
// echo sfProductOptions(['mass-t','cvet']);

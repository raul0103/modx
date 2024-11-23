<?php

/**
 * Формирование условий $where для sql запроса
 */
if (!function_exists('sfWhereToString')) {
    function sfWhereToString($where)
    {
        if (empty($where)) return '';

        $temporary = [];
        foreach ($where as $key => $value) {
            $conditions = explode(':', $key);

            $field = $conditions[0];
            $operator = strtoupper($conditions[1] ?? '=');

            // Экранирование значений и обработка массивов


            if ($operator === 'IN' || $operator === 'NOT IN') {
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }

                $valueList = implode(', ', array_map(fn($v) => "'" . addslashes($v) . "'", $value));
                $value = "($valueList)";
            } else {
                $value = "'" . addslashes($value) . "'";
            }

            // Формируем условия
            $temporary[] = "`$field` $operator $value";
        }

        return implode(' AND ', $temporary);
    }
}

// Пример использования
// echo sfWhere([
//     'parents:IN' => [1, 2, 3, 4],
//     'pagetitle:LIKE' => 'плиты',
//     'id:NOT IN' => '5,6,7',
//     'status' => 'active',
//     'price:>' => 100,
// ]);

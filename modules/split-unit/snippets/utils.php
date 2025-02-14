<?php

if (!function_exists('parseNum')) {
    function parseNum($string)
    {
        // Удаляем пробелы, затем ищем число
        $string = preg_replace('/\s+/', '', $string);

        if (preg_match('/[\d,\.]+/', $string, $matches)) {
            return str_replace(',', '.', $matches[0]);
        } else {
            return 0;
        }
    }
}

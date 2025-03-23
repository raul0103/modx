<?php

class Helpers
{
    /**
     * В тысячные
     * @param mixed $number
     * @return string
     */
    public function formattedNumber($number)
    {
        // Форматируем число с фиксированным количеством знаков после запятой
        $formatted = number_format($number, 2, '.', ' ');
        // Убираем лишние нули после точки
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted;
    }

    /**
     * Очистит число от лишних символов
     * @param mixed $input
     * @return float
     */
    public function parseNumber($input)
    {
        $input = preg_replace('/[^\d,.-]/', '', $input); // Удаляем все кроме цифр, точки, запятой и минуса
        $input = str_replace(',', '.', $input); // Заменяем запятые на точки

        // Преобразуем строку в float для правильного формата
        $number = floatval($input);

        return $number;
    }
}

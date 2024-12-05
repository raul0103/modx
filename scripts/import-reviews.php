<?php

$modx->getService('mainService', 'mainService', MODX_CORE_PATH . 'components/mltreviews/services/');

function convertDateToISO($dateString)
{
    // Устанавливаем локаль для корректной обработки русских названий месяцев
    setlocale(LC_TIME, 'ru_RU.UTF-8'); // Убедитесь, что локаль установлена на сервере

    // Преобразуем названия месяцев в английский формат (если локаль недоступна)
    $months = [
        'января' => 'January',
        'февраля' => 'February',
        'марта' => 'March',
        'апреля' => 'April',
        'мая' => 'May',
        'июня' => 'June',
        'июля' => 'July',
        'августа' => 'August',
        'сентября' => 'September',
        'октября' => 'October',
        'ноября' => 'November',
        'декабря' => 'December',
    ];

    // Заменяем русские месяцы на английские
    $dateString = strtr(mb_strtolower($dateString), $months);

    // Добавляем год к строке
    $dateString .= ' 2024';

    // Преобразуем строку в объект DateTime
    $datetime = DateTime::createFromFormat('j F Y', $dateString, new DateTimeZone('UTC'));

    if (!$datetime) {
        return "Неверный формат даты: $dateString";
    }

    // Возвращаем дату в формате ISO
    return $datetime->format('Y-m-d');
}


$file = MODX_BASE_PATH . '_import/csv/reviews3.csv';

if (($handle = fopen($file, "r")) !== false) {
    $index = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($index++ == 0) continue;

        $res = $modx->newObject('mltReview', [
            'resource_id' => $data[0],
            'content' => $data[3],
            'user_name' => $data[1],
            'createdon' => convertDateToISO($data[2]),
            'rating' => 5,
            'published' => 1
        ]);
        $res->save();
    }
    fclose($handle);
}

<?php

if (!function_exists('rus2translit')) {
    function rus2translit($string)
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
        ];

        return strtr($string, $converter);
    }
}

if (!function_exists('handleWord')) {
    function handleWord($word, $amountChars)
    {
        if (is_numeric($word)) {
            return $word;
        }

        if (strlen($word) <= 2) {
            return ucfirst($word);
        }

        return ucfirst(substr($word, 0, $amountChars));
    }
}

$table_prefix = $modx->getOption('table_prefix');
$sql = "SELECT id FROM {$table_prefix}ms2_products WHERE `article` IS NULL OR `article` = '' ";
$result = $modx->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

$ids = array_column($data, 'id');

$resources = $modx->getCollection('modResource', [
    'id:IN' => $ids
]);
foreach ($resources as $resource) {
    $article = '';

    // Получение родителя
    $parent = $modx->getObject('modResource', $resource->parent);

    // Получение слов
    $name = $parent->get('menutitle');
    if (empty($name)) {
        $name = $parent->get('pagetitle');
    }
    $name = mb_strtolower($name);

    // Переводим в транслит
    $name = rus2translit($name);

    // Убираем ненужные символы, оставляем только буквы, числа и пробелы
    $name = preg_replace('/[^a-z0-9 ]/', '', $name);

    // Разбиваем по словам
    $words = explode(' ', $name);

    // Составление артикула
    // Первое слово
    $article .= handleWord($words[0], 3);
    if (count($words) > 1) {
        // Второе слово
        $article .= handleWord($words[1], 2);
        // Остальные слова
        for ($i = 2; $i < count($words); $i++) {
            $article .= handleWord($words[$i], 1);
        }
    }

    $article .= '-' . $resource->id;

    echo 'Для товара с id ' . $resource->id . ' артикул будет таким: ' . $article . '<br>';

    $resource->set('article', $article);
    $resource->save();
}


echo 'Конец работы скрипта';
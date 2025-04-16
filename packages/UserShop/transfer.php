<?php

/**
 * Рекурсивная функция для копирования содержимого из одной директории в другую.
 * @param string $source - исходная директория
 * @param string $destination - целевая директория
 * @return bool - результат копирования
 */
function copyDirectory($source, $destination)
{
    // Проверяем, существует ли исходная директория
    if (!is_dir($source)) {
        return false;
    }

    // Если целевая директория не существует, создаем её
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    // Открываем исходную директорию
    $dir = opendir($source);

    // Проходим по каждому элементу внутри исходной директории
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            // Если это папка, рекурсивно копируем её
            if (is_dir($source . '/' . $file)) {
                copyDirectory($source . '/' . $file, $destination . '/' . $file);
            } else {
                // Если это файл, копируем его, перезаписывая существующие файлы
                copy($source . '/' . $file, $destination . '/' . $file);
            }
        }
    }

    // Закрываем директорию
    closedir($dir);

    return true;
}

// Задаем пути к директориям
$currentDir = dirname(__FILE__);  // Текущая директория
$source1 = $currentDir . '/assets/components/'; // Путь к исходной директории 1
$destination1 = 'C:/OSPanel/home/horton-v2/assets/components'; // Путь к целевой директории 1

$source2 = $currentDir . '/core/components/'; // Путь к исходной директории 2
$destination2 = 'C:/OSPanel/home/horton-v2/core/components'; // Путь к целевой директории 2

// Копируем директории
if (copyDirectory($source1, $destination1)) {
    echo "Папка из $source1 успешно скопирована в $destination1./n";
} else {
    echo "Не удалось скопировать папку из $source1 в $destination1./n";
}

if (copyDirectory($source2, $destination2)) {
    echo "Папка из $source2 успешно скопирована в $destination2./n";
} else {
    echo "Не удалось скопировать папку из $source2 в $destination2./n";
}

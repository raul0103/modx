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

$transfers = [
    $currentDir . '/assets/components/' => 'C:/OSPanel/home/stroymarket/assets/components',
    $currentDir . '/core/components/' => 'C:/OSPanel/home/stroymarket/core/components',

    $currentDir . '/core/elements/' => 'C:/Users/rshak/Desktop/GIT/stroymarket/core/elements/modules',
];

foreach ($transfers as $source => $destination) {
    if (copyDirectory($source, $destination)) {
        echo "Папка из $source успешно скопирована в $destination./n";
    } else {
        echo "Не удалось скопировать папку из $source в $destination./n";
    }
}

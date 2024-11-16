<?php

if (empty($directory)) return;

$directory = MODX_BASE_PATH . $directory;

// Проверяем, существует ли папка
if (!is_dir($directory)) {
    return "[$directory] - directory does not exist";
}

// Получаем список всех файлов в папке
$files = scandir($directory);

// Создаем массив для путей к изображениям
$images = [];

// Перебираем файлы
foreach ($files as $file) {
    // Игнорируем "." и ".."
    if ($file === '.' || $file === '..') {
        continue;
    }

    // Полный путь к файлу
    $filePath = MODX_BASE_PATH . $directory . DIRECTORY_SEPARATOR . $file;

    // Проверяем, является ли файл изображением
    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
        $images[] = str_replace(MODX_BASE_PATH, '', $filePath);
    }
}

return $images;

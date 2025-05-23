<?php
$sourceDir = MODX_BASE_PATH . '/_import/files/images/';
$targetDir = MODX_BASE_PATH . '/assets/images/categories/common/';

// Список файлов
$files = [
    'c226d3d72d7abeafcc0b9f6691535989.jpg',
    '16a7c4f8f0214090dac4bf3f2bb6d422.jpg',
    '32db82eb79731b0604b745cbd2ebcfda.jpg',
    '884149eca4353bf5884adf651c67494c.jpg',
    '14cb988ace274651cde917adfc6dbf99.jpg',
    '2d74948441b00ce8cdbcf9cbbc5469dc.jpg',
    'd3fbd26c93fefeae25b1bf72810ffbd3.jpg',
    '8aa2d78afa331eee08f1a6f12677666c.jpg',

];

// Удаление невалидных имён
// $files = array_filter($files, fn($f) => preg_match('/^[a-z0-9._-]+\.(jpg|jpeg|png|webp|gif)$/i', $f));

// Перенос
foreach ($files as $file) {
    $src = $sourceDir . $file;
    $dst = $targetDir . $file;

    if (file_exists($src)) {
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        if (copy($src, $dst)) {
            echo "✅ Перенесён: $file\n";
        } else {
            echo "❌ Ошибка копирования: $file\n";
        }
    } else {
        echo "⚠️ Не найден: $file\n";
    }
}

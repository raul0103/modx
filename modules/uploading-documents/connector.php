<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 403");
    exit();
}

define('MODX_API_MODE', true);
include $_SERVER['DOCUMENT_ROOT'] . '/index.php';

function siteUrl()
{
    return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME']
    );
}

$rules = [
    "size" => 1024 * 1024 * 10,
    "extension" => ['xls', 'xlsx', 'xlsm', 'pdf', 'doc', 'docx']
];

$dir = '/assets/modules/uploading-documents/files/';
$basedir = $_SERVER["DOCUMENT_ROOT"] . $dir;
if (!is_dir($basedir)) {
    mkdir($basedir, 0755, true);
}

// Проверка наличия файла
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = [
        "tmp" => $_FILES['file']['tmp_name'],
        "size" => $_FILES['file']['size'],
        "extension" => strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION))
    ];
    $file['name'] = md5($_FILES['file']['name'] . $file['size']) . '.' . $file["extension"];

    if ($file['size'] > $rules["size"])
        exit(json_encode([
            "status" => false,
            "message" => "Ошибка: Файл слишком большой. Максимальный размер - 10 МБ."
        ]));

    if (!in_array($file['extension'], $rules["extension"]))
        exit(json_encode([
            "status" => false,
            "message" => "Ошибка: Неподдерживаемый формат файла. Разрешенные форматы: " . implode(", ", $rules["extension"]) . "."
        ]));

    // Создание пути для сохранения файла
    $path = $basedir . $file['name'];

    // Перемещение загруженного файла в нужную директорию
    if (move_uploaded_file($file['tmp'], $path)) {
        $url = siteUrl() . $dir . $file['name'];

        exit(json_encode([
            "status" => true,
            "message" => $url
        ]));
    } else {
        exit(json_encode([
            "status" => false,
            "message" => "Ошибка: Не удалось сохранить файл."
        ]));
    }
} else {
    exit(json_encode([
        "status" => false,
        "message" => "Ошибка: Файл не был загружен."
    ]));
}

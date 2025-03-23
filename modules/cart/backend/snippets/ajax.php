<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data['processor']) || empty($data['action']) || $data['action'] !== "cart-connect") return;

$processor = $data['processor'];
$product_data = $data['product_data'];

/**
 * Ряд проверок на наличие необходимого процессора
 */
if (!function_exists('runProcessor')) {
    function runProcessor($processor, $product_data)
    {
        global $modx;

        $class_file = $_SERVER['DOCUMENT_ROOT'] . $modx->getOption("cart_module_path") . "/backend/processors/" . $processor . '.class.php';

        if (!file_exists($class_file)) {
            exit(json_encode([
                "success" => false,
                "data" => "Файл для класса {$class_file} не найден!"
            ], JSON_UNESCAPED_UNICODE));
        }

        require_once $class_file;

        if (!class_exists($processor)) {
            exit(json_encode([
                "success" => false,
                "data" => "Класс {$processor} не найден!"
            ], JSON_UNESCAPED_UNICODE));
        }

        $class_instance = new $processor();

        if (!method_exists($class_instance, 'process')) {
            exit(json_encode([
                "success" => false,
                "data" => "Метод 'process' не найден в классе {$processor}"
            ], JSON_UNESCAPED_UNICODE));
        }

        return $class_instance->process($product_data);
    }
}

$response = runProcessor($processor, $product_data);

exit(json_encode([
    "success" => true,
    "data" => $response
], JSON_UNESCAPED_UNICODE));

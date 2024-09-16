<?php

/**
 * Плагин проверяет созданы ли на конеткстах опции которые используются в проекте
 * Событие - OnManagerPageInit
 */
if ($modx->context->key === 'mgr') {
    // Обязательные настройки контекста для проверки
    // Можно вынести в системные настройки
    $required_options = [
        "catalog_id",
        "site_id",
        "phone",
        "email"
    ];

    // Получаем список текущих настроек контекстов
    $table_prefix = $modx->getOption("table_prefix");
    $sql = "SELECT * FROM {$table_prefix}context_setting WHERE context_key != 'mgr'";
    $data = $modx->query($sql);

    /**
     * Формируем массив контекст и значения его настроек
     * $context_data = ["web" => ["site_id" => 10, "catalog_id" => 0]];
     */
    $context_data = [];
    foreach ($data as $row) {
        $ctx = $row["context_key"];

        if (empty($context_data[$ctx]))
            $context_data[$ctx] = [];

        $context_data[$ctx][$row["key"]] = $row["value"];
    }

    $errors_html  = "";
    foreach ($context_data as $context => $values) {
        $errors_html .= "<ul style='margin-top:20px'><li>Контекст: <b>$context</b></li>";

        foreach ($required_options as $required_option) {
            if (!isset($values[$required_option])) {
                $errors_html .= "<li>Отсутсвует: $required_option</li>";
            } else if (empty($values[$required_option])) {
                $errors_html .= "<li>Не заполнен: $required_option</li>";
            }
        }
        $errors_html .= "</ul>";
    }

    $modx->regClientStartupHTMLBlock('
    <script type="text/javascript">
        Ext.onReady(function() {
            Ext.Msg.alert("Внимание!", "Для корректной работы проекта необходимо заполнить следующие опции в настройках контекста ' . $errors_html . '");
        });
    </script>');
}

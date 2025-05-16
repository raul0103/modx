<?php

if (!class_exists('UTMProvider')) {
    class UTMProvider
    {
        public function __construct() {}

        /**
         * По utm_source получает данные
         * utm_source - Сохраняется в сессию для работы на других страницах
         * utm_source_remove - параметр для сброса utm_source в сессии
         * @return array
         */
        public function getData()
        {
            session_start();

            // Очистка сессии для тестов
            if ($_GET['utm_source_remove']) {
                unset($_SESSION['utm_source']);
            }

            $utm_source = $_GET['utm_source'] ?: $_SESSION['utm_source'];
            $utm_file = __DIR__ . "/data/utm/$utm_source.json";

            $utm_data = null;
            if ($utm_source && file_exists($utm_file)) {
                $_SESSION['utm_source'] = $utm_source;

                $utm_data = file_get_contents($utm_file);
                $utm_data = json_decode($utm_data, true);
            }

            return $utm_data;
        }
    }
}

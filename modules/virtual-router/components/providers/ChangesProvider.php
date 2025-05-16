<?php

if (!class_exists('ChangesProvider')) {
    class ChangesProvider
    {
        public function __construct() {}

        /**
         * Получает данные для помены на странице. Используется в плагине plugins/VirtualRouter.php
         * @param mixed $context_key
         * @param mixed $global_data
         */
        public function getData($context_key, $global_data)
        {
            $changes_file = dirname(dirname(__DIR__)) . "/data/changes/$context_key.php";
            if (file_exists($changes_file)) {
                $callback = include $changes_file;
                if (is_callable($callback)) {
                    return $callback($global_data);
                }
            }
        }
    }
}

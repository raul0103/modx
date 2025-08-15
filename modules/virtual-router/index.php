<?php

/**
 * Основная логика модуля
 * Инициализация в virtual-router/plugins/VirtualRouter.php
 * 
 * ?virtual-router-debug=true - вывод итоговых глобальных данных
 */

if (!class_exists('VirtualRouter')) {
    class VirtualRouter
    {
        public $placeholder_key;
        public $cache;
        public $main_data;
        public $context_config;
        public $active_context_key;
        public $MainHandler;
        public $GlobalProvider;
        public $UTMProvider;

        public function __construct()
        {
            global $modx;
            session_start();

            $this->initConfig();
            $this->includeComponents();

            // 1. Получили основные даные для работы модуля
            $this->main_data = $this->MainHandler->getMainData();

            // 2. Определили часть глобальных данных и подменили контекст
            $this->MainHandler->switchContext($this->GlobalProvider);
            $this->active_context_key = $this->main_data['subdomain']['redirect_context_key'] ?: $this->main_data['context_key'];

            // 3. Опередляем оставшиеся глобальные данные
            $this->GlobalProvider->getCommonData();
            $this->GlobalProvider->getContextData($this->active_context_key);

            // 4. UTM. Поулчили данные и заменили по ним глобальные данные
            $utm_data = $this->UTMProvider->getData();
            if ($utm_data) $modified_global_data = array_replace_recursive($this->GlobalProvider->global_data, $utm_data);

            // 5. Записываем данные в placeholder
            $this->GlobalProvider->setDataToPlaceholder($modified_global_data);

            // 6. Сохраним в сессию для получения данных в плагинах
            $_SESSION["virtual-router"] = $modx->getPlaceholder("virtual-router");

            // Вывод данных в JSON
            if ($_GET['virtual-router-debug']) {
                header('Content-Type: application/json; charset=utf-8');
                exit(json_encode($modx->getPlaceholder("virtual-router"), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }
        }

        public function initConfig()
        {
            $this->placeholder_key = "virtual";
            $this->cache = [
                'options' => [xPDO::OPT_CACHE_KEY => 'default/virtual-router/']
            ];
        }

        public function includeComponents()
        {
            include __DIR__ . "/components/handlers/MainHandler.php";

            include __DIR__ . "/components/providers/ChangesProvider.php";
            include __DIR__ . "/components/providers/GlobalProvider.php";
            include __DIR__ . "/components/providers/UTMProvider.php";

            $this->MainHandler = new MainHandler($this->cache);
            $this->GlobalProvider = new GlobalProvider();
            $this->UTMProvider = new UTMProvider();
        }

        public function getChangesData()
        {
            $ChangesProvider = new ChangesProvider();

            $global_data =  $this->GlobalProvider->getDataPlaceholder();
            return $ChangesProvider->getData($this->active_context_key, $global_data);
        }
    }
}

<?php

/**
 * Основная логика модуля
 * Инициализация в virtual-router/plugins/VirtualRouter.php
 * 
 * - Получение списка доменов на сайте через насройку контекста http_host
 * - Опеределение домена/поддомена по $_SERVER['HTTP_HOST']
 * - Активация необходимого контекста
 * - Получение данных по context_key и subdomain
 */

if (!class_exists('VirtualRouter')) {
    class VirtualRouter
    {
        public $placeholder_key = "virtual";
        public $cache = [
            'options' => [
                xPDO::OPT_CACHE_KEY => 'default/virtual-router/',
            ]
        ];

        public function __construct()
        {
            global $modx;

            // Получение списка доменов
            $contexts_hosts = $this->getContextsHost();
            if (empty($contexts_hosts)) return;

            // Активация необходимого контекста
            $switch_result = $this->handlerSwitchContext($contexts_hosts);

            // Получение данных
            $globaldata = $this->getCurrentContextGlobalData($switch_result['context_key'], $switch_result['subdomain']);

            // Обязательно записываем в массив. Используется по всему сайту для получения данных
            $modx->setPlaceholder($this->placeholder_key, $globaldata);
        }

        /**
         * Получение списка доменов на сайте через насройку контекста http_host
         * @return array [
         *     "minvata-spb.ru" => "ursa",
         *     "testmicortest1.store" => "web",
         *     "www-xotpipe.ru" => "xotpipe",
         *  ]
         */
        public function getContextsHost()
        {
            global $modx;

            $cache_name = "contexts-hosts";

            if (!$result = $modx->cacheManager->get($cache_name, $this->cache['options'])) {
                $result = [];

                $settings = $modx->getCollection('modContextSetting');
                foreach ($settings as $setting) {
                    if ($setting->key == 'http_host') $result[$setting->value] = $setting->context_key;
                }

                $modx->cacheManager->set($cache_name, $result, 0, $this->cache['options']);
            }

            return $result;
        }

        /**
         * Определяем надо ли подменить контекст
         * @param mixed $contexts_hosts
         * @return array{context_key: string, subdomain: string}
         */
        public function handlerSwitchContext($contexts_hosts)
        {
            global $modx;

            $switch_context = false; // Надо ли изменить контекст

            // Домен ли это или поддомен
            if ($contexts_hosts[$_SERVER['HTTP_HOST']]) {
                $domain = $_SERVER['HTTP_HOST'];
                $context_key = $contexts_hosts[$domain];

                $switch_context = true;
            } else {
                $host_explode = explode('.', $_SERVER['HTTP_HOST']);
                $subdomain = $host_explode[0];

                $domain = str_replace($subdomain . '.', '', $_SERVER['HTTP_HOST']);
                $context_key = $contexts_hosts[$domain];

                // Проверяем существует ли по данному поддомену файл с данными
                if (file_exists(__DIR__ . "/data/global/$context_key/$subdomain.json")) {
                    $switch_context = true;
                }
            }

            if ($switch_context) {
                $modx->switchContext($context_key);
                return [
                    'context_key' => $context_key,
                    'subdomain' => $subdomain
                ];
            } else {
                header("HTTP/1.1 400 Bad Request");
                exit;
            }
        }

        /**
         * Получает по текущему контексту + домену/поддомену - данные для записи в глобальный плейсхолдер
         * @param mixed $context_key
         * @param mixed $subdomain
         */
        public function getCurrentContextGlobalData($context_key, $subdomain = null)
        {
            $globaldata_path = [
                "default" => __DIR__ . "/data/global/$context_key/_default.json"
            ];

            if ($subdomain) {
                $globaldata_path["current"] = __DIR__ . "/data/global/$context_key/$subdomain.json";
            }

            $globaldata = [];
            foreach ($globaldata_path as $key => $path) {
                if (!file_exists($path)) continue;

                $content = file_get_contents($path);
                if ($content !== false) {
                    $globaldata[$key] = get_object_vars(json_decode($content));
                }
            }

            // Объеденяем данные поддомена с дефолтными
            if (!empty($globaldata["current"])) {
                $globaldata = array_replace_recursive($globaldata["default"], $globaldata["current"]);
            } else {
                $globaldata = $globaldata['default'];
            }

            return $globaldata;
        }

        /**
         * Получает данные для помены на странице. Используется в плагине plugins/VirtualRouter.php
         * @param mixed $context_key
         */
        public function getChangesData($context_key)
        {
            global $modx;

            $changes_file = __DIR__ . "/data/changes/$context_key.php";
            if (file_exists($changes_file)) {
                $callback = include $changes_file;
                if (is_callable($callback)) {
                    $globaldata = $modx->getPlaceholder($this->placeholder_key);
                    return $callback($globaldata);
                }
            }

            return [];
        }
    }
}

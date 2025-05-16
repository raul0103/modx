<?php

if (!class_exists('MainHandler')) {

    class MainHandler
    {
        public $cache;
        public $main_data;

        public function __construct($cache)
        {
            $this->cache = $cache;
            $this->main_data = [
                "context_key" => null,
                "domain" => null,
                "subdomain" => [
                    "value" => null,
                    "redirect_context_key" => null // Редирект на контекст (если будет в конфиге контекста)
                ],
            ];
        }

        /**
         * Получение списка доменов на сайте через насройку контекста http_host
         * По этим данным мы безошибочно определим на какой котекст ссылается URL
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
         * Формирует основные данные для начала работы
         * @param mixed $contexts_hosts
         */
        public function getMainData()
        {
            $contexts_hosts = $this->getContextsHost();

            if ($contexts_hosts[$_SERVER['HTTP_HOST']]) {
                $this->main_data['domain'] = $_SERVER['HTTP_HOST'];
                $this->main_data['context_key'] = $contexts_hosts[$this->main_data['domain']];
            } else {
                $host_explode = explode('.', $_SERVER['HTTP_HOST']);

                $this->main_data['subdomain']['value'] = $host_explode[0];
                $this->main_data['domain'] = str_replace($this->main_data['subdomain']['value'] . '.', '', $_SERVER['HTTP_HOST']);
                $this->main_data['context_key'] = $contexts_hosts[$this->main_data['domain']];
            }

            return $this->main_data;
        }

        /**
         * Подмена контекста.
         * 
         * + Работа с конфигом контекста для опеределния:
         *    - Забанненые поддомены
         *    - Редиректы поддомена на другой контекст
         * 
         * + Получения глобальных данных по региону и городу
         * 
         * @param mixed $GlobalProvider
         */
        public function switchContext($GlobalProvider)
        {
            global $modx;

            $context_config = include __DIR__ . "/config/contexts/{$this->main_data['context_key']}.php";

            // Если сайт загружен без поддомена - меняем контекст
            if (!$this->main_data['subdomain']['value']) {
                $switch_context = true;
            } else {
                // Забанен ли поддомен в текущем контексте
                $banned_list = (array)$context_config['subdomains_banned'] ?: [];
                if (!empty($banned_list) && in_array($this->main_data['subdomain']['value'], $banned_list)) {
                    $this->abort();
                }
                // Есть ли редиректы
                $this->main_data['subdomain']['redirect_context_key'] = $context_config['subdomains_redirect_to_context'][$this->main_data['subdomain']['value']];

                // Есть ли файл с данными по поддомену
                $filename = $this->main_data['subdomain']['value'] . ".json";
                $data = $GlobalProvider->getRegionAndCityData($filename);
                if ($data) {
                    $switch_context = true;
                }
            }

            if ($switch_context) {
                $modx->switchContext($this->main_data['subdomain']['redirect_context_key'] ?: $this->main_data['context_key']);
            } else {
                $this->abort();
            }
        }

        public function abort()
        {
            header("HTTP/1.1 400 Bad Request");
            exit;
        }
    }
}

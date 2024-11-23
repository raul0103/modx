<?php

if (!class_exists('sfCache')) {
    class sfCache
    {
        public $params;

        public function get($dirname, $filename)
        {
            global $modx;

            $this->params($dirname, $filename);
            return $modx->cacheManager->get($this->params['name'], $this->params['options']);
        }
        public function set($dirname, $filename, $data)
        {
            global $modx;

            $this->params($dirname, $filename);
            $modx->cacheManager->set($this->params['name'], $data, 0, $this->params['options']);
        }

        public function params($dirname, $filename = null)
        {
            global $scriptProperties, $modx;
            $this->params = [
                'name' => $filename ?: md5(serialize($scriptProperties)),
                'options' => [
                    xPDO::OPT_CACHE_KEY => "default/simple-filter/$dirname/" . $modx->context->key . '/',
                ]
            ];
        }
    }
}

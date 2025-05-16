<?php

if (!class_exists('GlobalProvider')) {
    class GlobalProvider
    {
        public $global_data;
        public $global_data_placeholder_key;
        public $directory;

        public function __construct()
        {
            // Глобальные данные. Расположение данных опредляет их каскадное объединение
            $this->global_data = [
                "common" => [], // Основные данные для всего сайта
                "context" => [], // Данные на контекст
                "region" => [], // Данные по региону/Области
                "city" => [] // Данные города
            ];

            $this->global_data_placeholder_key = "virtual-router";
            $this->directory = dirname(dirname(__DIR__)) . "/data/global/";
        }

        /**
         * Определяем:
         * - Данные по региону 
         * - Данные по поддомену/городу
         * 
         * @param mixed $directory
         * @param mixed $filename
         */
        public function getRegionAndCityData($filename)
        {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->directory . "regions/")
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getFilename() === $filename) {
                    $region_path = $file->getPath();

                    $content = file_get_contents("$region_path/_default.json");
                    $this->setData('region', $content);

                    $content = file_get_contents($file);
                    $this->setData('city', $content);

                    return true;
                }
            }

            return false;
        }

        public function getCommonData()
        {
            $content = file_get_contents($this->directory . "/common/data.json");
            $this->setData('common', $content);
        }

        public function getContextData($context_key)
        {
            $content = file_get_contents($this->directory . "/contexts/$context_key.json");
            $this->setData('context', $content);
        }

        /**
         * Summary of setDataToPlaceholder
         * @param mixed $modified_global_data - Данные можно передать из вне, например после подмены глобальных данных из-за UTM
         */
        public function setDataToPlaceholder($modified_global_data = null)
        {
            global $modx;

            $result = [];
            foreach ($modified_global_data ?: $this->global_data as $data) {
                $result = array_replace_recursive($result, $data);
            }

            $modx->setPlaceholder($this->global_data_placeholder_key, $result);
        }

        public function getDataPlaceholder()
        {
            global $modx;

            return $modx->getPlaceholder($this->global_data_placeholder_key);
        }

        public function setData($key, $content)
        {
            if ($content) {
                $data = json_decode($content, true);
                $this->global_data[$key] = $data;
            }
        }
    }
}

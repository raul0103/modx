<?php

/**
 * Класс для работы с пакетом QueueCallback
 */

class QC
{
    public $modx;
    public $callback_delay = 300; // Задержка перед отправкой сообщения, в секундах

    public function __construct()
    {
        $this->initModx();
        $this->initPackage();
    }

    public function initModx()
    {
        global $modx;
        if (!$modx) {
            define('MODX_API_MODE', true);
            if (!$path = $_SERVER['DOCUMENT_ROOT']) {
                $path = dirname(__DIR__, 5);
            }
            include $path . '/index.php';
        }

        $this->modx = $modx;
    }

    public function initPackage()
    {
        $packageName = 'queue_callback';
        $path = MODX_CORE_PATH . 'elements/modules/' . $packageName . '/model/';
        $this->modx->addPackage($packageName, $path);
    }

    /**
     * Поиск уже существующей НЕ ОТПРАВЛЕННОЙ записи и ее обновление
     * Если запись найдена и обновлена return true
     * 
     * @param mixed $new_data  - Сообщение которое будет отправлено
     * @return boolean
     */
    public function updateRecord(array $new_data)
    {
        $session = $_REQUEST['PHPSESSID'] ?: $_COOKIE['PHPSESSID'];

        $find_record = $this->modx->getObject('QueueCallback', [
            'session' => $session,
            'sent' => 0
        ]);
        if ($find_record) {
            $data = $find_record->get('data');
            $data = json_decode($data, true);

            // Объеденили старые данные и новые
            $this->deepMerge($data,  $new_data);

            $find_record->set('data', json_encode($data, JSON_UNESCAPED_UNICODE));
            $find_record->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Создание записи
     * @param mixed $url [string] - адрес куда будет отправка 
     * @param mixed $data [json] - Сообщение которое будет отправлено
     * @return void
     */
    public function setRecord($url, $data)
    {
        // ID сессии пользователя
        $session = $_REQUEST['PHPSESSID'] ?: $_COOKIE['PHPSESSID'];

        $record = $this->modx->newObject('QueueCallback');
        $record->fromArray([
            'session' => $session,
            'url' => $url,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        ]);

        if (!$record->save()) $this->modx->log(modX::LOG_LEVEL_ERROR, '[QC] - Ошибка при создании записи');
    }

    public function callback()
    {
        // Получаем неотправленные записи
        $records = $this->modx->getCollection('QueueCallback', ['sent' => 0]);

        foreach ($records as $record) {
            // >>> Пора ли отправляеть сообщение
            $date = strtotime($record->get('date'));
            $now = time();
            if (($now - $date) < $this->callback_delay) continue;
            // <<<

            $data = $record->get('data');
            $data = json_decode($data, true);
            $data = http_build_query($data);

            //>>> Отправка запроса
            $url = "{$record->url}?$data";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            if ($response === false) {
                $error = curl_error($curl);
                $record->set('message', "Ошибка отправки сообщения: $error");
                $record->save();
            } else {
                $record->set('sent', 1);
                $record->save();
            }
            curl_close($curl);
            // <<<
        }
    }

    // Объединение глубокого массива
    public function deepMerge(array &$original, array $updates)
    {
        foreach ($updates as $key => $value) {
            if (is_array($value) && isset($original[$key]) && is_array($original[$key])) {
                $this->deepMerge($original[$key], $value); // рекурсивно сливаем подмассивы
            } else {
                $original[$key] = $value; // перезаписываем или добавляем значение
            }
        }
    }
}

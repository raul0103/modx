<?php

class UserTicketsUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'UserTickets'; // Указываем, с какой моделью работаем
    public $languageTopics = array('usershop:default'); // Добавляем языковые темы, если нужно
    public $objectType = 'ticket'; // Тип объекта

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function beforeSave()
    {
        // Получаем данные из запроса
        $adminResponse = $this->getProperty('admin_response');
        $this->setProperty('admin_response', $adminResponse);

        return parent::beforeSave();
    }
}

return 'UserTicketsUpdateProcessor';

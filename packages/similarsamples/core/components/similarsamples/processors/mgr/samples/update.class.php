<?php

class SamplesUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'SSSamples'; // Указываем, с какой моделью работаем
    public $languageTopics = array('similarsamples:default'); // Добавляем языковые темы, если нужно
    public $objectType = 'review'; // Тип объекта

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('similarsamples', $this->modx->getOption('core_path') . 'components/similarsamples/model/');
        return parent::initialize();
    }

    public function beforeSave()
    {
        $name = $this->getProperty('name');

        $this->setProperty('name', $name);

        return parent::beforeSave();
    }
}

return 'SamplesUpdateProcessor';

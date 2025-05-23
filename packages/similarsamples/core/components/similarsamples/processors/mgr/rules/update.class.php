<?php

class RulesUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'SSRules'; // Указываем, с какой моделью работаем
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
        $context_key = $this->getProperty('context_key');
        $options = $this->getProperty('options');
        $categories = $this->getProperty('categories');

        $this->setProperty('name', $name);
        $this->setProperty('context_key', $context_key);
        $this->setProperty('options', $options);
        $this->setProperty('categories', $categories);

        return parent::beforeSave();
    }
}

return 'RulesUpdateProcessor';

<?php

class RulesCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'SSRules';
    public $languageTopics = array('similarsamples:default');
    public $objectType = 'rules';

    public function initialize()
    {
        $this->modx->addPackage('similarsamples', $this->modx->getOption('core_path') . 'components/similarsamples/model/');
        return parent::initialize();
    }

    public function beforeSave()
    {
        return parent::beforeSave();
    }
}

return 'RulesCreateProcessor';

<?php

class SampleCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'SSSamples';
    public $languageTopics = array('similarsamples:default');
    public $objectType = 'samples';

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

return 'SampleCreateProcessor';

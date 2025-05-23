<?php

class SamplesRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'SSSamples';
    public $languageTopics = array('similarsamples:default');
    public $objectType = 'rules';

    public function initialize()
    {
        $this->modx->addPackage('similarsamples', $this->modx->getOption('core_path') . 'components/similarsamples/model/');
        return parent::initialize();
    }

    // Дополнительная проверка, если нужно, перед удалением
    public function beforeRemove()
    {
        return parent::beforeRemove();
    }
}

return 'SamplesRemoveProcessor';

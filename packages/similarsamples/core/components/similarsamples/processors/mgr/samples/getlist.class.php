<?php
class SamplesGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'SSSamples';
    public $languageTopics = array('similarsamples:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'samples';

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('similarsamples', $this->modx->getOption('core_path') . 'components/similarsamples/model/');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('SSSamples', 'SSSamples'));

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }
}
return 'SamplesGetListProcessor';

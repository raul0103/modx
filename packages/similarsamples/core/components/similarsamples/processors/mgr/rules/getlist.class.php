<?php
class RulesGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'SSRules';
    public $languageTopics = array('similarsamples:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'rules';

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('similarsamples', $this->modx->getOption('core_path') . 'components/similarsamples/model/');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('SSRules', 'SSRules'));

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }
}
return 'RulesGetListProcessor';

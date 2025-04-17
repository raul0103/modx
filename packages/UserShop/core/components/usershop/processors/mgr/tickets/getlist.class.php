<?php

class UserTicketsGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'UserTickets';
    public $languageTopics = array('usershop:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'ticket';

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('UserTickets', 'UserTickets'));

        $c->leftJoin('modUser', 'User', 'User.id = UserTickets.user_id');
        $c->select(['username' => 'User.username']);

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }
}
return 'UserTicketsGetListProcessor';

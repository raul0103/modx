<?php
class UserDiscountGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'UserDiscount';
    public $languageTopics = array('usershop:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'discount';

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('UserDiscount', 'UserDiscount'));

        $c->leftJoin('modUser', 'User', 'User.id = UserDiscount.user_id');
        $c->select(['username' => 'User.username']);

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }
}
return 'UserDiscountGetListProcessor';

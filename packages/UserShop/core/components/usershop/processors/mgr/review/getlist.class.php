<?php
class OrderReviewGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'OrderReviews';
    public $languageTopics = array('usershop:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'review';

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('OrderReviews', 'OrderReviews'));

        $c->leftJoin('modUser', 'User', 'User.id = OrderReviews.user_id');
        $c->select(['username' => 'User.username']);

        $c->leftJoin('msOrder', 'Order', 'Order.id = OrderReviews.order_id');
        $c->select(['order_num' => 'Order.num']);

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        // Возвращаем данные в виде массива
        return $object->toArray();
    }
}
return 'OrderReviewGetListProcessor';

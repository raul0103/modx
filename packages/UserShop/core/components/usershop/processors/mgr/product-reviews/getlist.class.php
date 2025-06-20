<?php
class OrderReviewGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'UserProductReviews';
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
        $c->select($this->modx->getSelectColumns('UserProductReviews', 'UserProductReviews'));

        $c->leftJoin('modUser', 'User', 'User.id = UserProductReviews.user_id');
        $c->select(['username' => 'User.username']);

        $c->leftJoin('modResource', 'Product', 'Product.id = UserProductReviews.product_id');
        $c->select(['product_id' => 'Product.id', 'product_pagetitle' => 'Product.pagetitle']);

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        // Возвращаем данные в виде массива
        return $object->toArray();
    }
}
return 'OrderReviewGetListProcessor';

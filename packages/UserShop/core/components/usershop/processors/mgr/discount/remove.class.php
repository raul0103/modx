<?php

class UserDiscountRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'UserDiscount';
    public $languageTopics = array('usershop:default');
    public $objectType = 'discount';

    public function initialize()
    {
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    // Дополнительная проверка, если нужно, перед удалением
    public function beforeRemove()
    {
        return parent::beforeRemove();
    }
}

return 'UserDiscountRemoveProcessor';

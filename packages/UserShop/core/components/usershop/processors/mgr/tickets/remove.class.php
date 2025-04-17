<?php

class UserTicketsRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'UserTickets';
    public $languageTopics = array('usershop:default');
    public $objectType = 'ticket';

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

return 'UserTicketsRemoveProcessor';

<?php
// core/components/usershop/processors/mgr/discount/create.class.php

class UserDiscountCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'UserDiscount'; // Модель скидки
    public $languageTopics = array('usershop:default');
    public $objectType = 'discount';

    public function initialize()
    {
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function beforeSave()
    {
        $discount = $this->getProperty('discount');
        $userId = $this->getProperty('user_id');

        // Валидация скидки
        if ($discount < 0 || $discount > 100) {
            $this->addFieldError('discount', 'Скидка должна быть в пределах от 0 до 100.');
            return false;
        }

        // Валидация ID пользователя (не может быть пустым)
        if (empty($userId)) {
            $this->addFieldError('user_id', 'ID пользователя не может быть пустым.');
            return false;
        }

        // Проверка существования пользователя
        $user = $this->modx->getObject('modUser', $userId);
        if (!$user) {
            $this->addFieldError('user_id', 'Пользователь с таким ID не существует.');
            return false;
        }

        // Проверка, не добавлена ли уже скидка для этого пользователя
        $existingDiscount = $this->modx->getCount('UserDiscount', array('user_id' => $userId));
        if ($existingDiscount > 0) {
            $this->addFieldError('user_id', 'Для этого пользователя уже существует скидка.');
            return false;
        }

        return parent::beforeSave();
    }
}

return 'UserDiscountCreateProcessor';

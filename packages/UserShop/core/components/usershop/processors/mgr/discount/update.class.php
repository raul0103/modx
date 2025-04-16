<?php
// core/components/usershop/processors/mgr/review/update.class.php

class UserDiscountUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'UserDiscount'; // Указываем, с какой моделью работаем
    public $languageTopics = array('usershop:default'); // Добавляем языковые темы, если нужно
    public $objectType = 'review'; // Тип объекта

    public function initialize()
    {
        // Регистрируем модель
        $this->modx->addPackage('usershop', $this->modx->getOption('core_path') . 'components/usershop/model/');
        return parent::initialize();
    }

    public function beforeSave()
    {
        // Получаем значение скидки
        $discount = $this->getProperty('discount');

        // Валидация: скидка должна быть от 0 до 100
        if ($discount < 0 || $discount > 100) {
            $this->addFieldError('discount', 'Скидка должна быть в пределах от 0 до 100.');
            return false;
        }

        // Если скидка в пределах допустимого диапазона, сохраняем
        $this->setProperty('discount', $discount);

        return parent::beforeSave();
    }
}

return 'UserDiscountUpdateProcessor';

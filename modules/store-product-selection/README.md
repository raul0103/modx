- Вызвать в начале страницы сниппет setPlaceholder.php, для записи массива ID товаров
- Обязательно передать в сниппет cookie_key. По нему можно сохранять ID для любых целей
- В дальнешем можно проверкой ID в массиве манипулировать ресурсами

## Элементы для товара

- Кнопка для добавления товара в избранное
  - {include "file:modules/store-product-selection/chunks/favorites/btn.tpl" product_id=$id}
- Счетчик избранных товаров
  - {include "file:modules/store-product-selection/chunks/favorites/counters.tpl"}

## Особые моменты

- При AJAX подгрузке данных, необходимо снова формировать плейсхолдер с данными через setPlaceholder.php. Как вариант в чанке товара проверять если нет плейсхолдера то вызвать setPlaceholder.php

## Зависмости

- Модуль notifications js/modules/notifications.js. Подключается в frontend/js/services/notification-service.js
- "../../../../../src/js/classes/Cookie";

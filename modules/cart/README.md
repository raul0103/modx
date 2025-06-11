# Модуль корзины для minishop2

- Фронт передает данные по товару (цена, ID, единица измерения) на бэк. Таким образом можно динамически менять единицы измерения, цены и в корзине будут данные по выбранной еденице
  - AJAX запросы летят на `snippets/ajax.php` который перенаправляет на процессоры `core/elements/modules/cart/backend/processors`
  - Решил делать через сниппет `ajax.php` а не через коннектор, так как экземпляр $modx в любом случае будет получен и не ннужно возиться и подключать его.
- Вся логика расчета в `processors/`
- Корзина хранится в сессиях

## FRONTEND

- `frontend/chunks/cart-product-controls.tpl`
  - Кнопки для регулирования товара в корзине
  - Каждая кнопка вызывает submit формы в которой находится.
  - Форма получает необходимые поля и передает на бэк

```html
<input type="hidden" name="id" value="PRODUCT_ID" />
<input type="hidden" name="price" value="PRODUCT_PRICE" />
<input type="hidden" name="unit" value="PRODUCT_UNIT" />
```

- `backend/snippets/getCartTotal.php` - Данные о текущем кол-ве товаров и их сумме можно получить сниппетом

- Обновление данных на фронте

  - `data-cart-total-count` - Обновление общего вол-ва товара в корзине
  - `data-cart-total-hide-empty="true"` - Если кол-во товаров 0 то элемент будет скрыт

  - `data-cart-total-summ` - Для обвноления общей суммы
  - `data-cart-total-summ-old` - Для обвноления общей СТАРОЙ суммы
  - `data-cart-product-summ="PRODUCT_ID"` - Обновление суммы по товару

- Глобальные события для управления корзиной

```js
cart.events.clear(); // Полностью очистит корзину
cart.events.remove(PRODUCT_ID); // Удалит товар из корзины
cart.events.minishopCreateOrder(); // Создаст заказ в minishop2
```

## BACKEND

- `backend/classes` - Основные классы для работы с корзиной. Могут использоваться и в сниппетах
- `backend/console` - Скрипты для запуска в консоли. Пока там установка нужных опций
- `backend/processors` - Основные процессоры для работы с изменением кол-ва товаров в корзине
- `backend/snippets` - Сниппеты
- `snippets/getProductData.php` - Отдает на страницу по PRODUCT_ID Данные по товару в корзине
- `snippets/getProducts.php` - Отдает список товаров в корзине

```php
{set $products = "@FILE modules/cart/backend/snippets/getProducts.php" | snippet : [
    'options' => [
        'option-1' => 'razmer-mm',
        'option-2' => 'obem-m3',
    ]
]}
```

- `snippets/getCartTotal.php` - Отдает на страницу общее кол-во позиций в корзине и общую сумму

## Установка

- Запустить `backend/console/setOptions.php`
  - Укажите верные параметры
  - Создаст необходимые настройки для работы
  - Можно создать вручню
- Вызвать `{'@FILE modules/cart/backend/snippets/ajax.php' | snippet}` в начале страницы
- В карточках товара вызывать `{include "file:modules/cart/frontend/chunks/cart-controls-default.tpl"}`

## Опции необходимые для работы скрипта

- `cart_module_path` - Путь до модуля корзины
  - По умолчанию `/core/elements/modules/cart/`
  - Можно установить через `console/setOptions.php`

## Зависмости

- Модуль notifications js/modules/notifications.js. Подключается в frontend/js/services/notification-service.js

## Обновления

- Добавли функционал для работы с корзиной минишопа `core\elements\modules\cart\backend\processors\minishoporder.class.php`

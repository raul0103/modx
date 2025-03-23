/**
 * Работает через модуль js/modules/notifications.js
 *
 * @param {*} action - Событие корзины
 */
export default function notificationService(action, product_count = null) {
  if (!notifications) return;

  let messages = {
    added: "Товар добавлен в корзину",
    update: "Количество товара в корзине изменено: ",
    remove: "Товар удален из корзины",
    clear: "Все товары удалены из корзины",
  };

  if (action == "plus") {
    if (product_count == 1) {
      notifications.success(messages.added);
    } else {
      notifications.success(messages.update + product_count);
    }
  } else if (["minus", "change", "remove"].indexOf(action) > -1) {
    if (!product_count || product_count == 0) {
      notifications.warning(messages.remove);
    } else {
      notifications.success(messages.update + product_count);
    }
  } else if (action == "clear") {
    notifications.warning(messages.clear);
  }
}

/**
 * Запускает событие при действиях с корзиной
 * @param {*} detail - Параметры
 */
export default function dispatch(detail) {
  let cart_event = new CustomEvent("CartEvent", detail);
  document.dispatchEvent(cart_event);
}

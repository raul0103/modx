/**
 * Скрипт управляет кнопками для добавления в избранное
 * Создаст глобальную функцию window.addProductSelection и window.deleteProductSelection, указать ее на кнопке onclick="addProductSelection(this,resource_id)", так как товары будут подгружать по ajax
 *
 * Аттрибуты необходимые для работы
 * data-selection-key="cookie_key" - Кол-во сохраненных товаров , можно установить на несколько элементов
 *    - Указываем в аттрибуте cookie_key для разделения, вдруг будут избранные товаров, статей и тд
 */
import Cookie from "./Cookie";

class ProductSelection {
  cookie = new Cookie();
  find_elements = {};

  init() {
    window.addProductSelection = this.addProductSelection;
    window.deleteProductSelection = this.deleteProductSelection;
  }

  /**
   * Добавит ID ресурса в куки избранного
   * @param {DOMElement} target - кнопка по которой было нажатие
   * @param {integer} resource_id - ID ресурса
   * @param {string} cookie_key - Ключ для куки куда будет сохранен результат
   * @param {object} addition_notification - сообщения
   * @returns
   */
  addProductSelection = (
    target,
    resource_id,
    cookie_key,
    addition_notification = {
      warning: "Товар удален",
      success: "Товар добавлен",
    }
  ) => {
    if (!cookie_key) {
      console.warn("[addProductSelection] Не задан cookie_key");
      return;
    }

    resource_id = +resource_id;

    let ids = this.cookie.get(cookie_key);
    if (ids) ids = ids.split(",").map(Number);
    else ids = [];

    if (ids.indexOf(resource_id) > -1) {
      ids = ids.filter((id) => id != resource_id);
      target.classList.remove("active");
      if (notifications) notifications.warning(addition_notification.warning);
    } else {
      ids.push(resource_id);
      target.classList.add("active");
      if (notifications) notifications.success(addition_notification.success);
    }

    this.cookie.set(cookie_key, ids);

    this.updateCounts(cookie_key, ids.length);
  };

  /**
   *
   * @param {string} cookie_key
   */
  deleteProductSelection = (cookie_key) => {
    this.cookie.delete(cookie_key);
    location.reload();
  };

  /**
   *
   * @param {string} cookie_key - Ключ для куки куда будет сохранен результат
   * @param {integer} count - кол-во ресурсов в избранном
   */
  updateCounts(cookie_key, count) {
    // Что-бы не бегать по странице скриптом при каждом нажатии
    if (!this.find_elements.counters) {
      this.find_elements.counters = {};
    }

    let counter_elements;
    if (this.find_elements.counters[cookie_key]) {
      counter_elements = this.find_elements.counters[cookie_key];
    } else {
      counter_elements = document.querySelectorAll(
        `[data-selection-key="${cookie_key}"]`
      );
      this.find_elements.counters[cookie_key] = counter_elements;
    }

    this.find_elements.counters[cookie_key].forEach((counter_element) => {
      counter_element.textContent = count;

      if (counter_element.dataset.totalHideEmpty == "true") {
        if (count < 1) {
          counter_element.classList.add("hidden");
        } else {
          counter_element.classList.remove("hidden");
        }
      }
    });
  }
}

export default function initProductSelection() {
  let product_selection = new ProductSelection();
  product_selection.init();
}

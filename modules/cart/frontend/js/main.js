/**
 * Класс управления корзиной
 */
import api from "./services/api";
import dispatch from "./utils/dispatch";
import update_elements from "./modules/update-elements";
import formDataSerialize from "./modules/form-data-serialize";
import notificationService from "./services/notification-service";

class Cart {
  /**
   * Основная фукнция на событие изменения товара в корзине
   */
  submit = async (event) => {
    event.preventDefault();

    // Элемент который отправил форму
    let submitter = event.submitter ?? event.target;
    let form = submitter.form;

    let data = formDataSerialize(form);

    // [minus|change|plus]
    let action = submitter.dataset.cartEvent;
    // Если это input с кол-вом тогда устанавливаем кол-во его значение
    if (action == "change") {
      data.count = submitter.value;
    }

    this.states.loading(form);

    let response = await api.response(action, data);
    if (!response && !response?.success) return;

    this.states.completion(form);

    // Обновляем элементы на странице
    update_elements.productSumm(response.data);
    update_elements.cartTotalSumm(response.data);
    update_elements.cartTotalCount(response.data);
    update_elements.productCount(response.data);

    // Уведомление
    notificationService(
      action,
      response.data && response.data.product_data.count
    );

    // Событие что корзина сработала
    dispatch({
      detail: {
        action,
        product_data: response.data.product_data,
      },
    });
  };

  events = {
    // Полностью очищает корзину
    clear: async (notification = true) => {
      let action = "clear";
      await api.response(action, {});

      // Уведомление
      if (notification) notificationService(action);
    },
    // Удаляет товар из корзины
    remove: async (product_id) => {
      let action = "remove";

      let response = await api.response(action, {id: product_id});
      if (!response && !response?.success) return;

      // Уведомление
      notificationService(action);

      update_elements.cartTotalSumm(response.data);
      update_elements.cartTotalCount(response.data);
    },
    minishopCreateOrder: async (product_data) => {
      await api.response("minishoporder", product_data);
    },
  };

  states = {
    loading: (e) => {
      if (!e) return;
      e.style.opacity = ".5";
      e.style["pointer-events"] = "none";
    },
    completion: (e) => {
      if (!e) return;
      e.style.opacity = "1";
      e.style["pointer-events"] = "all";
    },
  };
}

export default function initCart() {
  window.cart = new Cart();
}

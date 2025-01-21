import Storage from "./Storage";
import helpers from "./helpers";

/**
 * Класс для установки событий на DOm элементы модального окна
 */
class ModalEvents {
  constructor() {
    // Элементы на которых будут события
    this.event_elements = [
      {
        selector: "#ccm-confirm-btn", // Кнопка подтверждения города
        function_name: "confirmCity",
      },
      {
        selector: "#ccm-city-list-btn", // Кнопка для открытия другого модального окна со списком городов
        function_name: "openCityListModal",
      },
      {
        selector: "#ccm-modal-close-btn", // Кнопка закрытия модалки со списком городов
        function_name: "closeCityListModal",
      },
      {
        selector: "#ccm-modal-city-list-overlay", /// Задний фон отрытой модалки списка городов, для клика и закрытия
        function_name: "closeCityListModal",
      },
      {
        selector: "[data-city-subdomain]", // Кнопка выбранного города
        function_name: "changeCity",
      },
    ];

    // Селекторы к другим элементам модального окна
    this.selectors = {
      modal_changer: "#ccm-modal-changer", // Модальное окно для подтверждения города
      modal_city_list: "#ccm-modal-city-list", // Модальное окно со списком городов
    };

    this.storage = new Storage();
  }

  /**
   * @param {HTMLElement} modals - Элемент DOM.
   */
  setEvents(modals) {
    this.modals = modals; // Что бы использовать в функциях

    this.event_elements.forEach((event_element) => {
      let find_elements = modals.querySelectorAll(event_element.selector);
      if (!find_elements.length) return;

      find_elements.forEach((find_element) => {
        find_element.addEventListener("click", (e) =>
          this[event_element.function_name](e)
        );
      });
    });
  }

  /**
   * Город подтвержден
   * - Получаем из HTML модалки все доступные варианты городов
   * - Получаем ранее определенный город пользователя
   * - Находим по списку городов город пользователя для получения поддомена
   * - Если есть поддомен то перенаправить на него при нажатии на кнопку
   */
  confirmCity() {
    // Находим все города и поддомены по ним
    let city_list = helpers.getCityList(this.modals);
    // Получаем город пользователя
    let user_city = this.storage.get("user-city");
    // Проверяем если город пользователя есть в списке то отдаем на редирект поддомен этого города
    let find_user_city = city_list.find(
      (city_item) => city_item.city_name == user_city
    );
    let city_subdomain = find_user_city ? find_user_city.city_subdomain : null;
    this.changeCity(null, city_subdomain);
  }

  /**
   * - Открывает модалку со списком городов
   * - Удаляет модалку с подтверждением города
   */
  openCityListModal() {
    let modal_changer = this.modals.querySelector(this.selectors.modal_changer);
    let modal_city_list = this.modals.querySelector(
      this.selectors.modal_city_list
    );

    if (modal_changer) modal_changer.classList.add("hide");
    if (modal_city_list) modal_city_list.classList.add("show");
  }

  closeCityListModal() {
    this.modals.remove();
  }

  /**
   * Функция управляет выбором города
   * - Записать в storage что город выбран
   * - Сформировать новую ссылку
   * - Редирект
   * @param {*} event
   * @param {string} city_subdomain - Поддомен выбранного города
   */
  changeCity(event = null, city_subdomain = null) {
    if (!city_subdomain && event) {
      city_subdomain = event.target.dataset.citySubdomain;
    }

    // Получаем текущий URL
    let current_url = new URL(location.href);

    let domain = helpers.getDomain();

    // Создаем новый URL с поддоменом
    let new_url;
    if (city_subdomain) {
      new_url = `${current_url.protocol}//${city_subdomain}.${domain}${current_url.pathname}`;
    } else {
      new_url = `${current_url.protocol}//${domain}${current_url.pathname}`;
    }

    this.storage.set(true, domain);

    if (new_url !== location.href) {
      location.href = new_url;
    } else {
      this.modals.remove();
    }
  }
}

export default ModalEvents;

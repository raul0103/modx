import Ajax from "./Ajax";
import Storage from "./Storage";
import ModalEvents from "./ModalEvents";
import Api from "./Api";

class CityChanger {
  constructor() {
    this.storage_key = "city-changer";

    this.api = new Api();
    this.ajax = new Ajax();
    this.storage = new Storage();
    this.modalEvents = new ModalEvents();

    this.selectors = {
      modal_container: ".header__wrapper", // Контейнер куда будет помещена модалка, если нет то в боди
    };
  }

  init() {
    let is_city_selected = this.storage.get();
    if (is_city_selected) return;

    this.ajax.getHTMLModals().then((html_modals) => {
      this.addHTMLModalsToPage(html_modals);
    });
  }

  /**
   * Добавление модалки на страницу
   * - Привязка событий на элементы
   * - Добавление названия города пользователя в модалку
   *
   * @param {string} html_modals
   */
  async addHTMLModalsToPage(html_modals) {
    if (!html_modals) return;

    let div = document.createElement("div");
    div.innerHTML = html_modals;

    await this.addUserCityNameToModal(div);
    this.modalEvents.setEvents(div);

    let modal_container = document.querySelector(
      this.selectors.modal_container
    );

    if (modal_container) {
      // Родительскому элементу обязательно приходится ставить такие стили для правильного отображения, иначе модалка спрячется за элементы с любым z-index
      modal_container.style["z-index"] = 999;
      modal_container.style.positio = "static";
      modal_container.appendChild(div);
    } else {
      div.style["z-index"] = 999;
      div.style.positio = "static";
      document.querySelector("body").appendChild(div);
    }
  }

  async addUserCityNameToModal(modals) {
    let elem = modals.querySelector("#ccm-city-name");
    if (elem) elem.innerHTML = await this.api.getUserCity();
  }
}

export default CityChanger;

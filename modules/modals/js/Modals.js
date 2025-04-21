/**
 * Управление модальными окнами
 *
 * Вторичные элементы
 *  - data-modal-body-before - Блок который отображается до отправки формы
 *  - data-modal-body-after - Блок после отправки формы
 */
export default class Modals {
  constructor() {
    this.selectors = {
      close: "[data-modal-close]",
      show_body_before: "[data-modal-body-before]",
      show_body_after: "[data-modal-body-after]",
    };

    this.events_init = {}; // События которые уже определены
    this.current_opened = null; // Текущая открая модалка
  }

  events = {
    /**
     *
     * @param {*} modal_id
     * @param {Function} close_callback - Передаем открытому окну калбек. Например после оформления заказа, по закрытии модалки произойдет перезагрузка странциы и все товары удалятся
     * @returns
     */
    open: (modal_id, close_callback) => {
      if (!modal_id) return;

      let modal = document.getElementById(modal_id);
      if (!modal) return;

      modal.classList.add("opened");

      this.current_opened = modal;

      // Записали для каких модалок уже была инициализация события
      if (!this.events_init[modal_id]) {
        this.events_init[modal_id] = true;
        this.events.close(modal, close_callback);
      }
    },
    close: (modal, close_callback) => {
      let modal_close_btns = modal.querySelectorAll(this.selectors.close);
      modal_close_btns.forEach((modal_close_btn) => {
        modal_close_btn.addEventListener("click", () => {
          modal.classList.remove("opened");

          if (close_callback) {
            close_callback();
          }
        });
      });
    },
    closeCurrent: () => {
      if (!this.current_opened) return;
      this.current_opened.classList.remove("opened");
      this.current_opened = null;
    },
  };

  // Показывает контент из data-modal-body-after
  showBodyAfter() {
    let modal = this.current_opened;
    if (!modal) return;

    let show_body_before = modal.querySelector(this.selectors.show_body_before);
    if (!show_body_before) return;

    let show_body_after = modal.querySelector(this.selectors.show_body_after);
    if (!show_body_after) return;

    show_body_before.classList.add("hide");
    show_body_after.classList.add("show");
  }

  // Показывает контент из data-modal-body-before
  showBodyBefore() {
    let modal = this.current_opened;
    if (!modal) return;

    let show_body_before = modal.querySelector(this.selectors.show_body_before);
    if (!show_body_before) return;

    let show_body_after = modal.querySelector(this.selectors.show_body_after);
    if (!show_body_after) return;

    show_body_before.classList.remove("hide");
    show_body_after.classList.remove("show");
  }
}

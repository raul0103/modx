export default class Notifications {
  constructor() {
    this.container = document.getElementById("notifications-container");
    if (!this.container) {
      this.container = document.createElement("div");
      this.container.id = "notifications-container";
      document.body.appendChild(this.container);
    }
  }

  show(message, type = "success") {
    const notification = document.createElement("div");
    notification.classList.add("notification", type);

    notification.innerText = message;

    // Добавляем уведомление в контейнер
    this.container.appendChild(notification);

    // Показать уведомление
    setTimeout(() => {
      notification.classList.add("show");
    }, 10);

    // Удалить уведомление через 3 секунды
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300); // Ждем пока завершится анимация скрытия
    }, 3000);
  }

  success(message) {
    this.show(message, "success");
  }

  error(message) {
    this.show(message, "error");
  }

  warning(message) {
    this.show(message, "warning");
  }
}

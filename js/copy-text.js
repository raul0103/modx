// Вызывать через onclick="copyText(event,value)"

window.copyText = (event, value = null) => {
  let element = event.target;

  // Создаем временное текстовое поле
  let tmp_input = document.createElement("textarea");
  tmp_input.value = value || element.innerText;

  // Добавляем временное поле в документ
  document.body.appendChild(tmp_input);

  // Выделяем текст в поле
  tmp_input.select();
  tmp_input.setSelectionRange(0, 99999); // Для мобильных устройств

  // Копируем текст
  document.execCommand("copy");

  // Удаляем временное поле
  document.body.removeChild(tmp_input);

  notifications.success("Текст скопирован");
};

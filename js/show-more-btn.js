// Примеры вызова:
// 1. onclick="showMoreElement('[data-projects-item]',3)" - Покажет по 3 элемента за нажатие
// 2. onclick="showMoreElement('[data-review-item]',8, this)"
const sm_config = {
  hidden_class: "hidden",
  hiddent_text: "Скрыть",
  target_text_content: {},
};
window.showMoreElement = (attr, limit, target) => {
  const all_elements = document.querySelectorAll(attr);
  const hidden_elements = Array.from(all_elements).filter((elem) =>
    elem.classList.contains(sm_config.hidden_class)
  );

  if (hidden_elements.length > 0) {
    hidden_elements.forEach((element, i) => {
      if (i < limit) {
        element.classList.remove(sm_config.hidden_class);
      }
    });

    // Значит все элементы видны
    if (hidden_elements.length <= limit) {
      target.textContent = sm_config.hiddent_text;
    }
  } else {
    all_elements.forEach((element, i) => {
      if (i >= limit) {
        element.classList.add(sm_config.hidden_class);
      }
    });
    target.textContent = sm_config.target_text_content[attr];
  }

  if (!sm_config.target_text_content[attr])
    sm_config.target_text_content[attr] = target.textContent;
};

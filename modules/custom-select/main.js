/**
 * data-custom-select-btn="list" - кнопка открытия списка
 * data-custom-select-element="list" - список
 * data-custom-select-item - Элемент списка
 */

export default function initCustomSelect() {
  let btns = document.querySelectorAll("[data-custom-select-btn]");

  btns.forEach((btn) => {
    let key = btn.dataset.customSelectBtn;
    let dropdown = document.querySelector(
      `[data-custom-select-element="${key}"]`
    );

    // Открытие и закрытие выпадающего списка
    btn.addEventListener("click", () => {
      dropdown.classList.toggle("hidden");
    });

    // Выбор
    dropdown.addEventListener("click", (event) => {
      if (event.target.hasAttribute("data-custom-select-item")) {
        btn.textContent = event.target.textContent;
        dropdown.classList.add("hidden");
      }
    });

    // Закрытие списка при клике вне области
    document.addEventListener("click", (event) => {
      if (!event.target.closest(".custom-select")) {
        dropdown.classList.add("hidden");
      }
    });
  });
}

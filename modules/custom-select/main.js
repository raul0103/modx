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
    let input = btn.querySelector("input");
    let select = btn.querySelector("select"); // Настоящий селект который скрыт на странице

    // Открытие и закрытие выпадающего списка
    btn.addEventListener("click", () => {
      dropdown.classList.toggle("hidden");
      btn.classList.toggle("active");
    });

    // Выбор
    dropdown.addEventListener("click", (event) => {
      if (event.target.hasAttribute("data-custom-select-item")) {
        input.value = event.target.textContent.trim();

        if (select) {
          select.value = input.value;
          select.dispatchEvent(new Event("change", { bubbles: true }));
        }

        btn.classList.remove("active");
        btn.classList.add("selected");
        dropdown.classList.add("hidden");
      }
    });

    // Закрытие списка при клике вне области
    document.addEventListener("click", (event) => {
      if (!event.target.closest(".custom-select")) {
        dropdown.classList.add("hidden");
        btn.classList.remove("active");
      }
    });
  });
}

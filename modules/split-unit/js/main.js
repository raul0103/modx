/**
 * Поиск элементов с ценой производится по
 * data-split-unit-product
 * data-split-unit
 *
 * Внимательно с расположением элементов!
 * Поиск соседей, для привязки классов, производится по родителю
 */

export default function initSplitUnit() {
  // split_unit.events.activation
  window.split_unit = {};
  split_unit.events = {
    activation: (product_id, unit, btn) => {
      if (!unit || !product_id) {
        console.error("Не передан параметр product_id или unit");
        return;
      }

      let find_prices = document.querySelectorAll(
        `[data-split-unit-product="${product_id}"][data-split-unit="${unit}"]`
      );
      if (!find_prices.length) return;

      utils.deactiveElements(find_prices[0], "opened");
      utils.deactiveElements(btn, "active");
      btn.classList.add("active");

      find_prices.forEach((find_price) => {
        find_price.classList.add("opened");
      });
    },
  };

  let utils = {
    // Удаляет активный класс с прошлого элемента. Поиск по родителю
    deactiveElements(element, class_name) {
      let parent = element.parentNode;
      let find_elements = parent.querySelectorAll(`.${class_name}`);

      if (!find_elements.length) return;

      find_elements.forEach((find_element) => {
        find_element.classList.remove(class_name);
      });
    },
  };
}

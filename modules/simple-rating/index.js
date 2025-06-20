document.querySelectorAll("[data-rating-star]").forEach((star) => {
  star.addEventListener("click", function () {
    // Убираем класс active со всех звезд
    this.parentElement.querySelectorAll("[data-rating-star]").forEach((s) => {
      s.classList.remove("active");
    });

    // Добавляем класс active для выбранной звезды и предыдущих
    this.classList.add("active");
    let sibling = this.nextElementSibling;
    while (sibling) {
      sibling.classList.add("active");
      sibling = sibling.nextElementSibling;
    }
  });
});

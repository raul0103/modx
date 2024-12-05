window.generatePaginate = (container_id, callback, page = 1, total_pages) => {
  const max_visible = 5; // Максимальное число видимых страниц

  const container = document.getElementById(container_id);
  if (!container) return;

  container.innerHTML = ""; // Очистить контейнер перед добавлением

  // >>> Получение текущей страницы
  let current_page = parseInt(page, 10);
  // <<<

  // >>> Общее количество страниц
  if (!total_pages || total_pages < 1) return;
  // <<<

  const createPageItem = (page, isActive = false) => {
    const li = document.createElement("li");
    li.textContent = page;
    li.classList.toggle("active", isActive);

    li.addEventListener("click", () => callback(page));
    return li;
  };

  // >>> Диапазон видимых страниц
  const visibleStart = Math.max(1, current_page - Math.floor(max_visible / 2));
  const visibleEnd = Math.min(total_pages, visibleStart + max_visible - 1);

  // Если есть "Первая страница" вне диапазона
  if (visibleStart > 1) {
    container.appendChild(createPageItem(1)); // Первая страница
    if (visibleStart > 2) {
      const dots = document.createElement("li");
      dots.textContent = "...";
      dots.classList.add("dots");
      container.appendChild(dots); // "..."
    }
  }

  // Основные видимые страницы
  for (let i = visibleStart; i <= visibleEnd; i++) {
    container.appendChild(createPageItem(i, i === current_page));
  }

  // Если есть "Последняя страница" вне диапазона
  if (visibleEnd < total_pages) {
    if (visibleEnd < total_pages - 1) {
      const dots = document.createElement("li");
      dots.textContent = "...";
      dots.classList.add("dots");
      container.appendChild(dots); // "..."
    }
    container.appendChild(createPageItem(total_pages)); // Последняя страница
  }
};

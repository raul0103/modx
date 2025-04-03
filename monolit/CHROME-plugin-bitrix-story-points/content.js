// content.js (Скрипт расширения)
function addButton() {
  const button = document.createElement("button");
  button.innerText = "Установить Story Points";
  button.style.position = "fixed";
  button.style.top = "58px";
  button.style.right = "108px";
  button.style.zIndex = "9999";
  button.style.padding = "10px 15px";
  button.style.backgroundColor = "#007bff";
  button.style.color = "white";
  button.style.border = "none";
  button.style.borderRadius = "5px";
  button.style.cursor = "pointer";

  function injectScript(fn) {
    const script = document.createElement("script");
    script.textContent = `(${fn})();`;
    document.documentElement.appendChild(script);
    script.remove();
  }

  button.onclick = function () {
    // Передаем код, который будет выполняться в контексте страницы
    injectScript(() => {
      if (typeof BX !== "undefined" && BX.rest) {
        const match = location.href.match(/\/task\/view\/(\d+)/);
        if (match) {
          const taskId = match[1];
          const explanation =
            "📌 Что учитываем для оценки:\n\n" +
            "Для назначения Story Points задаче необходимо учесть 2 фактора:\n" +
            "🔹 Уровень сложности (1-10)\n" +
            "🔹 Планируемое время (1-8 часов)\n\n" +
            "🧩 Методология оценки сложности:\n" +
            "1-3 — механическая задача, не требующая творчества\n" +
            "4-6 — полу-механическая, требует специальных знаний\n" +
            "7-8 — умственная работа, требует опыта\n" +
            "9-10 — экспертная задача, требует стратегического мышления\n\n" +
            "Теперь введите количество Story Points:";

          const storyPoints = prompt(explanation, 0);
          if (storyPoints) {
            BX.rest.callMethod(
              "tasks.api.scrum.task.update",
              {
                id: taskId,
                fields: { storyPoints: storyPoints },
              },
              function (res) {
                alert("✅ Успех");
              }
            );
          }
        } else {
          alert("❌ Ошибка");
        }
      }
    });
  };

  let wrapper = document.getElementById("pagetitle-menu");
  if (wrapper) {
    wrapper.appendChild(button);
  } else {
    document.body.appendChild(button);
  }
}

addButton();

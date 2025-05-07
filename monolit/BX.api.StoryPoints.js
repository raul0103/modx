const storyPoints = prompt(
  "Введите новые Story Points:\n\nУровень сложности задачи от 1 до 10 \n\nПланируемое время потраченное на задачу (в часах) от 1 до 8 \n\nУровень сложности задачи: \n\n1-3 — задача механическая, не требующая включения творческого мышления \n\n4-6 — задача полу-механическая, но требующая каких-то специальных знаний или навыков \n\n7-8 — задача умственная; специалиста/руководителя, требующая специальных знаний и навыков \n\n9-10 — глубокая экспертная или стратегическая задача, требующая принятия сложных решений и особых компетенций / высоких полномочий, ответственности",
  0
);

const match = location.href.match(/\/task\/view\/(\d+)/);
if (match) {
  const taskId = match[1];
  BX.rest.callMethod(
    "tasks.api.scrum.task.update",
    {
      id: taskId,
      fields: {
        storyPoints: storyPoints,
      },
    },
    function (res) {
      if (res.answer.result) {
        alert("Story Points успешно обновлены");
      } else {
        alert("Ошибка при смене Story Points");
        console.log(res);
      }
    }
  );
} else {
  console.error("ID задачи не найден в ссылке");
}

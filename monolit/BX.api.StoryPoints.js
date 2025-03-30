const storyPoints = prompt("Введите новые Story Points:", 0);

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

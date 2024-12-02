const fs = require("fs");
const PurgeCSS = require("purgecss").PurgeCSS;

// Запуск PurgeCSS
new PurgeCSS()
  .purge({
    content: ["./core/elements/**/*.tpl"],
    css: ["./assets/templates/aspro_next/css/*.css"],
    safelist: ["class-to-keep"], // Исключить из удаления
  })
  .then((results) => {
    results.forEach((result) => {
      const originalCSS = fs.readFileSync(result.file, "utf-8"); // Исходный CSS
      const usedCSS = result.css; // CSS, который используется

      // Найти разницу между оригинальным и использованным CSS
      const unusedCSS = originalCSS
        .split("\n")
        .filter((line) => !usedCSS.includes(line))
        .join("\n");

      // Добавить комментарии к неиспользуемым стилям
      const commentedUnusedCSS = unusedCSS
        .split("\n")
        .map((line) => `/* ${line.trim()} */`)
        .join("\n");

      // Скомбинировать использованный CSS с закомментированным неиспользуемым
      const newCSS = `${usedCSS}\n\n/* --- Unused styles commented below --- */\n${commentedUnusedCSS}`;

      // Перезаписать CSS-файл с результатом
      fs.writeFileSync(result.file, newCSS, "utf-8");
      console.log(`Processed file: ${result.file}`);
    });
  })
  .catch((err) => console.error(err));

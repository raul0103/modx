/**
 * Данный api работает на удаление поддоменов
 *
 * - По документации beget разрешено выполнять не более 60ти запросов в минуту, поэтому испольуем API_DELAY
 */

const API = {
  login: "",
  password: "",
  delay: 1000, // Задержка между запросами
};

const DOMAIN = "domain.ru"; // Домен от которого необходимо удалить данные

async function main() {
  const response = await fetch(
    `https://api.beget.com/api/domain/getSubdomainList?login=${API.login}&passwd=${API.password}&output_format=json`
  );

  if (response.ok) {
    const data = await response.json();

    if (data?.answer?.result) {
      const find_domains = data?.answer?.result.filter(
        (item) => item.fqdn.indexOf(DOMAIN) > -1
      );

      for (const find_domain of find_domains) {
        const response = await fetch(
          `https://api.beget.com/api/domain/deleteSubdomain?login=${API.login}&passwd=${API.password}&input_format=json&output_format=json&input_data={"id": ${find_domain.id}}`
        );
        let response_json = await response.text();

        if (response.ok)
          console.log(`[DELETED] ${find_domain.fqdn}: ${response_json}`);
        else console.log(`[ERROR] ${find_domain.fqdn}: ${response_json}`);

        await delay(API.delay);
      }
    }
  } else {
    console.error("Ошибка при получении поддоменов: " + response.status);
  }
}

main();

function delay(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

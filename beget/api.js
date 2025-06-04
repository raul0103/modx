const domains = [
  {
    id: 12953436,
    fqdn: "zavod-zgbi-moskva.ru",
    date_add: "2025-06-04 18:52:40",
    auto_renew: true,
    date_register: "2025-06-04",
    date_expire: "2026-06-04",
    can_renew: null,
    registrar: "beget",
    registrar_status: "delegated",
    register_order_status: "FINISH",
    register_order_comment: null,
    renew_order_status: null,
    is_under_control: 1,
  },
];

const domain_names = [
  "zavod-zgbi-moskva.ru",
  "zavod-zgbi-odincovo.ru",
  "zavod-zgbi-ramenskoe.ru",
];

const API_PASSWORD = "";
const API_LOGIN = "milosl0a";
const SITE_ID = 8576776;

// Привязать домены к сайту
// domains.forEach(async (domain) => {
//   await fetch(
//     `https://api.beget.com/api/site/linkDomain?login=${API_LOGIN}&passwd=${API_PASSWORD}&input_format=json&output_format=json&input_data={"domain_id":${domain.id},"site_id":${SITE_ID}}`
//   );
// });

// Версия php
// domains.forEach(async (domain) => {
//   await fetch(
//     `https://api.beget.com/api/domain/changePhpVersion?login=${API_LOGIN}&passwd=${API_PASSWORD}&full_fqdn=${domain.fqdn}&php_version=7.4&is_cgi=true&output_format=json`
//   );
// });

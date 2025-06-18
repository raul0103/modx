const domains = [
  {
    id: 12994580,
    fqdn: "купить-жби-красногорск.рф",
    date_add: "2025-06-18 06:43:53",
    auto_renew: true,
    date_register: "2025-06-18",
    date_expire: "2026-06-18",
    can_renew: null,
    registrar: "beget",
    registrar_status: "delegated",
    register_order_status: "FINISH",
    register_order_comment: null,
    renew_order_status: null,
    is_under_control: 1,
  },
  {
    id: 12994581,
    fqdn: "купить-жби-мытищи.рф",
    date_add: "2025-06-18 06:44:05",
    auto_renew: true,
    date_register: "2025-06-18",
    date_expire: "2026-06-18",
    can_renew: null,
    registrar: "beget",
    registrar_status: "delegated",
    register_order_status: "FINISH",
    register_order_comment: null,
    renew_order_status: null,
    is_under_control: 1,
  },
  {
    id: 12994582,
    fqdn: "купить-жби-люберцы.рф",
    date_add: "2025-06-18 06:44:18",
    auto_renew: true,
    date_register: "2025-06-18",
    date_expire: "2026-06-18",
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
  "купить-жби-москва.рф",
  "купить-жби-одинцово.рф",
  "купить-жби-раменское.рф",
];

const API_PASSWORD = encodeURIComponent("");
const API_LOGIN = "ru12345680";
const SITE_ID = 8581445;

// Привязать домены к сайту
domain_names.forEach(async (domain_name) => {
  const domain = domains.find((item) => item.fqdn === domain_name);
  if (domain)
    await fetch(
      `https://api.beget.com/api/site/linkDomain?login=${API_LOGIN}&passwd=${API_PASSWORD}&input_format=json&output_format=json&input_data={"domain_id":${domain.id},"site_id":${SITE_ID}}`
    );
});

// Версия php
domain_names.forEach(async (domain_name) => {
  await fetch(
    `https://api.beget.com/api/domain/changePhpVersion?login=${API_LOGIN}&passwd=${API_PASSWORD}&full_fqdn=${domain_name}&php_version=7.4&is_cgi=true&output_format=json`
  );
});

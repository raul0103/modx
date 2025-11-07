const domain = "www-xotpipe.ru";

const exceptions = [
  domain,
  `autoconfig.${domain}`,
  `autodiscover.${domain}`,
  `www.${domain}`,
];
document.querySelectorAll('[st="dns-item-list"]').forEach((element) => {
  const title = element.querySelector('[st="dns-list-item-title"]').textContent;
  if (exceptions.indexOf(title) > -1) return;

  const btn = element.querySelector('[st="button-dns-delete-node"]');
  if (!btn) return;

  btn.click();
});

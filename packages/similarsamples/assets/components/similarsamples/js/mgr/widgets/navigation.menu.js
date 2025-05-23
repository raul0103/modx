Ext.namespace("SimilarSamples.widget");

SimilarSamples.widget.NavigationMenu = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "similarsamples-widget-navigation",
    cls: "similarsamples-navigation-menu",
    items: [
      {
        xtype: "panel",
        layout: "hbox",
        items: [
          {
            xtype: "button",
            cls: getActiveClass("home"),
            text: "Правила",
            handler: () =>
              (window.location.href =
                "index.php?a=home&namespace=similarsamples"),
          },
          // {
          //   xtype: "button",
          //   cls: getActiveClass("samples"),
          //   text: "Выборки",
          //   handler: () =>
          //     (window.location.href =
          //       "index.php?a=samples&namespace=similarsamples"),
          // },
        ],
      },
    ],
  });
  SimilarSamples.widget.NavigationMenu.superclass.constructor.call(
    this,
    config
  );
};

Ext.extend(SimilarSamples.widget.NavigationMenu, MODx.Panel);

// Функция для получения активного класса
let getActiveClass = function (page) {
  // Получаем текущий URL
  const currentUrl = window.location.href;

  // Проверяем, содержит ли URL нужную страницу
  if (currentUrl.indexOf("a=" + page) !== -1) {
    return "similarsamples-nav-link active"; // Если страница активна, добавляем класс "active"
  }

  return "similarsamples-nav-link"; // Если страница не активна, то просто ссылка
};

Ext.reg(
  "similarsamples-widget-navigation",
  SimilarSamples.widget.NavigationMenu
);

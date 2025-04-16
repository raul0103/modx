Ext.namespace("UserShop.widget");

UserShop.widget.NavigationMenu = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-widget-navigation",
    cls: "usershop-navigation-menu",
    items: [
      {
        xtype: "panel",
        layout: "hbox",
        items: [
          {
            xtype: "button",
            cls: getActiveClass("home"),
            text: "Главная",
            handler: () =>
              (window.location.href = "index.php?a=home&namespace=usershop"),
          },
          {
            xtype: "button",
            cls: getActiveClass("reviews"),
            text: "Отзывы на заказы",
            handler: () =>
              (window.location.href = "index.php?a=reviews&namespace=usershop"),
          },
          {
            xtype: "button",
            cls: getActiveClass("discount"),
            text: "Персональные скидки",
            handler: () =>
              (window.location.href =
                "index.php?a=discount&namespace=usershop"),
          },
        ],
      },
    ],
  });
  UserShop.widget.NavigationMenu.superclass.constructor.call(this, config);
};

Ext.extend(UserShop.widget.NavigationMenu, MODx.Panel);

// Функция для получения активного класса
let getActiveClass = function (page) {
  // Получаем текущий URL
  const currentUrl = window.location.href;

  // Проверяем, содержит ли URL нужную страницу
  if (currentUrl.indexOf(page) !== -1) {
    return "usershop-nav-link active"; // Если страница активна, добавляем класс "active"
  }

  return "usershop-nav-link"; // Если страница не активна, то просто ссылка
};

Ext.reg("usershop-widget-navigation", UserShop.widget.NavigationMenu);

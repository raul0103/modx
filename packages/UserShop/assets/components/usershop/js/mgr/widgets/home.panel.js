Ext.namespace("UserShop.panel");

UserShop.panel.Home = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-panel-home",
    cls: "container",
    items: [
      {
        html: "<h2>Пользователи магазина</h2>",
        cls: "modx-page-header",
      },
      {
        xtype: "usershop-widget-navigation",
        preventRender: true,
      },
    ],
  });
  UserShop.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.panel.Home, MODx.Panel);
Ext.reg("usershop-panel-home", UserShop.panel.Home);

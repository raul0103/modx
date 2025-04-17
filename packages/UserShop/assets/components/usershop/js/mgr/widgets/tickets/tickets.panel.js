Ext.namespace("UserShop.panel");

UserShop.panel.Tickets = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-panel-tickets",
    cls: "container",
    items: [
      {
        html: "<h2>Обращения пользователей</h2>",
        cls: "modx-page-header",
      },
      {
        xtype: "usershop-widget-navigation",
      },
      {
        xtype: "usershop-grid-tickets",
      },
    ],
  });
  UserShop.panel.Tickets.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.panel.Tickets, MODx.Panel);
Ext.reg("usershop-panel-tickets", UserShop.panel.Tickets);

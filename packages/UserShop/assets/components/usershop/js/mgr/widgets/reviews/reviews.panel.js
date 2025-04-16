Ext.namespace("UserShop.panel");

UserShop.panel.Reviews = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-panel-reviews",
    cls: "container",
    items: [
      {
        html: "<h2>Отзывы на заказы</h2>",
        cls: "modx-page-header",
      },
      {
        xtype: "usershop-widget-navigation",
      },
      {
        xtype: "usershop-grid-reviews",
      },
    ],
  });
  UserShop.panel.Reviews.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.panel.Reviews, MODx.Panel);
Ext.reg("usershop-panel-reviews", UserShop.panel.Reviews);

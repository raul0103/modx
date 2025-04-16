Ext.namespace("UserShop.panel");

UserShop.panel.Reviews = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-panel-discount",
    cls: "container",
    items: [
      {
        html: "<h2>Персональные скидки</h2>",
        cls: "modx-page-header",
      },
      {
        xtype: "usershop-widget-navigation",
        preventRender: true,
      },
      {
        xtype: "usershop-grid-discount",
      },
    ],
  });
  UserShop.panel.Reviews.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.panel.Reviews, MODx.Panel);
Ext.reg("usershop-panel-discount", UserShop.panel.Reviews);

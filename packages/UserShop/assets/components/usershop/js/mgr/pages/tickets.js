Ext.namespace("UserShop.page");

UserShop.page.Tickets = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "usershop-panel-tickets",
        renderTo: "usershop-content",
      },
    ],
  });
  UserShop.page.Tickets.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.page.Tickets, MODx.Component);
Ext.reg("usershop-page-tickets", UserShop.page.Tickets);

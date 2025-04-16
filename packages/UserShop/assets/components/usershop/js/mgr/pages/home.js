Ext.namespace("UserShop.page");

UserShop.page.Home = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "usershop-panel-home",
        renderTo: "usershop-content",
      },
    ],
  });
  UserShop.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.page.Home, MODx.Component);
Ext.reg("usershop-page-home", UserShop.page.Home);

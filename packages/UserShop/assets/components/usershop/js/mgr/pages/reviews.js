Ext.namespace("UserShop.page");

UserShop.page.Reviews = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "usershop-panel-reviews",
        renderTo: "usershop-content",
      },
    ],
  });
  UserShop.page.Reviews.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.page.Reviews, MODx.Component);
Ext.reg("usershop-page-reviews", UserShop.page.Reviews);

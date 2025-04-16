Ext.namespace("UserShop.page");

UserShop.page.Discount = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "usershop-panel-discount",
        renderTo: "usershop-content",
      },
    ],
  });
  UserShop.page.Discount.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.page.Discount, MODx.Component);
Ext.reg("usershop-page-discount", UserShop.page.Discount);

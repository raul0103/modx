Ext.namespace("UserShop.page");

UserShop.page.ProductReviews = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "usershop-panel-product-reviews",
        renderTo: "usershop-content",
      },
    ],
  });
  UserShop.page.ProductReviews.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.page.ProductReviews, MODx.Component);
Ext.reg("usershop-page-product-reviews", UserShop.page.ProductReviews);

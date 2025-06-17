Ext.namespace("UserShop.panel");

UserShop.panel.ProductReviews = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "usershop-panel-product-reviews",
    cls: "container",
    items: [
      {
        html: "<h2>Отзывы на товары</h2>",
        cls: "modx-page-header",
      },
      {
        xtype: "usershop-widget-navigation",
      },
      {
        xtype: "usershop-grid-product-reviews",
      },
    ],
  });
  UserShop.panel.ProductReviews.superclass.constructor.call(this, config);
};
Ext.extend(UserShop.panel.ProductReviews, MODx.Panel);
Ext.reg("usershop-panel-product-reviews", UserShop.panel.ProductReviews);

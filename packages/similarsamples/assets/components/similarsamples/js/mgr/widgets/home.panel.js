Ext.namespace("SimilarSamples.panel");

SimilarSamples.panel.Home = function (config) {
  config = config || {};
  Ext.apply(config, {
    id: "similarsamples-panel-rules",
    cls: "container",
    items: [
      // {
      //   html: "<h2>Записи</h2>",
      //   cls: "modx-page-header",
      // },
      {
        xtype: "similarsamples-widget-navigation",
        preventRender: true,
      },
      {
        xtype: "similarsamples-grid-main",
      },
    ],
  });
  SimilarSamples.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(SimilarSamples.panel.Home, MODx.Panel);
Ext.reg("similarsamples-panel-rules", SimilarSamples.panel.Home);

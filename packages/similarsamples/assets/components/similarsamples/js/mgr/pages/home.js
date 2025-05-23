Ext.namespace("SimilarSamples.page");

SimilarSamples.page.Home = function (config) {
  config = config || {};
  Ext.applyIf(config, {
    components: [
      {
        xtype: "similarsamples-panel-rules",
        renderTo: "similarsamples-content",
      },
    ],
  });
  SimilarSamples.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(SimilarSamples.page.Home, MODx.Component);
Ext.reg("similarsamples-page-home", SimilarSamples.page.Home);

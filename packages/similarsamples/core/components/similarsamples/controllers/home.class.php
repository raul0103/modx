<?php
class SimilarSamplesHomeManagerController extends modExtraManagerController
{
    public function process(array $scriptProperties = array()) {}

    public function getPageTitle()
    {
        return 'Записи';
    }

    public function loadCustomCssJs()
    {
        $assetsUrl = $this->modx->getOption('similarsamples.assets_url');
        $this->addCss($assetsUrl . 'css/mgr/main.css');
        $this->addJavascript($assetsUrl . 'js/mgr/main.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/navigation.menu.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/grids/rules.grid.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/home.panel.js');
        $this->addJavascript($assetsUrl . 'js/mgr/pages/home.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.load({ xtype: "similarsamples-page-home" });
            });
        </script>');
    }

    public function getTemplateFile()
    {
        return 'base.tpl';
    }
}

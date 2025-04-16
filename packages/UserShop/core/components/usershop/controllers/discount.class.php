<?php
class UserShopDiscountManagerController extends modExtraManagerController
{
    public function process(array $scriptProperties = array()) {}

    public function getPageTitle()
    {
        return 'Скидки клиентов';
    }

    public function loadCustomCssJs()
    {
        $assetsUrl = $this->modx->getOption('usershop.assets_url');
        $this->addCss($assetsUrl . 'css/mgr/main.css');
        $this->addJavascript($assetsUrl . 'js/mgr/main.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/navigation.menu.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/discount/discount.grid.js');
        $this->addJavascript($assetsUrl . 'js/mgr/widgets/discount/discount.panel.js');
        $this->addJavascript($assetsUrl . 'js/mgr/pages/discount.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.load({ xtype: "usershop-page-discount" });
            });
        </script>');
    }

    public function getTemplateFile()
    {
        return 'base.tpl';
    }
}

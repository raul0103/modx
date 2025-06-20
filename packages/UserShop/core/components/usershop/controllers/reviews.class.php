<?php
class UserShopReviewsManagerController extends modExtraManagerController
{
    public function process(array $scriptProperties = array()) {}

    public function getPageTitle()
    {
        return 'Отзывы о заказах';
    }

    public function loadCustomCssJs()
    {
        $assetsUrl = $this->modx->getOption('usershop.assets_url');
        $this->addCss($assetsUrl . 'css/mgr/main.css');
        $this->addJavascript($assetsUrl . 'js/mgr/main.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/navigation.menu.js');

        $this->addJavascript($assetsUrl . 'js/mgr/widgets/order-reviews/reviews.grid.js');
        $this->addJavascript($assetsUrl . 'js/mgr/widgets/order-reviews/reviews.panel.js');
        $this->addJavascript($assetsUrl . 'js/mgr/pages/order-reviews.js');

        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                MODx.load({ xtype: "usershop-page-reviews" });
            });
        </script>');
    }

    public function getTemplateFile()
    {
        return 'base.tpl';
    }
}

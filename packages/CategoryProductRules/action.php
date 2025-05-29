<?php
const PRODUCTION = false;

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

// Доступ к процессорам mgr допускается если пользователь авторизован в админке
if (PRODUCTION && $_REQUEST['context'] == "mgr" && !$modx->user->hasSessionContext('mgr')) {
    http_response_code(403);
    header('Content-Type: application/json');
    exit(json_encode([
        'success' => false,
        'error' => 'You are not logged in to the MODX admin panel (mgr).',
    ]));
}

// Экшены для mgr контекста
if ($_REQUEST['context'] == "mgr") {
    if ($_REQUEST['action'] === "get-contexts-list") {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/getContextsList.php";
        getContextsList();
    }

    if ($_REQUEST['action'] === "get-categories" && ($_REQUEST['products_count'] || $_REQUEST['category_id']) && $_REQUEST['context_key']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/getCategories.php";
        getCategories($_REQUEST['products_count'], $_REQUEST['category_id'], $_REQUEST['context_key']);
    }

    if ($_REQUEST['action'] === "get-category" && $_REQUEST['category_id']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/getCategory.php";
        getCategory($_REQUEST['category_id']);
    }

    if ($_REQUEST['action'] === "get-rule" && $_REQUEST['category_id']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/rules/get.php";
        getRules($_REQUEST['category_id']);
    }

    if ($_REQUEST['action'] === "get-rules" && $_REQUEST['context_key']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/rules/list.php";
        getRules($_REQUEST['context_key']);
    }

    if ($_REQUEST['action'] === "create-rules" && $_REQUEST['category_id'] && $_REQUEST['context_key'] && $_REQUEST['data']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/rules/create.php";
        createRules($_REQUEST['category_id'], $_REQUEST['context_key'], $_REQUEST['data']);
    }

    if ($_REQUEST['action'] === "update-rules" && $_REQUEST['category_id'] && $_REQUEST['context_key'] && $_REQUEST['data']) {
        include MODX_CORE_PATH . "components/CategoryProductRules/actions/mgr/rules/update.php";
        updateRules($_REQUEST['category_id'], $_REQUEST['context_key'], $_REQUEST['data']);
    }
}

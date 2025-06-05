<?php
const PRODUCTION = true;

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
    if ($_REQUEST['action'] === "fetch.context-categories") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/fetchContextCategories.php";
        tsFetchContextCategories();
    }
    if ($_REQUEST['action'] === "fetch.category-tags") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/fetchCategoryTags.php";
        tsFetchCategoryTags($_REQUEST['category_id']);
    }

    if ($_REQUEST['action'] === "category.create") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/category/create.php";
        tsCreateCategory($_REQUEST['name'], $_REQUEST['context_key']);
    }
    if ($_REQUEST['action'] === "category.remove") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/category/remove.php";
        tsRemoveCategory($_REQUEST['category_id']);
    }
    if ($_REQUEST['action'] === "category.update") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/category/update.php";
        tsUpdateCategory($_REQUEST['category_id'], $_REQUEST['name']);
    }

    if ($_REQUEST['action'] === "tag.create") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/tag/create.php";
        tsCreateTag($_REQUEST['title'], $_REQUEST['uri'], $_REQUEST['image'], $_REQUEST['type'], $_REQUEST['resource_id'],  $_REQUEST['category_id'], $_REQUEST['group_name']);
    }
    if ($_REQUEST['action'] === "tag.remove") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/tag/remove.php";
        tsRemoveTag($_REQUEST['tag_id']);
    }
    if ($_REQUEST['action'] === "tag.update") {
        include MODX_CORE_PATH . "components/TagsStore/actions/mgr/tag/update.php";
        tsUpdateTag($_REQUEST['tag_id'], $_REQUEST['title'], $_REQUEST['uri'], $_REQUEST['image'], $_REQUEST['type'], $_REQUEST['resource_id'],  $_REQUEST['category_id'], $_REQUEST['group_name']);
    }
}

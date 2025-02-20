<?php

/**
 * Получит из куки все ресурсы в избранном и запишет в плейсхолдер
 * Передать cookie_key при вызове сниппета
 * 
 * @return array
 */
if (empty($cookie_key)) return;

$favorite_ids = $_COOKIE[$cookie_key];
if ($favorite_ids) {
    $favorite_ids = explode(",", $favorite_ids);
} else {
    $favorite_ids = [];
}

$modx->setPlaceholder($cookie_key, $favorite_ids);

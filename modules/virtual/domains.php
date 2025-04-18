<?php

/**
 * Домены сайты. Помогает избежать проблем с такими как osnova.spb.ru
 * + Помогают определить верный контекст для поиска
 * osnova.spb.ru или osnova.spb-2.ru они принадлежат к разным контекстам и будет понятно где искать для них виртуалки
 */
$domains = [
    "stroymarket.waskuli.beget.tech" => "plitnye",
    "alterteplo.waskuli.beget.tech" => "alterteplo"
];

return $domains;

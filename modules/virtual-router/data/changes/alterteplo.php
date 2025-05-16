<?php

return function ($global_data) {
    return [
        "Санкт-Петербург" => $global_data["toponim"]["base"]["standart"],
        "в Санкт-Петербурге" => $global_data["toponim"]["where"]["standart"],
        "по Санкт-Петербургу" => $global_data["toponim"]["on"]["standart"],
        "Санкт-Петербурга" => $global_data["toponim"]["what"]["standart"],
    ];
};

<?php

return function ($global_data) {
    return [
        "Москва" => $global_data["toponim"]["base"]["standart"],
        "в Москве" => $global_data["toponim"]["where"]["standart"],
        "по Москве" => $global_data["toponim"]["on"]["standart"],
        "Москвы" => $global_data["toponim"]["what"]["standart"],
    ];
};

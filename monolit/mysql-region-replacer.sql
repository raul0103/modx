UPDATE modx_site_content SET
    content = REPLACE(content, 'в Санкт-Петербурге', 'в Москве');
UPDATE modx_site_content SET
    content = REPLACE(content, 'по Санкт-Петербургу', 'по Москве');
UPDATE modx_site_content SET
    content = REPLACE(content, 'Санкт-Петербурга', 'Москвы');
UPDATE modx_site_content SET
    content = REPLACE(content, 'Санкт-Петербург', 'Москва');

UPDATE modx_site_content SET
    content = REPLACE(content, 'Ленинградскую', 'Московскую');
UPDATE modx_site_content SET
    content = REPLACE(content, 'Ленинградская', 'Московская');
UPDATE modx_site_content SET
    content = REPLACE(content, 'по Ленинградской', 'по Московской');
UPDATE modx_site_content SET
    content = REPLACE(content, 'по Ленобласти', 'по Московской области');
UPDATE modx_site_content SET
    content = REPLACE(content, 'Ленобласть', 'Московская область');

UPDATE modx_site_content SET
    pagetitle   = REPLACE(pagetitle,   'ЛО', 'МО'),
    longtitle   = REPLACE(longtitle,   'ЛО', 'МО'),
    description = REPLACE(description, 'ЛО', 'МО'),
    introtext   = REPLACE(introtext,   'ЛО', 'МО'),
    content     = REPLACE(content,     'ЛО', 'МО');

UPDATE modx_site_content SET
    pagetitle   = REPLACE(pagetitle,   'СПб', 'МСК'),
    longtitle   = REPLACE(longtitle,   'СПб', 'МСК'),
    description = REPLACE(description, 'СПб', 'МСК'),
    introtext   = REPLACE(introtext,   'СПб', 'МСК'),
    content     = REPLACE(content,     'СПб', 'МСК'),
    menutitle   = REPLACE(menutitle,   'СПб', 'МСК');

UPDATE modx_site_content SET
    pagetitle   = REPLACE(pagetitle,   'СПБ', 'МСК'),
    longtitle   = REPLACE(longtitle,   'СПБ', 'МСК'),
    description = REPLACE(description, 'СПБ', 'МСК'),
    introtext   = REPLACE(introtext,   'СПБ', 'МСК'),
    content     = REPLACE(content,     'СПБ', 'МСК'),
    menutitle   = REPLACE(menutitle,   'СПБ', 'МСК');

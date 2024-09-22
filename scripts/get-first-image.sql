SELECT
    `msProductFile`.`id` AS `msProductFile_id`,
    `msProductFile`.`product_id` AS `msProductFile_product_id`,
    `msProductFile`.`source` AS `msProductFile_source`,
    `msProductFile`.`parent` AS `msProductFile_parent`,
    `msProductFile`.`name` AS `msProductFile_name`,
    `msProductFile`.`description` AS `msProductFile_description`,
    `msProductFile`.`path` AS `msProductFile_path`,
    `msProductFile`.`file` AS `msProductFile_file`,
    `msProductFile`.`type` AS `msProductFile_type`,
    `msProductFile`.`createdon` AS `msProductFile_createdon`,
    `msProductFile`.`createdby` AS `msProductFile_createdby`,
    `msProductFile`.`rank` AS `msProductFile_rank`,
    `msProductFile`.`url` AS `msProductFile_url`,
    `msProductFile`.`properties` AS `msProductFile_properties`,
    `msProductFile`.`hash` AS `msProductFile_hash`,
    `msProductFile`.`active` AS `msProductFile_active`
FROM
    `modx_ms2_product_files` AS `msProductFile`
WHERE
    (
        `msProductFile`.`product_id` = 88
        AND `msProductFile`.`parent` = 0
        AND `msProductFile`.`type` = 'image'
    )
LIMIT
    1
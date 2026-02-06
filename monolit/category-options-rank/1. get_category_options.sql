SELECT
  sc.alias,
  mso.key,
  co.rank
FROM
  modx_ms2_category_options AS co
  LEFT JOIN modx_site_content AS sc ON sc.id = co.category_id
  LEFT JOIN modx_ms2_options AS mso ON mso.id = co.option_id
WHERE
  co.category_id IN (
    SELECT
      id
    FROM
      modx_site_content
    WHERE
      context_key = 'krovelnyjstroymarket'
  );
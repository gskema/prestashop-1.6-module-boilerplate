CREATE TABLE IF NOT EXISTS `ps_mm_model4` (
  `id_mm_model4` INT(11)      NOT NULL AUTO_INCREMENT,
  `property1`    VARCHAR(128) NULL,
  `toggleable`   TINYINT(1)   DEFAULT 1,
  `active`       TINYINT(1)   DEFAULT 1,

  /* Bug: allow PrestaShop to insert dummy values here */
  `position`     INT(10)      DEFAULT 0,

  PRIMARY KEY (`id_mm_model4`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;


CREATE TABLE IF NOT EXISTS `ps_mm_model4_lang` (
  `id_mm_model4` INT(11) NOT NULL,
  `id_lang`      INT(11) NOT NULL,
  `text`         TEXT    NULL,
  `content`      TEXT    NULL,

  /* @TODO Add id_shop to this table to have different translations per-shop basis */
  /* `id_shop`   INT(11) NOT NULL, */

  PRIMARY KEY (`id_mm_model4`, `id_lang`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

CREATE TABLE IF NOT EXISTS `ps_mm_model4_shop` (
  `id_mm_model4` INT(11) NOT NULL,
  `id_shop`      INT(11) NOT NULL,
  `position`     INT(10) DEFAULT 0,
  PRIMARY KEY (`id_mm_model4`, `id_shop`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

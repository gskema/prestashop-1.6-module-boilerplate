CREATE TABLE IF NOT EXISTS `ps_mm_model3` (
  `id_mm_model3` INT(11)      NOT NULL AUTO_INCREMENT,
  `property1`    VARCHAR(128) NULL,
  `toggleable`   TINYINT(1)   DEFAULT 1,
  `active`       TINYINT(1)   DEFAULT 0,

  /* Bug: allow PrestaShop to insert dummy values here */
  `position`     INT(10)       DEFAULT 0,

  PRIMARY KEY (`id_mm_model3`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

CREATE TABLE IF NOT EXISTS `ps_mm_model3_shop` (
  `id_mm_model3` INT(11) NOT NULL,
  `id_shop`      INT(11) NOT NULL,
  `position`     INT(10) DEFAULT 0,
  PRIMARY KEY (`id_mm_model3`, `id_shop`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

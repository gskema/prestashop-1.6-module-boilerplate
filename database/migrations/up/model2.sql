CREATE TABLE IF NOT EXISTS `ps_mm_model2` (
  `id_mm_model2`   INT(11)      NOT NULL AUTO_INCREMENT,
  `property1`      VARCHAR(128) NULL,
  `toggleable`     TINYINT(1)   DEFAULT 1,
  `active`         TINYINT(1)   DEFAULT 1,
  `position`       INT(10)      DEFAULT 0,
  PRIMARY KEY (`id_mm_model2`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

CREATE TABLE IF NOT EXISTS `ps_mm_model2_lang` (
  `id_mm_model2` INT(11) NOT NULL,
  `id_lang`      INT(11) NOT NULL,
  `text`         TEXT    NULL,
  `content`      TEXT    NULL,
  PRIMARY KEY (`id_mm_model2`, `id_lang`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

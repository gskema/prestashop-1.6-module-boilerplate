CREATE TABLE IF NOT EXISTS `ps_mm_model1` (
    `id_mm_model1` INT(11)      NOT NULL AUTO_INCREMENT,
    `property1`    VARCHAR(128) NULL,
    `toggleable`   TINYINT(1)   DEFAULT 1,
    `active`       TINYINT(1)   DEFAULT 1,
    `position`     INT(10)      DEFAULT 0,
    PRIMARY KEY (`id_mm_model1`)
) ENGINE = INNODB DEFAULT CHARSET = UTF8;

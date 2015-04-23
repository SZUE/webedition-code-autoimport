CREATE TABLE ###TBLPREFIX###tblMetaValues (
 `tag` varchar(255) NOT NULL,
 `value` varchar(255) NOT NULL,
 PRIMARY KEY (`tag`(32),`value`(32))
)  ENGINE=MyISAM;
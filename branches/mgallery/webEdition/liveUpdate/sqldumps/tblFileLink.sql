CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int unsigned NOT NULL COMMENT 'collection ID',
	DocumentTable enum('tblFile','tblObjectFiles','tblVFile','tblCategorys','tblTemplates','tblObject','tblnavigation','tblNewsletter','tblglossary','tblbanner') NOT NULL default 'tblFile' COMMENT 'the table where ID matches',
	type enum('media','document','object','master','archive','collection') NOT NULL default 'media' COMMENT 'referenced type',
	remObj int unsigned NOT NULL default '0' COMMENT 'the referenced object',
	remTable enum('tblFile','tblObjectFiles') NOT NULL default 'tblFile' COMMENT 'the table where to find the referenced object',
	`position` smallint unsigned NOT NULL default '0' COMMENT 'optional position in case ordering is important',
	`isTemp` tinyint unsigned NOT NULL default '0' COMMENT 'this is one if the referenced object is in temporary table',
	PRIMARY KEY (ID,DocumentTable,`type`,remObj,`position`,isTemp),
	KEY remObj (remTable,remObj)
) ENGINE=MyISAM;
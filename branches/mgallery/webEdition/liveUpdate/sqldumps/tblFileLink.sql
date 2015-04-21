CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int unsigned NOT NULL COMMENT 'remote ID of the following table',
	DocumentTable enum('tblFile','tblObjectFiles','tblVFile','tblCategorys','tblTemplates','tblObject','tblnavigation','tblNewsletter','tblglossary','tblbanner') NOT NULL default 'tblFile' COMMENT 'the table where ID matches',
	type enum('media','document','object','master','archive','collection') NOT NULL default 'media' COMMENT 'referenced type',
	remObj int unsigned NOT NULL default '0' COMMENT 'the referenced object',
	remTable enum('tblFile','tblObjectFiles') NOT NULL default 'tblFile' COMMENT 'the table where to find the referenced object',
	`position` int unsigned NOT NULL default '0' COMMENT 'optional position in case ordering is important',
	`isTemp` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY (ID,DocumentTable,`type`,remObj,`position`,isTemp),
	KEY remObj (remTable,remObj)
) ENGINE=MyISAM;
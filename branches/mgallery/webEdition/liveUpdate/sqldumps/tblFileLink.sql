CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int unsigned NOT NULL,
	DocumentTable enum('tblFile','tblObjectFiles','tblVFile','tblCategorys') NOT NULL default 'tblFile',
	type enum('media','document','object','master','archive') NOT NULL default 'media',
	remObj int unsigned NOT NULL default '0',
	remTable enum('tblFile','tblObjectFiles','tblVFile','tblCategorys','tblTemplates','tblNavigation') NOT NULL default 'tblFile',
	`position` int unsigned NOT NULL default '0',
	`isTemp` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY (ID,DocumentTable,`type`,remObj,`position`,isTemp),
	KEY remObj (remTable,remObj)
) ENGINE=MyISAM;

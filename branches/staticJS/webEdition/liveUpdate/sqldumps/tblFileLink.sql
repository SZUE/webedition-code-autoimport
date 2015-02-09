CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int unsigned NOT NULL,
	DocumentTable enum('tblFile','tblObjectFiles','tblVFile') NOT NULL default 'tblFile',
	type enum('image','master','archive') NOT NULL default 'image',
	remObj int unsigned NOT NULL default '0',
	remTable enum('tblFile','tblObjectFiles','tblVFile') NOT NULL default 'tblFile',
	`position` int unsigned NOT NULL default '0',
	PRIMARY KEY  (ID,DocumentTable,remObj),
	KEY remObj (remTable,remObj)
) ENGINE=MyISAM;

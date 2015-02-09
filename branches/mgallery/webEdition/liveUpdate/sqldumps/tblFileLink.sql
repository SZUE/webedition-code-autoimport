CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int(11) unsigned NOT NULL auto_increment,
	DocumentTable enum('tblFile','tblObjectFiles','tblVFile') NOT NULL default 'tblFile',
	type enum('image','master','archive') NOT NULL default 'image',
	remObj int unsigned NOT NULL default '0',
	`position` int unsigned NOT NULL default '0',
	PRIMARY KEY  (ID,DocumentTable,type),
	KEY remObj (DocumentTable,remObj)
) ENGINE=MyISAM;

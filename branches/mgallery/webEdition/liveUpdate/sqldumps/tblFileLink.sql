CREATE TABLE ###TBLPREFIX###tblFileLink (
  ID int(11) unsigned NOT NULL auto_increment,
	table enum('tblFile','tblObjectFiles','tblVFile') NOT NULL default 'tblFile',
	type enum('image','master','archive') NOT NULL default '',
	remObj int unsigned NOT NULL default '0',
	position int unsigned NOT NULL default '0',
	PRIMARY KEY  (ID,table,type),
	KEY remObj (table,remObj)
) ENGINE=MyISAM;

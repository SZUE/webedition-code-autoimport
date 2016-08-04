CREATE TABLE ###TBLPREFIX###tblOrderItems (
	ID int unsigned NOT NULL auto_increment,
  articleID int unsigned NOT NULL default '0',
  quantity decimal(10,3) NOT NULL default '0',
  Price decimal(15,5) NOT NULL default '0',
	customFields TEXT DEFAULT NULL,
	category TEXT DEFAULT NULL,

	PRIMARY KEY  (ID)
) ENGINE=MyISAM;
CREATE TABLE ###TBLPREFIX###tblOrderItems (
	ID int unsigned NOT NULL auto_increment,
  IntArticleID int unsigned NOT NULL default '0',
  IntQuantity decimal(10,3) NOT NULL default '0',
  Price decimal(15,5) NOT NULL default '0',

	PRIMARY KEY  (ID)
) ENGINE=MyISAM;
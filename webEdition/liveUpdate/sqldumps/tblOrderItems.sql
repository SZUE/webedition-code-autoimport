CREATE TABLE ###TBLPREFIX###tblOrderItems (
	ID int unsigned NOT NULL auto_increment,
  IntArticleID int unsigned default NULL,
  IntQuantity float default NULL,
  Price varchar(20) default NULL,

	PRIMARY KEY  (ID)
) ENGINE=MyISAM;
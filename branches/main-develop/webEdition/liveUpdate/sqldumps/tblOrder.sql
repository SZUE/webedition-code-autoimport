CREATE TABLE ###TBLPREFIX###tblOrder (
	ID int unsigned NOT NULL auto_increment,
	shopname tinytext NOT NULL DEFAULT '',
	customOrderNo varchar(100) NOT NULL DEFAULT '',
  IntCustomerID int unsigned default NULL,
  DateOrder DATETIME timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  DateConfirmation DATETIME default NULL,

	PRIMARY KEY  (ID),
	KEY DateOrder (DateOrder),
	KEY IntCustomerID(IntCustomerID)
) ENGINE=MyISAM;
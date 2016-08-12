CREATE TABLE ###TBLPREFIX###tblOrderItemDocument (
	ID int unsigned NOT NULL auto_increment,
	DocID int unsigned NOT NULL,
	type enum('document','object') NOT NULL,
	variant varchar(50),
	Published DATETIME NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	SerializedData longblob NOT NULL,
PRIMARY KEY (ID),
UNIQUE KEY doc (DocID,type,variant,Published)
) ENGINE=MyISAM;
CREATE TABLE ###TBLPREFIX###tblOrderItem (
	orderID int unsigned NOT NULL,
  orderDocID int unsigned NOT NULL,
  quantity decimal(10,3) NOT NULL default '0',
  Price decimal(15,5) NOT NULL default '0',
  Vat decimal(4,2) default NULL,
	customFields TEXT DEFAULT NULL,
	PRIMARY KEY (orderID,orderDocID),
	KEY orderDocID(orderDocID)
) ENGINE=MyISAM;
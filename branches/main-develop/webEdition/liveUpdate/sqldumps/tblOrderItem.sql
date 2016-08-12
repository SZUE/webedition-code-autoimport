CREATE TABLE ###TBLPREFIX###tblOrderItem (
	orderID int unsigned NOT NULL,
  orderDocID int unsigned NOT NULL,
  quantity decimal(10,3) NOT NULL default '0',
  Price decimal(15,5) NOT NULL default '0',
	customFields TEXT DEFAULT NULL,
	PRIMARY KEY (orderID,orderDocID),
	KEY orderDocID(orderDocID)
) ENGINE=MyISAM;
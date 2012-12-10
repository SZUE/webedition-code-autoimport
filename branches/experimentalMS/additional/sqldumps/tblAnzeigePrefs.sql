CREATE TABLE tblAnzeigePrefs (
  ID int NOT NULL IDENTITY(1,1),
  strDateiname varchar(255) NOT NULL default '',
  strFelder text NOT NULL,
  PRIMARY KEY  (ID),
  CONSTRAINT strDateiname UNIQUE(strDateiname)
) 
/* query separator */
INSERT  INTO tblAnzeigePrefs VALUES ('edit_shop_properties','a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}');
/* query separator */
INSERT INTO tblAnzeigePrefs VALUES ('shop_pref','€|19|german');

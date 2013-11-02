CREATE TABLE ###TBLPREFIX###tblAnzeigePrefs (
  strDateiname varchar(255) NOT NULL default '',
  strFelder text NOT NULL,
  PRIMARY KEY  (strDateiname)
) ENGINE=MyISAM;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs VALUES ('edit_shop_properties','a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}');
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs VALUES ('shop_pref','€|19|german');

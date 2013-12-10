CREATE TABLE ###TBLPREFIX###tblAnzeigePrefs (
  strDateiname varchar(255) NOT NULL default '',
  strFelder text NOT NULL,
  PRIMARY KEY  (strDateiname)
) ENGINE=MyISAM;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs SET ID=1,strDateiname='edit_shop_properties',strFelder='a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}';
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs SET ID=2,strDateiname='shop_pref',strFelder='€|19|german';
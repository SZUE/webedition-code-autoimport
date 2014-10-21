/*FIXME: mv to tblSettings*/
###UPDATEDROPCOL(ID,###TBLPREFIX###tblAnzeigePrefs)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblAnzeigePrefs (
  strDateiname varchar(255) NOT NULL default '',
  strFelder text NOT NULL,
  PRIMARY KEY  (strDateiname)
) ENGINE=MyISAM;
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs SET strDateiname='edit_shop_properties',strFelder='a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblAnzeigePrefs SET strDateiname='shop_pref',strFelder='€|19|german';

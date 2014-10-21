CREATE TABLE ###TBLPREFIX###tblObjectLink (
  OID int(11) unsigned NOT NULL default '0',
  field varchar(255) NOT NULL default '',
  TID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (OID,field),
  KEY `TID` (`TID`)
) ENGINE=MyISAM;

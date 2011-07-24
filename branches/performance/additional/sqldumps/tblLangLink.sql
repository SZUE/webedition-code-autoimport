CREATE TABLE ###TBLPREFIX###tblLangLink (
  ID int(11) NOT NULL AUTO_INCREMENT,
  DID int(11) NOT NULL default '0',
  DLocale varchar(5) NOT NULL default '',
  IsFolder tinyint(1) NOT NULL default '0',
  IsObject tinyint(1) NOT NULL default '0',
  LDID int(11) NOT NULL default '0',
  Locale varchar(5) NOT NULL default '',
  DocumentTable enum('tblFile','tblObjectFile','tblDocTypes') NOT NULL,
  PRIMARY KEY (ID),
  KEY DID (DID,Locale(5))
) ENGINE=MyISAM;

CREATE TABLE ###TBLPREFIX###tblLangLink (
  ID int(11) unsigned NOT NULL AUTO_INCREMENT,
  DID int(11) unsigned NOT NULL default '0',
  DLocale varchar(5) NOT NULL default '',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  IsObject tinyint(1) unsigned NOT NULL default '0',
  LDID int(11) unsigned NOT NULL default '0',
  Locale varchar(5) NOT NULL default '',
  DocumentTable enum('tblFile','tblObjectFile','tblDocTypes') NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY DID (DID,DocumentTable,DLocale,Locale,IsFolder,IsObject),
  UNIQUE KEY DLocale (DLocale,LDID,Locale,DocumentTable,IsFolder,IsObject),
  KEY LDID (LDID,DocumentTable,Locale)
) ENGINE=MyISAM;

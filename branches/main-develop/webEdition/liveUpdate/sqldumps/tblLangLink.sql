CREATE TABLE ###TBLPREFIX###tblLangLink (
  ID int unsigned NOT NULL AUTO_INCREMENT,
  DID int unsigned NOT NULL default '0',
  DLocale char(5) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  IsObject tinyint unsigned NOT NULL default '0',
  LDID int unsigned NOT NULL default '0',
  Locale char(5) NOT NULL default '',
  DocumentTable enum('tblFile','tblObjectFile','tblObjectFiles','tblDocTypes') NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY DID (DID,DLocale,IsObject,IsFolder,Locale,DocumentTable),
  UNIQUE KEY DLocale (DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable),
  KEY LDID (LDID,DocumentTable,Locale)
) ENGINE=MyISAM;

/* query separator */
###UPDATEONLY### UPDATE ###TBLPREFIX###tblLangLink SET DocumentTable="tblObjectFiles" WHERE DocumentTable="tblObjectFile";

/* query separator */
###ONKEYFAILED(DID,###TBLPREFIX###tblLangLink)ALTER IGNORE TABLE ###TBLPREFIX###tblLangLink ADD UNIQUE KEY DID (DID,DLocale,IsObject,IsFolder,Locale,DocumentTable);###

/* query separator */
###ONKEYFAILED(DLocale,###TBLPREFIX###tblLangLink)ALTER IGNORE TABLE ###TBLPREFIX###tblLangLink ADD UNIQUE KEY DLocale (DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable);###

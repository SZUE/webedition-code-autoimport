###UPDATEDROPCOL(LanguageID,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEDROPCOL(IsBinary,###TBLPREFIX###tblContent)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblContent (
  ID bigint(20) unsigned NOT NULL auto_increment,
  BDID int(11) unsigned NOT NULL default '0',
  Dat longtext,
  AutoBR enum('on','off') NOT NULL default 'off',
  PRIMARY KEY (ID),
  KEY BDID (BDID)
) ENGINE=MyISAM;

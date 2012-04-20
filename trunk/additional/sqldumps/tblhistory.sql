CREATE TABLE ###TBLPREFIX###tblhistory (
  ID bigint(20) unsigned NOT NULL auto_increment,
  DID bigint(20) unsigned NOT NULL default '0',
  DocumentTable varchar(64) NOT NULL default '',
  ContentType varchar(32) NOT NULL default '',
  ModDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Act enum('save') NOT NULL default 'save',
  UserName varchar(64) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY UserName (UserName,DocumentTable),
  KEY DID (DID,DocumentTable,ModDate)
) ENGINE=MyISAM;

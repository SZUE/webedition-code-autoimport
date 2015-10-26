CREATE TABLE ###TBLPREFIX###tblWorkflowLog (
  ID bigint(20) unsigned NOT NULL auto_increment,
  RefID bigint(20) unsigned NOT NULL default '0',
  docTable enum('tblWorkflowLog') NOT NULL default 'tblWorkflowLog',
  userID int(11) unsigned NOT NULL default '0',
  logDate int(10) unsigned NOT NULL default '0',
  `Type` tinyint(4) unsigned NOT NULL default '0',
  Description tinytext NOT NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
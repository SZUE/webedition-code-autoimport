CREATE TABLE ###TBLPREFIX###tblWorkflowLog (
  ID mediumint unsigned NOT NULL auto_increment,
  RefID int unsigned NOT NULL default '0',
  docTable enum('tblWorkflowLog') NOT NULL default 'tblWorkflowLog',
  userID int unsigned NOT NULL default '0',
  logDate int unsigned NOT NULL default '0',
  `Type` tinyint unsigned NOT NULL default '0',
  Description tinytext NOT NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
CREATE TABLE ###TBLPREFIX###tblWorkflowDoc (
  ID int unsigned NOT NULL auto_increment,
  workflowID int unsigned NOT NULL default '0',
  documentID int unsigned NOT NULL default '0',
  userID smallint unsigned NOT NULL default '0',
  `Status` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
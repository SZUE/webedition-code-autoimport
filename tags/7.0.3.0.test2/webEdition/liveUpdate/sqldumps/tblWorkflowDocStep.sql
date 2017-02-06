CREATE TABLE ###TBLPREFIX###tblWorkflowDocStep (
  ID int unsigned NOT NULL auto_increment,
  workflowDocID int unsigned NOT NULL default '0',
  workflowStepID int unsigned NOT NULL default '0',
  startDate int unsigned NOT NULL default '0',
  finishDate int unsigned NOT NULL default '0',
  `Status` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
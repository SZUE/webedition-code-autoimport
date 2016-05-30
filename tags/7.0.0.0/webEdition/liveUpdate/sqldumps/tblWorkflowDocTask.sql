CREATE TABLE ###TBLPREFIX###tblWorkflowDocTask (
  ID int unsigned NOT NULL auto_increment,
  documentStepID int unsigned NOT NULL default '0',
  workflowTaskID int unsigned NOT NULL default '0',
  `Date` int unsigned NOT NULL default '0',
  todoID int unsigned NOT NULL default '0',
  `Status` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
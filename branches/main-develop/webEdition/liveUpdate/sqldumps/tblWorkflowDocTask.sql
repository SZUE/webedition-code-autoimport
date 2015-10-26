CREATE TABLE ###TBLPREFIX###tblWorkflowDocTask (
  ID int(11) unsigned NOT NULL auto_increment,
  documentStepID int(11) unsigned NOT NULL default '0',
  workflowTaskID int(11) unsigned NOT NULL default '0',
  `Date` int(10) unsigned NOT NULL default '0',
  todoID int(11) unsigned NOT NULL default '0',
  `Status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
CREATE TABLE ###TBLPREFIX###tblWorkflowDocStep (
  ID int(11) unsigned NOT NULL auto_increment,
  workflowDocID int(11) unsigned NOT NULL default '0',
  workflowStepID int(11) unsigned NOT NULL default '0',
  startDate int(10) unsigned NOT NULL default '0',
  finishDate int(10) unsigned NOT NULL default '0',
  `Status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
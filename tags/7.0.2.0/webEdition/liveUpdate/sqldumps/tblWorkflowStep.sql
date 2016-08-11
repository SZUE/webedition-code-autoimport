CREATE TABLE ###TBLPREFIX###tblWorkflowStep (
  ID int unsigned NOT NULL auto_increment,
  `Worktime` float NOT NULL default '0',
  timeAction tinyint unsigned NOT NULL default '0',
  stepCondition int unsigned NOT NULL default '0',
  workflowID int unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
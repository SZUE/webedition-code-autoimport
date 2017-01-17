CREATE TABLE ###TBLPREFIX###tblWorkflowTask (
  ID int unsigned NOT NULL auto_increment,
  userID mediumint unsigned NOT NULL default '0',
  Edit tinyint unsigned NOT NULL default '0',
  Mail tinyint unsigned NOT NULL default '0',
  stepID int unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
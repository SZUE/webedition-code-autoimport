CREATE TABLE ###TBLPREFIX###tblWorkflowTask (
  ID int unsigned NOT NULL auto_increment,
  userID int unsigned NOT NULL default '0',
  Edit int unsigned NOT NULL default '0',
  Mail int unsigned NOT NULL default '0',
  stepID int unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
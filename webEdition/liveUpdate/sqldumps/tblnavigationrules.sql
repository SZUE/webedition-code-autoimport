CREATE TABLE ###TBLPREFIX###tblnavigationrules (
  ID int unsigned NOT NULL auto_increment,
  NavigationName varchar(255) default NULL,
  NavigationID int unsigned NOT NULL default '0',
  SelectionType enum('doctype','classname') NOT NULL default 'doctype',
  FolderID int unsigned NOT NULL default '0',
  DoctypeID mediumint unsigned NOT NULL default '0',
  Categories text NOT NULL,
  ClassID smallint unsigned NOT NULL default '0',
  WorkspaceID int unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
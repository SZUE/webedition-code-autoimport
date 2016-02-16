CREATE TABLE ###TBLPREFIX###tblnavigationrules (
  ID int unsigned NOT NULL auto_increment,
  NavigationName varchar(255) default NULL,
  NavigationID int unsigned NOT NULL default '0',
  SelectionType varchar(16) NOT NULL default '',
  FolderID int unsigned NOT NULL default '0',
  DoctypeID int unsigned NOT NULL default '0',
  Categories text NOT NULL,
  ClassID int unsigned NOT NULL default '0',
  WorkspaceID int unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
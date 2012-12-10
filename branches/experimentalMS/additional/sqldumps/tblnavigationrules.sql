CREATE TABLE tblnavigationrules (
  ID int  NOT NULL IDENTITY(1,1),
  NavigationName varchar(255) default NULL,
  NavigationID int  NOT NULL default '0',
  SelectionType varchar(16) NOT NULL default '',
  FolderID int  NOT NULL default '0',
  DoctypeID int  NOT NULL default '0',
  Categories text NOT NULL,
  ClassID int  NOT NULL default '0',
  WorkspaceID int  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
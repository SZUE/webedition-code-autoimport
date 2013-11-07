CREATE TABLE ###TBLPREFIX###tblObjectFiles (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Icon enum('class_folder.gif','folder.gif','objectFile.gif') NOT NULL,
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  ContentType enum('folder','objectFile') NOT NULL,
  CreationDate int(11) unsigned NOT NULL default '0',
  ModDate int(11) unsigned NOT NULL default '0',
  `Path` varchar(255) NOT NULL default '',
  Url varchar(255) NOT NULL default '',
  TriggerID int(11) unsigned NOT NULL default '0',
  CreatorID int(11) unsigned NOT NULL default '0',
  ModifierID int(11) unsigned NOT NULL default '0',
  RestrictOwners tinyint(1) unsigned NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  Workspaces varchar(1000) NOT NULL default '',
  ExtraWorkspaces varchar(1000) NOT NULL default '',
  ExtraWorkspacesSelected varchar(1000) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  ExtraTemplates varchar(255) NOT NULL default '',
  TableID int(11) unsigned NOT NULL default '0',
  ObjectID bigint(20) unsigned NOT NULL default '0',
  Category text NOT NULL,
  ClassName enum('we_class_folder','we_objectFile') NOT NULL,
  IsClassFolder tinyint(1) unsigned NOT NULL default '0',
  IsNotEditable tinyint(1) unsigned NOT NULL default '0',
  Published int(11) unsigned NOT NULL default '0',
  IsSearchable tinyint(1) unsigned NOT NULL default '1',
  `Charset` varchar(64) default NULL,
  `Language` varchar(5) default NULL,
  WebUserID bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (ID),
  KEY Path (Path),
  KEY WebUserID (WebUserID),
  KEY TableID (TableID),
  KEY Url (Url)
) ENGINE=MyISAM;

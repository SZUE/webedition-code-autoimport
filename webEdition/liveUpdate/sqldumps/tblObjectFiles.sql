###UPDATEDROPCOL(ObjectID,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(IsNotEditable,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblObjectFiles)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblObjectFiles (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
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
  Category text NOT NULL,
  ClassName enum('we_class_folder','we_objectFile') NOT NULL,
  IsClassFolder tinyint(1) unsigned NOT NULL default '0',
  Published int(11) unsigned NOT NULL default '0',
  IsSearchable tinyint(1) unsigned NOT NULL default '1',
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  Language varchar(5) default NULL,
  WebUserID bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (ID),
  KEY Path (Path),
  KEY WebUserID (WebUserID),
  KEY TableID (TableID),
  KEY Url (Url)
) ENGINE=MyISAM;

/* query separator */
###UPDATEDROPCOL(OF_IsSearchable,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(OF_Charset,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(OF_WebUserID,###TBLPREFIX###tblObjectFiles)###

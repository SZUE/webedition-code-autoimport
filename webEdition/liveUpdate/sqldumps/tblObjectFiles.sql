###UPDATEDROPCOL(ObjectID,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(IsNotEditable,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblObjectFiles)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblObjectFiles (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  ContentType enum('folder','objectFile') NOT NULL,
  CreationDate int unsigned NOT NULL default '0',
  ModDate int unsigned NOT NULL default '0',
  `Path` varchar(1023) NOT NULL default '',
  Url varchar(255) NOT NULL default '',
  TriggerID int unsigned NOT NULL default '0',
  CreatorID int unsigned NOT NULL default '0',
  ModifierID int unsigned NOT NULL default '0',
  RestrictOwners tinyint unsigned NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  Workspaces varchar(1000) NOT NULL default '',
  ExtraWorkspaces varchar(1000) NOT NULL default '',
  ExtraWorkspacesSelected varchar(1000) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  ExtraTemplates varchar(255) NOT NULL default '',
  TableID int unsigned NOT NULL default '0',
  Category text NOT NULL,
  ClassName enum('we_class_folder','we_objectFile') NOT NULL,
  IsClassFolder tinyint unsigned NOT NULL default '0',
  Published int unsigned NOT NULL default '0',
  IsSearchable tinyint unsigned NOT NULL default '1',
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  Language varchar(5) default NULL,
  WebUserID bigint unsigned NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY Path (Path),
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
/* query separator */
###UPDATEDROPCOL(NCAddress,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(NCPerson,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(NCAddressHeadline,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(NCPersonHeadline,###TBLPREFIX###tblObjectFiles)###
/* query separator */
###UPDATEDROPCOL(NCAddressOpen,###TBLPREFIX###tblObjectFiles)###

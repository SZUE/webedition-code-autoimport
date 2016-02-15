###UPDATEDROPCOL(IsNotEditable,###TBLPREFIX###tblversions)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblversions)###
/* query separator */
###UPDATEDROPCOL(Filehash,###TBLPREFIX###tblversions)###
/* query separator */
###UPDATEDROPCOL(IP,###TBLPREFIX###tblversions)###
/* query separator */
###UPDATEDROPCOL(Browser,###TBLPREFIX###tblversions)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblversions (
  ID bigint(20) unsigned NOT NULL auto_increment,
  documentID int(11) unsigned NOT NULL,
  documentTable tinytext NOT NULL,
  documentElements longblob NOT NULL,
  documentScheduler blob NOT NULL,
  documentCustomFilter blob NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `status` enum('saved','published','unpublished','deleted') NOT NULL,
  version mediumint(6) unsigned NOT NULL,
  binaryPath varchar(255) NOT NULL,
  modifications tinytext NOT NULL,
  modifierID int(11) unsigned NOT NULL,
  /*IP varchar(40) NOT NULL,
  Browser tinytext NOT NULL,*/
  ContentType enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile','video/*','audio/*') NOT NULL,
  Text tinytext NOT NULL,
  ParentID int unsigned NOT NULL,
  CreationDate int unsigned NOT NULL,
  CreatorID int(11) unsigned NOT NULL,
  Path tinytext NOT NULL,
  TemplateID int(11) unsigned NOT NULL,
  Filename tinytext NOT NULL,
  Extension tinytext NOT NULL,
  IsDynamic tinyint(1) unsigned NOT NULL,
  IsSearchable tinyint(1) unsigned NOT NULL,
  ClassName ENUM('we_flashDocument','we_folder','we_htmlDocument','we_imageDocument','we_otherDocument','we_textDocument','we_webEditionDocument','we_quicktimeDocument','we_document_video','we_document_audio','we_class_folder','we_objectFile','we_object','we_template') NOT NULL,
  DocType smallint unsigned NOT NULL,
  Category text NOT NULL,
  RestrictOwners tinyint(1) unsigned NOT NULL,
  Owners varchar(255) NOT NULL,
  OwnersReadOnly text NOT NULL,
  Language char(5) NOT NULL,
  WebUserID bigint unsigned NOT NULL,
  Workspaces text NOT NULL,
  ExtraWorkspaces text NOT NULL,
  ExtraWorkspacesSelected text NOT NULL,
  Templates tinytext NOT NULL,
  ExtraTemplates tinytext NOT NULL,
  MasterTemplateID int(11) unsigned NOT NULL default '0',
  TableID mediumint unsigned NOT NULL,
  ObjectID bigint unsigned NOT NULL,
  IsClassFolder tinyint(1) unsigned NOT NULL,
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  active tinyint(1) unsigned NOT NULL,
  fromScheduler tinyint(1) unsigned NOT NULL,
  fromImport tinyint(1) unsigned NOT NULL,
  resetFromVersion bigint(20) unsigned NOT NULL,
  InGlossar tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY documentID (documentID,documentTable(64),version),
  KEY timestamp (timestamp,CreationDate),
  KEY binaryPath (binaryPath),
  KEY version (version)
) ENGINE=MyISAM ;

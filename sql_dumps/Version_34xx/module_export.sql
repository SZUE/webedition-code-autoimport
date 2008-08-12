-- MySQL dump 9.11
--
-- Host: localhost    Database: we3
-- ------------------------------------------------------
-- Server version	4.0.20

--
-- Table structure for table `tblexport`
--

CREATE TABLE tblexport (
  ID bigint(20) NOT NULL auto_increment,
  ParentID bigint(20) NOT NULL default '0',
  Text varchar(255) NOT NULL default '',
  Icon varchar(255) NOT NULL default 'link.gif',
  IsFolder tinyint(1) NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  ExportTo varchar(255) NOT NULL default '',
  ServerPath varchar(255) NOT NULL default '',
  Filename varchar(255) NOT NULL default '',
  Selection varchar(255) NOT NULL default '',
  SelectionType varchar(255) NOT NULL default '',
  DocType varchar(255) NOT NULL default '',
  Folder bigint(20) NOT NULL default '0',
  ClassName varchar(255) NOT NULL default '',
  Categorys varchar(255) NOT NULL default '',
  selDocs text NOT NULL,
  selTempl text NOT NULL,
  selObjs text NOT NULL,
  selClasses text NOT NULL,
  HandleDefTemplates tinyint(1) NOT NULL default '0',
  HandleDocIncludes tinyint(1) NOT NULL default '0',
  HandleObjIncludes tinyint(1) NOT NULL default '0',
  HandleDocLinked tinyint(1) NOT NULL default '0',
  HandleDefClasses tinyint(1) NOT NULL default '0',
  HandleObjEmbeds tinyint(1) NOT NULL default '0',
  HandleDoctypes tinyint(1) NOT NULL default '0',
  HandleCategorys tinyint(1) NOT NULL default '0',
  HandleOwners tinyint(1) NOT NULL default '0',
  ExportDepth varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

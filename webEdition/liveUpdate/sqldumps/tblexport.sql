###UPDATEDROPCOL(Icon,###TBLPREFIX###tblexport)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblexport (
  ID smallint unsigned NOT NULL auto_increment,
  ParentID smallint unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  ExportType enum('WE','XML','CSV') default 'WE',
  ExportTo enum('','local','server') NOT NULL default '',
  ServerPath varchar(255) NOT NULL default '',
  Filename varchar(255) NOT NULL default '',
  Extension enum('.xml','.csv','') NOT NULL default '.xml',
  Selection enum('auto','manual') NOT NULL default 'auto',
  SelectionType enum('doctype','classname','document') NOT NULL default 'doctype',
  DocType mediumint unsigned NOT NULL,
  Folder smallint unsigned NOT NULL default '0',
  ClassName varchar(255) NOT NULL default '',
  Categorys text NOT NULL,
  selDocs text NOT NULL,
  selTempl text NOT NULL,
  selObjs text NOT NULL,
  selClasses text NOT NULL,
  HandleDefTemplates tinyint unsigned NOT NULL default '0',
  HandleDocIncludes tinyint unsigned NOT NULL default '0',
  HandleObjIncludes tinyint unsigned NOT NULL default '0',
  HandleDocLinked tinyint unsigned NOT NULL default '0',
  HandleDefClasses tinyint unsigned NOT NULL default '0',
  HandleObjEmbeds tinyint unsigned NOT NULL default '0',
  HandleDoctypes tinyint unsigned NOT NULL default '0',
  HandleCategorys tinyint unsigned NOT NULL default '0',
  ExportDepth tinyint unsigned NOT NULL default '0',
  HandleOwners tinyint unsigned NOT NULL default '0',
  HandleNavigation tinyint unsigned NOT NULL default '0',
  HandleThumbnails tinyint unsigned NOT NULL default '0',
  XMLCdata tinyint unsigned NOT NULL default '1',
  CSVDelimiter enum('semicolon','comma','colon','tab','space') NOT NULL default 'comma',
  CSVLineend enum('windows','unix','mac') NOT NULL default 'windows',
  CSVEnclose enum('singlequote','doublequote') NOT NULL default 'doublequote',
  CSVFieldnames tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
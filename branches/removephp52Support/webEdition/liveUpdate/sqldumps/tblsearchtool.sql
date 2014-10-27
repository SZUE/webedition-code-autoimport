###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblsearchtool WHERE predefined=1 AND ID>13;
/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblsearchtool SET ID=ID+25 WHERE ID<25;
/* query separator */

CREATE TABLE ###TBLPREFIX###tblsearchtool (
  ID smallint(4) unsigned NOT NULL auto_increment,
  ParentID smallint(4) unsigned NOT NULL default '0',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  Icon ENUM('folder.gif','Suche.gif') NOT NULL default 'Suche.gif',
  `Path` varchar(255) NOT NULL,
  `Text` varchar(255) NOT NULL,
  predefined tinyint(1) unsigned NOT NULL,
  folderIDDoc int(11) unsigned NOT NULL,
  folderIDTmpl int(11) unsigned NOT NULL,
  searchDocSearch varchar(255) NOT NULL,
  searchTmplSearch varchar(255) NOT NULL,
  searchForTextDocSearch varchar(255) NOT NULL,
  searchForTitleDocSearch tinyint(1) unsigned NOT NULL,
  searchForContentDocSearch varchar(255) NOT NULL,
  searchForTextTmplSearch varchar(255) NOT NULL,
  searchForContentTmplSearch varchar(255) NOT NULL,
  anzahlDocSearch tinyint(4) unsigned NOT NULL,
  anzahlTmplSearch tinyint(4) unsigned NOT NULL,
  anzahlAdvSearch tinyint(4) unsigned NOT NULL,
  setViewDocSearch tinyint(1) unsigned NOT NULL,
  setViewTmplSearch tinyint(1) unsigned NOT NULL,
  setViewAdvSearch tinyint(1) unsigned NOT NULL,
  OrderDocSearch varchar(64) NOT NULL,
  OrderTmplSearch varchar(64) NOT NULL,
  OrderAdvSearch varchar(64) NOT NULL,
  searchAdvSearch varchar(255) NOT NULL,
  locationAdvSearch varchar(255) NOT NULL,
  searchFieldsAdvSearch varchar(255) NOT NULL,
  search_tables_advSearch varchar(255) NOT NULL,
  activTab tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (ID),
  UNIQUE KEY Path (Path)
) ENGINE=MyISAM AUTO_INCREMENT=25;
/* query separator */
REPLACE INTO ###TBLPREFIX###tblsearchtool (ID,`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(1,0, 1, 'folder.gif', '/_PREDEF_', 'p', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(2,1, 1, 'folder.gif', '/_PREDEF_/document', 'p', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(3,1, 1, 'folder.gif', '/_PREDEF_/object', 'p', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(4,2, 0, 'Suche.gif', '/_PREDEF_/document/unpublished', 'p', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(5,2, 0, 'Suche.gif', '/_PREDEF_/document/static', 'p', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:8:"statisch";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(6,2, 0, 'Suche.gif', '/_PREDEF_/document/dynamic', 'p', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:9:"dynamisch";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(7,3, 0, 'Suche.gif', '/_PREDEF_/object/unpublished', 'p', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(8,0, 1, 'folder.gif', '/_CUSTOM_', 'c', 1, 0, 0, '', '', 0, 0, 0, 0, 0, 10, 10, 10, 0, 0, 0, '', '', '', '', '', '', '', 4),
(9,0,1,'folder.gif', '/_VERSION_','v', 1, 0, 0, '', '', 0, 0, 0, 0, 0, 10, 10, 10, 0, 0, 0, '', '', '', '', '', '', '', 4),
(10,9, 1, 'folder.gif', '/_VERSION_/document', 'v', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(11,9, 1, 'folder.gif', '/_VERSION_/object', 'v', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(12,10, 0, 'Suche.gif', '/_VERSION_/document/deleted', 'v', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(13,11, 0, 'Suche.gif', '/_VERSION_/object/deleted', 'v', 1, 0, 0, 'a:0:{}', 'a:0:{}', 1, 1, 1, 1, 1, 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3);
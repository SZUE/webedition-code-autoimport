###UPDATEONLY###CREATE TEMPORARY TABLE IF NOT EXISTS _delKeys(
  ID bigint unsigned
)ENGINE = MEMORY;

/* query separator */
###UPDATEONLY###INSERT INTO _delKeys SELECT s.ID FROM ###TBLPREFIX###tblsearchtool s, ###TBLPREFIX###tblsearchtool t WHERE s.Path=t.Path AND s.ID>t.ID;
/* query separator */
###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblsearchtool WHERE ID IN (SELECT ID FROM _delKeys);
/* query separator */

###UPDATEONLY###DROP TEMPORARY TABLE IF EXISTS _delKeys;
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
) ENGINE=MyISAM ;
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(0, 1, 'folder.gif', '/Vordefinierte Suchanfragen', 'Vordefinierte Suchanfragen', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(1, 1, 'folder.gif', '/Vordefinierte Suchanfragen/Dokumente', 'Dokumente', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(1, 1, 'folder.gif', '/Vordefinierte Suchanfragen/Objekte', 'Objekte', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Unveroeffentlichte Dokumente', 'Unveroeffentlichte Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Statische Dokumente', 'Statische Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:8:"statisch";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Dynamische Dokumente', 'Dynamische Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:9:"dynamisch";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(3, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Objekte/Unveroeffentlichte Objekte', 'Unveroeffentlichte Objekte', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(0, 1, 'folder.gif', '/Eigene Suchanfragen', 'Eigene Suchanfragen', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '0', 0, '0', '0', '0', 10, 10, 10, 0, 0, 0, '', '', '', 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:2:"ID";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblobjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblobject";s:1:"0";}', 4);
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool SET IsFolder=1, Icon='folder.gif', Path='/Versionen',Text='Versionen', predefined=1,folderIDDoc=0, folderIDTmpl=0, searchDocSearch='a:1:{i:0;s:0:"";}', searchTmplSearch='a:1:{i:0;s:0:"";}', searchForTextDocSearch=0, searchForTitleDocSearch=0, searchForContentDocSearch=0, searchForTextTmplSearch=0, searchForContentTmplSearch=0, anzahlDocSearch=10, anzahlTmplSearch=10, anzahlAdvSearch=10, setViewDocSearch=0,setViewTmplSearch=0, setViewAdvSearch=0,OrderDocSearch='', OrderTmplSearch='', OrderAdvSearch='', searchAdvSearch='a:1:{i:0;s:0:"";}',locationAdvSearch='a:1:{i:0;s:7:"CONTAIN";}',searchFieldsAdvSearch='a:1:{i:0;s:2:"ID";}', search_tables_advSearch='a:4:{s:7:"tblFile";s:1:"1";s:14:"tblobjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblobject";s:1:"0";}', activTab=4;
/* query separator */
###INSTALLONLY###SELECT @vid:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen' LIMIT 0,1;
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@vid, 1, 'folder.gif', '/Versionen/Dokumente', 'Dokumente', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(@vid, 1, 'folder.gif', '/Versionen/Objekte', 'Objekte', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4);
/* query separator */
###INSTALLONLY###SELECT @vdid:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen/Dokumente' LIMIT 0,1;
/* query separator */
###INSTALLONLY###SELECT @void:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen/Objekte' LIMIT 0,1;
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@vdid, 0, 'Suche.gif', '/Versionen/Dokumente/geloeschte Dokumente', 'geloeschte Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3);
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@void, 0, 'Suche.gif', '/Versionen/Objekte/geloeschte Objekte', 'geloeschte Objekte', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3);

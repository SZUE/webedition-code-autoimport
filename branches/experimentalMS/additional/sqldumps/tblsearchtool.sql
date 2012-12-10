CREATE TABLE tblsearchtool (
  ID bigint  NOT NULL IDENTITY(1,1),
  ParentID bigint  NOT NULL default '0',
  IsFolder tinyint  NOT NULL default '0',
  Icon varchar(255) NOT NULL,
  Path varchar(255) NOT NULL,
  "Text" varchar(255) NOT NULL,
  predefined tinyint  NOT NULL,
  folderIDDoc int  NOT NULL,
  folderIDTmpl int  NOT NULL,
  searchDocSearch varchar(255) NOT NULL,
  searchTmplSearch varchar(255) NOT NULL,
  searchForTextDocSearch varchar(255) NOT NULL,
  searchForTitleDocSearch tinyint  NOT NULL,
  searchForContentDocSearch varchar(255) NOT NULL,
  searchForTextTmplSearch varchar(255) NOT NULL,
  searchForContentTmplSearch varchar(255) NOT NULL,
  anzahlDocSearch tinyint  NOT NULL,
  anzahlTmplSearch tinyint  NOT NULL,
  anzahlAdvSearch tinyint  NOT NULL,
  setViewDocSearch tinyint  NOT NULL,
  setViewTmplSearch tinyint  NOT NULL,
  setViewAdvSearch tinyint  NOT NULL,
  OrderDocSearch varchar(64) NOT NULL,
  OrderTmplSearch varchar(64) NOT NULL,
  OrderAdvSearch varchar(64) NOT NULL,
  searchAdvSearch varchar(255) NOT NULL,
  locationAdvSearch varchar(255) NOT NULL,
  searchFieldsAdvSearch varchar(255) NOT NULL,
  search_tables_advSearch varchar(255) NOT NULL,
  activTab tinyint  NOT NULL default '1',
  PRIMARY KEY  (ID),
  CONSTRAINT Path UNIQUE(Path)
) 
INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(0, 1, 'folder.gif', '/Vordefinierte Suchanfragen', 'Vordefinierte Suchanfragen', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(1, 1, 'folder.gif', '/Vordefinierte Suchanfragen/Dokumente', 'Dokumente', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(1, 1, 'folder.gif', '/Vordefinierte Suchanfragen/Objekte', 'Objekte', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Unveroeffentlichte Dokumente', 'Unveroeffentlichte Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 's:24:"a:1:{i:0;s:7:"CONTAIN";}";', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Statische Dokumente', 'Statische Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:8:"statisch";}', 's:32:"s:24:"a:1:{i:0;s:7:"CONTAIN";}";";', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(2, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Dokumente/Dynamische Dokumente', 'Dynamische Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:9:"dynamisch";}', 's:32:"s:24:"a:1:{i:0;s:7:"CONTAIN";}";";', 'a:1:{i:0;s:11:"Speicherart";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(3, 0, 'Suche.gif', '/Vordefinierte Suchanfragen/Objekte/Unveroeffentlichte Objekte', 'Unveroeffentlichte Objekte', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 25, 25, 25, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:17:"geparkt_geaendert";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:4:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3),
(0, 1, 'folder.gif', '/Eigene Suchanfragen', 'Eigene Suchanfragen', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '0', 0, '0', '0', '0', 10, 10, 10, 0, 0, 0, '', '', '', 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:2:"ID";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblobjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblobject";s:1:"0";}', 4);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(0, 1, 'folder.gif', '/Versionen', 'Versionen', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '0', 0, '0', '0', '0', 10, 10, 10, 0, 0, 0, '', '', '', 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:2:"ID";}', 'a:4:{s:7:"tblFile";s:1:"1";s:14:"tblobjectFiles";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblobject";s:1:"0";}',4);
/* query separator */
SELECT @vid:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen' LIMIT 0,1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@vid, 1, 'folder.gif', '/Versionen/Dokumente', 'Dokumente', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4),
(@vid, 1, 'folder.gif', '/Versionen/Objekte', 'Objekte', 1, 0, 0, '', '', '', 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '',4);
/* query separator */
SELECT @vdid:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen/Dokumente' LIMIT 0,1;
/* query separator */
SELECT @void:=ID FROM ###TBLPREFIX###tblsearchtool WHERE Path='/Versionen/Objekte' LIMIT 0,1;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@vdid, 0, 'Suche.gif', '/Versionen/Dokumente/geloeschte Dokumente', 'gelöschte Dokumente', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"1";s:14:"tblObjectFiles";s:1:"0";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblsearchtool (`ParentID`, `IsFolder`, `Icon`, `Path`, `Text`, `predefined`, `folderIDDoc`, `folderIDTmpl`, `searchDocSearch`, `searchTmplSearch`, `searchForTextDocSearch`, `searchForTitleDocSearch`, `searchForContentDocSearch`, `searchForTextTmplSearch`, `searchForContentTmplSearch`, `anzahlDocSearch`, `anzahlTmplSearch`, `anzahlAdvSearch`, `setViewDocSearch`, `setViewTmplSearch`, `setViewAdvSearch`, `OrderDocSearch`, `OrderTmplSearch`, `OrderAdvSearch`, `searchAdvSearch`, `locationAdvSearch`, `searchFieldsAdvSearch`, `search_tables_advSearch`, `activTab`) VALUES
(@void, 0, 'Suche.gif', '/Versionen/Objekte/geloeschte Objekte', 'gelöschte Objekte', 1, 0, 0, 'a:1:{i:0;s:0:"";}', 'a:1:{i:0;s:0:"";}', '1', 1, '1', '1', '1', 10, 10, 10, 0, 0, 0, 'Text', 'Text', 'Text', 'a:1:{i:0;s:7:"deleted";}', 'a:1:{i:0;s:7:"CONTAIN";}', 'a:1:{i:0;s:6:"Status";}', 'a:5:{s:7:"tblFile";s:1:"0";s:14:"tblObjectFiles";s:1:"1";s:11:"tblversions";s:1:"1";s:12:"tblTemplates";s:1:"0";s:9:"tblObject";s:1:"0";}',3);

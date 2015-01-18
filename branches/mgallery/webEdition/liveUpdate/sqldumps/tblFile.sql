###UPDATEONLY### UPDATE ###TBLPREFIX###tblFile SET Path=REPLACE(Path,"//","/") WHERE Path LIKE "%//%"
/* query separator */
###UPDATEDROPCOL(temp_doc_type,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(Deleted,###TBLPREFIX###tblFile)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblFile (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Icon enum('pdf.gif','zip.gif','word.gif','excel.gif','powerpoint.gif','prog.gif','link.gif','image.gif','html.gif','we_dokument.gif','javascript.gif','css.gif','htaccess.gif','folder.gif','flashmovie.gif','quicktime.gif','odg.gif','video.svg','audio.svg') NOT NULL default 'prog.gif',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  ContentType enum('','image/*','text/html','text/webedition','text/js','text/css','text/htaccess','text/plain','folder','application/x-shockwave-flash','application/*','video/quicktime','text/xml','video/*','audio/*') NOT NULL default '',
  CreationDate int(11) unsigned NOT NULL default '0',
  ModDate int(11) unsigned NOT NULL default '0',
  RebuildDate int(11) unsigned NOT NULL default '0',
  `Path` varchar(255) NOT NULL default '',
  Filehash char(40) NOT NULL default '',
  TemplateID int(11) unsigned NOT NULL default '0',
  temp_template_id int(11) unsigned NOT NULL default '0',
  Filename varchar(255) NOT NULL default '',
  Extension varchar(16) NOT NULL default '',
  IsDynamic tinyint(1) unsigned NOT NULL default '0',
  IsSearchable tinyint(1) unsigned NOT NULL default '0',
  DocType smallint(6) unsigned NOT NULL,
  ClassName ENUM('we_flashDocument','we_folder','we_htmlDocument','we_imageDocument','we_otherDocument','we_textDocument','we_webEditionDocument','we_quicktimeDocument','we_document_video','we_document_audio') NOT NULL default 'we_textDocument',
  Category text NOT NULL default '',
  temp_category text NOT NULL default '',
  Published int(11) unsigned NOT NULL default '0',
  CreatorID int(11) unsigned NOT NULL default '0',
  ModifierID int(11) unsigned NOT NULL default '0',
  RestrictOwners tinyint(1) unsigned NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  `Language` varchar(5) NOT NULL default '',
  WebUserID bigint(20) unsigned NOT NULL default '0',
  listview tinyint(1) unsigned NOT NULL default '0',
  InGlossar tinyint(1) unsigned NOT NULL default '0',
	urlMap varchar(100) NOT NULL default '',
	parseFile tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY  (ID),
  KEY Path (Path(30),IsFolder),
  KEY WebUserID (WebUserID),
	KEY urlMap (urlMap),
	KEY TemplateID (TemplateID,IsDynamic),
	KEY ParentID(ParentID,IsSearchable,Published)
) ENGINE=MyISAM;

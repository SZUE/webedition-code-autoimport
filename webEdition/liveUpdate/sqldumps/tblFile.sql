###UPDATEONLY### UPDATE ###TBLPREFIX###tblFile SET Path=REPLACE(Path,"//","/") WHERE Path LIKE "%//%"
/* query separator */

CREATE TABLE ###TBLPREFIX###tblFile (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Icon enum('','pdf.gif','zip.gif','word.gif','excel.gif','powerpoint.gif','prog.gif','link.gif','image.gif','html.gif','we_dokument.gif','javascript.gif','css.gif','htaccess.gif','folder.gif','flashmovie.gif','quicktime.gif','odg.gif') NOT NULL,
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  ContentType enum('','image/*','text/html','text/webedition','text/js','text/css','text/htaccess','text/plain','folder','application/x-shockwave-flash','application/*','video/quicktime','application/*','text/xml') NOT NULL default '',
  CreationDate int(11) unsigned NOT NULL default '0',
  ModDate int(11) unsigned NOT NULL default '0',
  RebuildDate int(11) unsigned NOT NULL default '0',
  `Path` varchar(255) NOT NULL default '',
  Filehash varchar(40) NOT NULL default '',
  TemplateID int(11) unsigned NOT NULL default '0',
  temp_template_id int(11) unsigned NOT NULL default '0',
  Filename varchar(255) NOT NULL default '',
  Extension varchar(16) NOT NULL default '',
  IsDynamic tinyint(1) unsigned NOT NULL default '0',
  IsSearchable tinyint(1) unsigned NOT NULL default '0',
  DocType smallint(6) NOT NULL,
  temp_doc_type smallint(6) NOT NULL,
  ClassName varchar(64) NOT NULL default '',
  Category text NULL default NULL,
  temp_category text NULL default NULL,
  Deleted int(11) unsigned NOT NULL default '0',
  Published int(11) unsigned NOT NULL default '0',
  CreatorID bigint(20) unsigned NOT NULL default '0',
  ModifierID bigint(20) unsigned NOT NULL default '0',
  RestrictOwners tinyint(1) unsigned NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  documentArray text NOT NULL,
  `Language` varchar(5) NOT NULL default '',
  WebUserID bigint(20) unsigned NOT NULL default '0',
  listview tinyint(1) unsigned NOT NULL default '0',
  InGlossar tinyint(1) unsigned NOT NULL default '0',
	urlMap varchar(100) NOT NULL default '',
	PRIMARY KEY  (ID),
  KEY Path (Path),
  KEY WebUserID (WebUserID),
	KEY urlMap (urlMap)
) ENGINE=MyISAM;

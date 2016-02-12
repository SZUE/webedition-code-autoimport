###UPDATEONLY### UPDATE ###TBLPREFIX###tblFile SET Path=REPLACE(Path,"//","/") WHERE Path LIKE "%//%"
/* query separator */
###UPDATEDROPCOL(temp_doc_type,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(Deleted,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblFile)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblFile (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  IsProtected tinyint(1) unsigned NOT NULL default '0',
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
  Category text NOT NULL,
  temp_category text NOT NULL,
  Published int(11) unsigned NOT NULL default '0',
  CreatorID int(11) unsigned NOT NULL default '0',
  ModifierID int(11) unsigned NOT NULL default '0',
  RestrictOwners tinyint(1) unsigned NOT NULL default '0',
  Owners tinytext NOT NULL,
  OwnersReadOnly text NOT NULL,
  `Language` varchar(5) NOT NULL default '',
  WebUserID bigint(20) unsigned NOT NULL default '0',
	viewType enum('list','icons') NOT NULL default 'list',
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

/* query separator */
###ONCOL(listview,###TBLPREFIX###tblFile) UPDATE ###TBLPREFIX###tblFile SET viewType="icons" WHERE listview=1;###
/* query separator */
###UPDATEDROPCOL(listview,###TBLPREFIX###tblFile)###

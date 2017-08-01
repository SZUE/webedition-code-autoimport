###ONCOL(Icon,###TBLPREFIX###tblFile) UPDATE ###TBLPREFIX###tblFile SET Path=REPLACE(Path,"//","/") WHERE Path LIKE "%//%";###
/* query separator */
###UPDATEDROPCOL(temp_doc_type,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(Deleted,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblFile SET ClassName="we_otherDocument",ContentType="application/*" WHERE ClassName="we_quicktimeDocument";
/* query separator */

CREATE TABLE ###TBLPREFIX###tblFile (
	ID int unsigned NOT NULL auto_increment,
	ParentID int unsigned NOT NULL default '0',
	`Text` varchar(255) NOT NULL default '',
	IsFolder tinyint unsigned NOT NULL default '0',
	IsProtected tinyint unsigned NOT NULL default '0',
	ContentType enum('','image/*','text/html','text/webedition','text/js','text/css','text/htaccess','text/plain','folder','application/x-shockwave-flash','application/*','text/xml','video/*','audio/*') NOT NULL default '',
	CreationDate int unsigned NOT NULL default '0',
	ModDate int unsigned NOT NULL default '0',
	Path varchar(800) NOT NULL default '',
	Filehash char(40) NOT NULL default '',
	TemplateID int unsigned NOT NULL default '0',
	temp_template_id int unsigned NOT NULL default '0',
	Filename varchar(255) NOT NULL default '',
	Extension varchar(16) NOT NULL default '',
	IsDynamic tinyint unsigned NOT NULL default '0',
	IsSearchable tinyint unsigned NOT NULL default '0',
	DocType mediumint unsigned NOT NULL,
	ClassName ENUM('we_flashDocument','we_folder','we_htmlDocument','we_imageDocument','we_otherDocument','we_textDocument','we_webEditionDocument','we_document_video','we_document_audio') NOT NULL default 'we_textDocument',
	Category text NOT NULL,
	temp_category text NOT NULL,
	Published int unsigned NOT NULL default '0',
	CreatorID int unsigned NOT NULL default '0',
	ModifierID int unsigned NOT NULL default '0',
	RestrictOwners tinyint unsigned NOT NULL default '0',
	Owners tinytext NOT NULL,
	OwnersReadOnly text NOT NULL,
	`Language` char(5) NOT NULL default '',
	WebUserID bigint unsigned NOT NULL default '0',
	viewType enum('list','icons') NOT NULL default 'list',
	InGlossar tinyint unsigned NOT NULL default '0',
	urlMap varchar(100) NOT NULL default '',
	parseFile tinyint unsigned NOT NULL default '0',
	PRIMARY KEY (ID),
	UNIQUE KEY ParentID (ParentID,Filename,Extension),
	KEY WebUserID (WebUserID),
	KEY urlMap (urlMap),
	KEY TemplateID (TemplateID,IsDynamic),
	KEY searchable(ParentID,IsSearchable,Published),
	KEY Path(Path(250))
) ENGINE=MyISAM;

/* query separator */
###ONCOL(listview,###TBLPREFIX###tblFile) UPDATE ###TBLPREFIX###tblFile SET viewType="icons" WHERE listview=1;###
/* query separator */
###UPDATEDROPCOL(listview,###TBLPREFIX###tblFile)###
/* query separator */
###UPDATEDROPCOL(RebuildDate,###TBLPREFIX###tblFile)###

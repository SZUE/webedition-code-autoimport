CREATE TABLE ###TBLPREFIX###tblhistory (
  DID bigint(20) unsigned NOT NULL default '0',
  DocumentTable enum('tblFile','tblObject','tblTemplates','tblObjectFiles') NOT NULL,
  ContentType enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile') NOT NULL,
  ModDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Act enum('save') NOT NULL default 'save',
  UserName varchar(64) NOT NULL default '',
	UID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (DID,DocumentTable,ModDate,Act),
  KEY UserName (UserName,DocumentTable),
	KEY perUser (UID,DocumentTable)
) ENGINE=MyISAM;

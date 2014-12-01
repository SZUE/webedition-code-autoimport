###UPDATEDROPCOL(ID,###TBLPREFIX###tblhistory)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblhistory (
  DID bigint(20) unsigned NOT NULL default '0',
  DocumentTable enum('tblFile','tblObject','tblTemplates','tblObjectFiles') NOT NULL,
  ContentType enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile') NOT NULL,
  ModDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserName varchar(64) NOT NULL default '',
	UID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (DID,DocumentTable,UID),
	KEY perUser (UID,DocumentTable,ModDate)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblhistory) TRUNCATE TABLE ###TBLPREFIX###tblhistory;###
/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblhistory) ALTER TABLE ###TBLPREFIX###tblhistory ADD PRIMARY KEY (DID,DocumentTable,UID);###
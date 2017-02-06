###UPDATEDROPCOL(ID,###TBLPREFIX###tblhistory)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblhistory (
  DID int unsigned NOT NULL default '0',
  DocumentTable enum('tblFile','tblObject','tblTemplates','tblObjectFiles','tblVFile') NOT NULL,
  ContentType enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile','video/*','audio/*','text/weCollection') NOT NULL,
  ModDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserName varchar(64) NOT NULL default '',
	UID int unsigned NOT NULL default '0',
  PRIMARY KEY (DID,DocumentTable,UID),
	KEY perUser (UID,DocumentTable,ModDate),
	KEY moddate (DocumentTable,ModDate)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblhistory) TRUNCATE TABLE ###TBLPREFIX###tblhistory;###
/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblhistory) ALTER TABLE ###TBLPREFIX###tblhistory ADD PRIMARY KEY (DID,DocumentTable,UID);###
###UPDATEONLY### UPDATE ###TBLPREFIX###tblLink l SET l.Type="href" WHERE l.Name LIKE '%we_jkhdsf_%' AND Type="txt"
/* query separator */
###UPDATEONLY### UPDATE ###TBLPREFIX###tblLink l SET l.Type="attrib" WHERE l.Name IN('Charset','useMetaTitle','origwidth','origheight','width','height') AND Type="txt"
/* query separator */

CREATE TABLE ###TBLPREFIX###tblLink (
  DID int(11) unsigned NOT NULL default '0',
  CID int(11) unsigned NOT NULL default '0',
  `Type` varchar(16) NOT NULL default '',
  Name varchar(255) NOT NULL default '',
  DocumentTable enum('tblFile','tblTemplates','tblWebUser') NOT NULL,
  PRIMARY KEY (CID),
  KEY DID (DID,DocumentTable),
  KEY Name (Name(4)),
  KEY `Type` (`Type`)
) ENGINE=MyISAM;
/*
type field - current known
application
attrib
block
customer
date
formfield
href
img
input
linklist
list
object
text
txt
variant
variants
*/
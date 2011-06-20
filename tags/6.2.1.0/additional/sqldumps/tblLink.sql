CREATE TABLE ###TBLPREFIX###tblLink (
  DID int(11) NOT NULL default '0',
  CID int(11) NOT NULL default '0',
  `Type` varchar(16) NOT NULL default '',
  Name varchar(255) NOT NULL default '',
  DocumentTable enum('tblFile','tblTemplates') NOT NULL,
  PRIMARY KEY (CID,DocumentTable),
  KEY DID (DID),
  KEY Name (Name(4)),
  KEY `Type` (`Type`)
) ENGINE=MyISAM;

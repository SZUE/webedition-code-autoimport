###ONCOL(AutoBR,###TBLPREFIX###tblContent)UPDATE ###TBLPREFIX###tblContent c JOIN ###TBLPREFIX###tblLink l ON l.CID=c.ID SET c.BDID=c.Dat,c.Dat="" WHERE l.Name LIKE "%_intID" AND c.BDID=0 AND c.Dat!="";###
/* query separator */
###ONCOL(AutoBR,###TBLPREFIX###tblContent)UPDATE ###TBLPREFIX###tblContent c SET c.BDID=0 WHERE c.BDID>0 AND c.Dat!="";###
/* query separator */
###UPDATEDROPCOL(AutoBR,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEDROPCOL(LanguageID,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEDROPCOL(IsBinary,###TBLPREFIX###tblContent)###
/* query separator */
###ONCOL(dHash,###TBLPREFIX###tblContent)UPDATE ###TBLPREFIX###tblContent SET BDID=Dat,Dat=NULL WHERE Dat!="" AND ID IN (SELECT CID FROM ###TBLPREFIX###tblLink WHERE DocumentTable='tblFile'  AND nHash IN (x'b435e227d5dd201e1768b2bcb2e0aa81',x'eaae26a6fb20ed3ef54fb23bfa0b1fcc',x'11b4278c7e5a79003db77272c1ed2cf5',x'5e8b6e54ab9f39e8df3a49d1fa478324',x'58b79779851d8d14bbd71d6bd2ad0cba',x'09d2a3e9b7efc29e9a998d7ae84cca87',x'b6b8646a49103a66f9d9e2aae212bdbe',x'fe40feec71672d515faa242b1cff2165',x'c6e9ec12d4d8b4e75e596aaf47772a3d'));
/* query separator */

CREATE TABLE ###TBLPREFIX###tblContent (
  ID int unsigned NOT NULL auto_increment,
  DID int unsigned NOT NULL default '0',
  `Type` enum('attrib','block','checkbox','collection','customer','date','formfield','href','img','input','LanguageDocName','link','linklist','object','select','txt','variant','variants','video') NOT NULL default 'txt',
  `Name` varchar(255) NOT NULL default '',
	nHash binary(16) NOT NULL,
  DocumentTable enum('tblFile','tblTemplates','tblWebUser') NOT NULL,
  BDID int unsigned NOT NULL default '0',
  Dat longtext default NULL,
  PRIMARY KEY (ID),
  KEY BDID (BDID),
	KEY nHash(nHash,DocumentTable)
) ENGINE=MyISAM;

/* query separator */
###UPDATEDROPKEY(dHash,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEDROPCOL(dHash,###TBLPREFIX###tblContent)###

/* query separator */
###INSTALLONLY###ALTER TABLE ###TBLPREFIX###tblContent ADD UNIQUE KEY prim(DID,DocumentTable,nHash);

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
CREATE TABLE ###TBLPREFIX###tblContent (
  ID int unsigned NOT NULL auto_increment,
  BDID int unsigned NOT NULL default '0',
  Dat longtext default NULL,
	dHash binary(16) NOT NULL,
  PRIMARY KEY (ID),
  KEY BDID (BDID),
	KEY dHash(dHash)
) ENGINE=MyISAM;

/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblContent SET BDID=Dat,Dat=NULL,dHash=x'00000000000000000000000000000000' WHERE ID IN (SELECT CID FROM ###TBLPREFIX###tblLink WHERE DocumentTable='tblFile' AND nHash IN (x'b435e227d5dd201e1768b2bcb2e0aa81',x'eaae26a6fb20ed3ef54fb23bfa0b1fcc',x'11b4278c7e5a79003db77272c1ed2cf5',x'5e8b6e54ab9f39e8df3a49d1fa478324',x'58b79779851d8d14bbd71d6bd2ad0cba',x'09d2a3e9b7efc29e9a998d7ae84cca87',x'b6b8646a49103a66f9d9e2aae212bdbe',x'fe40feec71672d515faa242b1cff2165',x'c6e9ec12d4d8b4e75e596aaf47772a3d')) AND dHash!=x'00000000000000000000000000000000';
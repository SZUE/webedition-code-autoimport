###UPDATEDROPCOL(LanguageID,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEDROPCOL(IsBinary,###TBLPREFIX###tblContent)###
/* query separator */
###UPDATEONLY### UPDATE ###TBLPREFIX###tblContent c JOIN ###TBLPREFIX###tblLink l ON l.CID=c.ID SET c.BDID=c.Dat,c.Dat="" WHERE l.Name LIKE '%_intID' AND c.BDID=0 AND c.Dat!=""
/* query separator */
###UPDATEONLY### UPDATE ###TBLPREFIX###tblContent c SET c.BDID=0 WHERE c.BDID>0 AND c.Dat!=""
/* query separator */
CREATE TABLE ###TBLPREFIX###tblContent (
  ID int(11) unsigned NOT NULL auto_increment,
  BDID int(11) unsigned NOT NULL default '0',
  Dat longtext,
  AutoBR enum('on','off') NOT NULL default 'off',
  PRIMARY KEY (ID),
  KEY BDID (BDID)
)

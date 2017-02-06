###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="href" WHERE l.Name LIKE "%we_jkhdsf_%" AND Type="txt";###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="txt" WHERE l.Type IN ('text','application');###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="attrib" WHERE l.Name IN('Charset','useMetaTitle','origwidth','origheight','width','height') AND Type="txt";###
/* query separator */
###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblLink l SET l.Type="block" WHERE l.Type='list';###
/* query separator */
###UPDATEDROPKEY(Type,###TBLPREFIX###tblLink)###
/* query separator */
###UPDATEDROPKEY(DID,###TBLPREFIX###tblLink)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblLink (
  DID int unsigned NOT NULL default '0',
  CID int unsigned NOT NULL default '0',
  `Type` enum('attrib','block','checkbox','collection','customer','date','formfield','href','img','input','LanguageDocName','link','linklist','object','txt','variant','variants','video') NOT NULL default 'txt',
  `Name` varchar(255) NOT NULL default '',
  DocumentTable enum('tblFile','tblTemplates','tblWebUser') NOT NULL,
	nHash binary(16) NOT NULL,
 	KEY CID (CID),
  KEY Name (Name(4)),
	KEY nHash(nHash,DocumentTable)
) ENGINE=MyISAM;
/* primary key is added in update*/

/* query separator */
###INSTALLONLY###ALTER TABLE ###TBLPREFIX###tblLink ADD PRIMARY KEY (DID,DocumentTable,nHash)

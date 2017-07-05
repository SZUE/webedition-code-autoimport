CREATE TABLE ###TBLPREFIX###tblthumbnails (
  ID int unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL DEFAULT '',
  Format ENUM('','jpg','png','gif') NOT NULL DEFAULT '',
  Height smallint unsigned default NULL,
  Width smallint unsigned default NULL,
	`Options` set('Ratio','Maxsize','Interlace','Fitinside','Crop','Unsharp','GaussBlur','Negate','Gray','Sepia') NOT NULL DEFAULT 'Unsharp',
	description tinytext NOT NULL,
  `Directory` varchar(1024) NOT NULL DEFAULT '',
  `Quality` tinyint unsigned NOT NULL DEFAULT  '8',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(Ratio,###TBLPREFIX###tblthumbnails)UPDATE ###TBLPREFIX###tblthumbnails SET Options=CONCAT_WS(",",IF(Ratio,"Ratio",NULL),IF(Maxsize,"Maxsize",NULL),IF(Interlace,"Interlace",NULL),IF(Fitinside,"Fitinside",NULL));###

/* query separator */
###UPDATEDROPCOL(Utilize,###TBLPREFIX###tblthumbnails)###
/* query separator */
###UPDATEDROPCOL(Ratio,###TBLPREFIX###tblthumbnails)###
/* query separator */
###UPDATEDROPCOL(Maxsize,###TBLPREFIX###tblthumbnails)###
/* query separator */
###UPDATEDROPCOL(Interlace,###TBLPREFIX###tblthumbnails)###
/* query separator */
###UPDATEDROPCOL(Fitinside,###TBLPREFIX###tblthumbnails)###
/* query separator */
###UPDATEDROPCOL(Date,###TBLPREFIX###tblthumbnails)###

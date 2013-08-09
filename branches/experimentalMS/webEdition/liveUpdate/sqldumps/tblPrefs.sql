###UPDATEONLY###DROP TABLE IF EXISTS ###TBLPREFIX###tblPrefs_old;
/* query separator */
CREATE TABLE tblPrefs (
  userID bigint  NOT NULL default '0',
  keyy varchar(100) NOT NULL default '',
	value text NOT NULL,
  PRIMARY KEY (userID,keyy),	
) 


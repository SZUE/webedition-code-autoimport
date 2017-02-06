###UPDATEDROPCOL(Icon,###TBLPREFIX###tblNewsletter)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblNewsletter (
  ID mediumint unsigned NOT NULL auto_increment,
  ParentID mediumint unsigned NOT NULL default '0',
  IsFolder tinyint unsigned NOT NULL default '0',
  Path varchar(255) NOT NULL default '',
  `Text` varchar(255) NOT NULL default '',
  Subject varchar(255) NOT NULL default '',
  Sender varchar(255) NOT NULL default '',
  Reply varchar(255) NOT NULL default '',
  Test varchar(255) NOT NULL default '',
  Log text NOT NULL,
  Step mediumint unsigned NOT NULL default '0',
  Offset mediumint unsigned NOT NULL default '0',
  Charset ENUM('','UTF-8','ISO-8859-1','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-11','ISO-8859-12','ISO-8859-13','ISO-8859-14','ISO-8859-15','Windows-1251','Windows-1252') NOT NULL default '',
  isEmbedImages tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
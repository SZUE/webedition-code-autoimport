CREATE TABLE ###TBLPREFIX###tblUser (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  `Type` tinyint(1) unsigned NOT NULL default '0',
  `First` varchar(255) NOT NULL default '',
  `Second` varchar(255) NOT NULL default '',
  Address varchar(255) NOT NULL default '',
  HouseNo varchar(11) NOT NULL default '',
  City varchar(255) NOT NULL default '',
  PLZ varchar(32) NOT NULL default '',
  State varchar(255) NOT NULL default '',
  Country varchar(255) NOT NULL default '',
  Tel_preselection varchar(11) NOT NULL default '',
  Telephone varchar(32) NOT NULL default '',
  Fax_preselection varchar(11) NOT NULL default '',
  Fax varchar(32) NOT NULL default '',
  Handy varchar(32) NOT NULL default '',
  Email varchar(255) NOT NULL default '',
  Description TEXT NOT NULL,
  username varchar(255) NOT NULL default '',
  passwd varchar(255) NOT NULL default '',
  Permissions text NOT NULL,
  ParentPerms tinyint(1) unsigned NOT NULL default '0',
  Alias int(11) unsigned NOT NULL default '0',
  CreatorID int(11) unsigned NOT NULL default '0',
  CreateDate int(10) unsigned NOT NULL default '0',
  ModifierID int(11) unsigned NOT NULL default '0',
  ModifyDate int(10) unsigned NOT NULL default '0',
  Ping int(11) unsigned NOT NULL default '0',
  Portal varchar(255) NOT NULL default '',
  workSpace TEXT NOT NULL default '',
  workSpaceDef TEXT NOT NULL default '',
  workSpaceTmp TEXT NOT NULL default '',
  workSpaceNav TEXT NOT NULL default '',
  workSpaceObj TEXT NOT NULL default '',
  workSpaceNwl TEXT NOT NULL default '',
  workSpaceCust TEXT NOT NULL default '',
  ParentWs tinyint(1) unsigned NOT NULL default '0',
  ParentWst tinyint(1) unsigned NOT NULL default '0',
  ParentWsn tinyint(1) unsigned NOT NULL default '0',
  ParentWso tinyint(1) unsigned NOT NULL default '0',
  ParentWsnl tinyint(1) unsigned NOT NULL default '0',
  ParentWsCust tinyint(1) unsigned NOT NULL default '0',
  Salutation varchar(32) NOT NULL default '',
  LoginDenied tinyint(1) unsigned NOT NULL default '0',
  UseSalt tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY Ping (Ping),
  KEY Alias (Alias),
  UNIQUE username (username)
) ENGINE=MyISAM;
/* query separator */
###INSTALLONLY###INSERT INTO ###TBLPREFIX###tblUser VALUES (1,0,'admin','/admin','user.gif',0,0,'webEdition','','','','',0,'','','','','','','','','','admin','c0e024d9200b5705bc4804722636378a','a:55:{s:13:\"ADMINISTRATOR\";s:1:\"1\";s:18:\"NEW_WEBEDITIONSITE\";s:1:\"1\";s:10:\"NEW_GRAFIK\";s:1:\"1\";s:8:\"NEW_HTML\";s:1:\"1\";s:9:\"NEW_FLASH\";s:1:\"1\";s:6:\"NEW_JS\";s:1:\"1\";s:7:\"NEW_CSS\";s:1:\"1\";s:12:\"NEW_SONSTIGE\";s:1:\"1\";s:12:\"NEW_TEMPLATE\";s:1:\"1\";s:14:\"NEW_DOC_FOLDER\";s:1:\"1\";s:22:\"CHANGE_DOC_FOLDER_PATH\";s:1:\"0\";s:15:\"NEW_TEMP_FOLDER\";s:1:\"1\";s:17:\"CAN_SEE_DOCUMENTS\";s:1:\"1\";s:17:\"CAN_SEE_TEMPLATES\";s:1:\"1\";s:22:\"SAVE_DOCUMENT_TEMPLATE\";s:1:\"1\";s:17:\"DELETE_DOC_FOLDER\";s:1:\"1\";s:18:\"DELETE_TEMP_FOLDER\";s:1:\"1\";s:15:\"DELETE_DOCUMENT\";s:1:\"1\";s:15:\"DELETE_TEMPLATE\";s:1:\"1\";s:13:\"BROWSE_SERVER\";s:1:\"1\";s:12:\"EDIT_DOCTYPE\";s:1:\"1\";s:14:\"EDIT_KATEGORIE\";s:1:\"1\";s:7:\"REBUILD\";s:1:\"1\";s:6:\"EXPORT\";s:1:\"1\";s:6:\"IMPORT\";s:1:\"1\";s:9:\"NEW_GROUP\";s:1:\"1\";s:8:\"NEW_USER\";s:1:\"1\";s:10:\"SAVE_GROUP\";s:1:\"1\";s:9:\"SAVE_USER\";s:1:\"1\";s:12:\"DELETE_GROUP\";s:1:\"1\";s:11:\"DELETE_USER\";s:1:\"1\";s:7:\"PUBLISH\";s:1:\"1\";s:21:\"EDIT_SETTINGS_DEF_EXT\";s:1:\"1\";s:13:\"EDIT_SETTINGS\";s:1:\"1\";s:11:\"EDIT_PASSWD\";s:1:\"1\";s:12:\"NEW_CUSTOMER\";s:1:\"0\";s:15:\"DELETE_CUSTOMER\";s:1:\"0\";s:13:\"EDIT_CUSTOMER\";s:1:\"0\";s:19:\"SHOW_CUSTOMER_ADMIN\";s:1:\"0\";s:16:\"NEW_SHOP_ARTICLE\";s:1:\"0\";s:19:\"DELETE_SHOP_ARTICLE\";s:1:\"0\";s:15:\"EDIT_SHOP_ORDER\";s:1:\"0\";s:17:\"DELETE_SHOP_ORDER\";s:1:\"0\";s:15:\"EDIT_SHOP_PREFS\";s:1:\"0\";s:19:\"CAN_SEE_OBJECTFILES\";s:1:\"1\";s:14:\"NEW_OBJECTFILE\";s:1:\"1\";s:21:\"NEW_OBJECTFILE_FOLDER\";s:1:\"1\";s:17:\"DELETE_OBJECTFILE\";s:1:\"1\";s:15:\"CAN_SEE_OBJECTS\";s:1:\"0\";s:10:\"NEW_OBJECT\";s:1:\"0\";s:13:\"DELETE_OBJECT\";s:1:\"0\";s:12:\"NEW_WORKFLOW\";s:1:\"0\";s:15:\"DELETE_WORKFLOW\";s:1:\"0\";s:13:\"EDIT_WORKFLOW\";s:1:\"0\";s:9:\"EMPTY_LOG\";s:1:\"0\";}',0,0,0,0,0,0,1146233940,'','','','','','','',0,0,0,0,0,'',0,1);

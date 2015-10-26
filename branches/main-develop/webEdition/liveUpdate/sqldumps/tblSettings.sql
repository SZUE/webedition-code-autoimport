/*FIXME: mv banner, shop, newsletter to this table*/

CREATE TABLE ###TBLPREFIX###tblSettings (
	tool ENUM('banner','glossary','newsletter','shop','webadmin') NOT NULL,
  pref_name VARCHAR(255) NOT NULL,
  pref_value text NOT NULL,
  PRIMARY KEY  (tool,pref_name)
) ENGINE=MyISAM;

/* query separator */
###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblSettings WHERE tool='shop' AND pref_name IN ('edit_shop_properties','shop_pref');
/* query separator */
###ONTAB(###TBLPREFIX###tblWebAdmin)INSERT INTO ###TBLPREFIX###tblSettings (SELECT "webadmin",Name,Value FROM ###TBLPREFIX###tblWebAdmin);###
/* query separator */
###ONTAB(###TBLPREFIX###tblWebAdmin)DROP TABLE IF EXISTS ###TBLPREFIX###tblWebAdmin;###
/* query separator */
###ONTAB(###TBLPREFIX###tblbannerprefs)INSERT INTO ###TBLPREFIX###tblSettings (SELECT "banner",pref_name,pref_value FROM ###TBLPREFIX###tblbannerprefs);###
/* query separator */
###ONTAB(###TBLPREFIX###tblbannerprefs)DROP TABLE IF EXISTS ###TBLPREFIX###tblbannerprefs;###
/* query separator */
###ONTAB(###TBLPREFIX###tblNewsletterPrefs)INSERT INTO ###TBLPREFIX###tblSettings (SELECT "newsletter",pref_name,pref_value FROM ###TBLPREFIX###tblNewsletterPrefs);###
/* query separator */
###ONTAB(###TBLPREFIX###tblNewsletterPrefs)DROP TABLE IF EXISTS ###TBLPREFIX###tblNewsletterPrefs;###


/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='edit_shop_properties',pref_value='a:2:{s:14:"customerFields";a:0:{}s:19:"orderCustomerFields";a:0:{}}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='shop_pref',pref_value='€|19|german';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='glossary',pref_name='weGlossaryAutomaticReplacement',pref_value='1';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET Name='FieldAdds',Value='a:13:{s:8:"Username";a:1:{s:4:"type";s:5:"input";}s:8:"Password";a:1:{s:4:"type";s:5:"input";}s:8:"Forename";a:1:{s:4:"type";s:5:"input";}s:7:"Surname";a:1:{s:4:"type";s:5:"input";}s:11:"LoginDenied";a:1:{s:4:"type";s:5:"input";}s:11:"MemberSince";a:1:{s:4:"type";s:5:"input";}s:9:"LastLogin";a:1:{s:4:"type";s:5:"input";}s:10:"LastAccess";a:1:{s:4:"type";s:5:"input";}s:15:"AutoLoginDenied";a:1:{s:4:"type";s:5:"input";}s:9:"AutoLogin";a:1:{s:4:"type";s:5:"input";}s:13:"Anrede_Anrede";a:2:{s:7:"default";s:10:",Herr,Frau";s:4:"type";s:6:"select";}s:13:"Newsletter_Ok";a:2:{s:7:"default";s:3:",ja";s:4:"type";s:6:"select";}s:25:"Newsletter_HTMLNewsletter";a:2:{s:7:"default";s:3:",ja";s:4:"type";s:6:"select";}}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET Name='Prefs',Value='a:4:{s:10:"start_year";s:4:"1900";s:17:"default_sort_view";s:20:"--Keine Sortierung--";s:15:"treetext_format";s:30:"#Username (#Forename #Surname)";s:13:"default_order";s:0:"";}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET Name='SortView',Value='';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET Name='default_saveRegisteredUser_register',Value='false';

/*FIXME: mv banner, shop, newsletter to this table*/

CREATE TABLE ###TBLPREFIX###tblSettings (
	tool ENUM('banner','glossary','newsletter','shop','webadmin') NOT NULL,
  pref_name VARCHAR(255) NOT NULL,
  pref_value text NOT NULL,
  PRIMARY KEY  (tool,pref_name)
) ENGINE=MyISAM;

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
###ONTAB(###TBLPREFIX###tblAnzeigePrefs)INSERT INTO ###TBLPREFIX###tblSettings (SELECT "shop",strDateiname,strFelder FROM ###TBLPREFIX###tblAnzeigePrefs);###
/* query separator */
###ONTAB(###TBLPREFIX###tblAnzeigePrefs)DROP TABLE IF EXISTS ###TBLPREFIX###tblAnzeigePrefs;###

/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='edit_shop_properties',pref_value='{"customerFields":[],"orderCustomerFields":[]}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='shop',pref_name='shop_pref',pref_value='EUR|19|german';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool='glossary',pref_name='weGlossaryAutomaticReplacement',pref_value='1';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool="webadmin",pref_name='FieldAdds',pref_value='{"Username":{"type":"input"},"Password":{"type":"input"},"Forename":{"type":"input"},"Surname":{"type":"input"},"LoginDenied":{"type":"input"},"MemberSince":{"type":"input"},"LastLogin":{"type":"input"},"LastAccess":{"type":"input"},"AutoLoginDenied":{"type":"input"},"AutoLogin":{"type":"input"},"Anrede_Anrede":{"default":",Herr,Frau","type":"select"},"Newsletter_Ok":{"default":",ja","type":"select"},"Newsletter_HTMLNewsletter":{"default":",ja","type":"select"}}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool="webadmin",pref_name='Prefs',pref_value='{"start_year":1900,"default_sort_view":"--Keine Sortierung--","treetext_format":"#Username (#Forename #Surname)","default_order":""}';
/* query separator */
###INSTALLONLY###INSERT IGNORE INTO ###TBLPREFIX###tblSettings SET tool="webadmin",pref_name='SortView',pref_value='';

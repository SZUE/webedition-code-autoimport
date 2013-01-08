<?php
/** Generated language file of webEdition CMS */
$l_prefs=array(
	'1_day'=>'1 journée',
	'1_hour'=>'1 heure',
	'1_minute'=>'1 minute',
	'1_week'=>'1 semaine',
	'1_year'=>'1 an',
	'add_dictionary_question'=>'Voulez-vous charger le dictionnaire pour cette langue ?',
	'all'=>'Tous',
	'ask_at_start'=>'En démarrant demander,<br/>quel editeur doit<br/>être utilisé',
	'authpass'=>'Mot de passe',
	'authuser'=>'Nom d`utilisateur',
	'auth'=>'Authentification HTTP',
	'backup_auto'=>'Auto',
	'backup_fast'=>'Rapide',
	'backup_slow'=>'Lente',
	'backwardcompatibility'=>'Compatibilité avec les versions antérieures',
	'backwardcompatibility_tagloading'=>'Charger tous les ancien fonctions `we_tag`',
	
	'base'=>array(
		'css'=>'Domain for CSS files',
		'img'=>'Domain for images',
		'js'=>'Domain for JS files',
	),
	'blocked_until'=>'Bloqué jusqu`à',
	'blockFormmail'=>'Limiter les requettes formmail',
	'blockFor'=>'Bloquer pour',
	'cache_information'=>'Set the preset values of the fields "Caching Type" and "Cache lifetime in seconds" for new templates here.<br/><br/>Please note that these setting are only the presets of the fields.',
	
	'cache_lifetimes'=>array(
		'0'=>'désactivé',
		'1800'=>'30 minutes',
		'21600'=>'6 heures',
		'300'=>'5 minutes',
		'3600'=>'1 heure',
		'43200'=>'12 heures',
		'600'=>'10 minutes',
		'60'=>'1 minute',
		'86400'=>'1 jour',
	),
	'cache_lifetime'=>'Cache lifetime in seconds',
	'cache_navigation'=>'Configuration par defaut',
	'cache_navigation_information'=>'Enter the defaults for the &lt;we:navigation&gt; tag here. This value can be overwritten by the attribute "cachelifetime" of the &lt;we:navigation&gt; tag.',
	'cache_presettings'=>'Préconfiguration',
	'cache_type'=>'Type de cache',
	'cache_type_document'=>'Cache de document',
	'cache_type_full'=>'Tout en cache',
	'cache_type_none'=>'Cache désactivé',
	'cache_type_wetag'=>'we:Tag cache',
	'cannot_delete_default_language'=>'La langue par défaut ne peut pas être supprimée.',
	'change_only_in_ie'=>'Comme le PlugIn Editor fonctionne seulement sous Windows dans le Internet Explorer, Mozilla, Firebird et Firefox ces préférences ne sont pas modifiables.',
	'choose_backendcharset'=>'Charset du backend',
	'choose_language'=>'Langue du backend',
	'clear_block_entry_question'=>'Voulez-vous vraiment débloquer le IP %s ?',
	'clear_log_question'=>'Voulez-vous vraiment vider l`historique ?',
	'cockpit_amount_columns'=>'Colonnes du cockpit',
	'confirm_install_plugin'=>'Le PlugIn ActiveX pour Mozilla , permet d`intégrer des Controles ActiveX dans le navigateur Mozilla. Le navigateur doit être redémarré après l`installation .\n\nConsidérez: ActiveX peut-être un risque pour la sécurité!\n\nContinuer avec l`installation?',
	'ContentType'=>'Type de contenu',
	'countries_country'=>'Pays',
	'countries_default'=>'Valeur par défaut',
	'countries_headline'=>'Sélection des pays',
	'countries_information'=>'Select the countries, which are available in the customer module, shop-module and so on.  The default value (Code `--`) - if filled - will be shown on top of the list, possible values are i.e. `please choose` or `--`.',
	'countries_noshow'=>'pas d`affichage',
	'countries_show'=>'afficher',
	'countries_top'=>'top list',
	'db_connect'=>'Type de connexion-<br/>de base de données',
	'db_set_charset'=>'Charset de la connection',
	'db_set_charset_information'=>'The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.',
	'db_set_charset_warning'=>'The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.',
	'debug_normal'=>'Dans mode normal',
	'debug_restart'=>'Les changement demandent un nouveau démarrage',
	'debug_seem'=>'Dans le SeeMode',
	'default_cache_lifetime'=>'Durée de mise en cache par défaut',
	'default_charset'=>'Frontend charset par défaut',
	'default_php_setting'=>'Préférences standard pour l`attribut-<br/><em>php</em> dans les we:tags',
	'deleteEntriesOlder'=>'Supprimer les entrées antérieures à',
	'delete_cache_add'=>'Créer une nouvelle entrée',
	'delete_cache_after'=>'Vider le cache de la navigation',
	'delete_cache_delete'=>'après avoir supprimé une entrée',
	'delete_cache_edit'=>'après avoir modifié une entrée',
	'dimension'=>'Taille de la fenêtre',
	'disable_template_code_check'=>'Désactiver la verification du<br/>code (php) invalide',
	'disable_template_tag_check'=>'Deactivate check for missing,<br/>closing we:tags',
	'dynamic'=>'Sites dynamiques',
	'editor_comment_font_color'=>'Commentaires',
	'editor_completion'=>'Complétion de code',
	'editor_docuclick'=>'Integration de la documentation',
	'editor_enable'=>'Activer',
	'editor_fontname'=>'Type de Police',
	'editor_fontsize'=>'Taille',
	'editor_font'=>'Police dans l`editeur',
	'editor_font_colors'=>'Définir les couleurs des polices',
	'editor_highlight_colors'=>'Highlighting colors',
	'editor_html_attribute_font_color'=>'Attributs HTML',
	'editor_html_tag_font_color'=>'Tags HTML',
	'editor_information'=>'Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br/><br/>These settings are used for the text editor of the abovementioned file types.',
	'editor_javascript2'=>'CodeMirror2',
	'editor_javascript'=>'Editeur Javascript (beta)',
	'editor_javascript_information'=>'The JavaScript editor is still in beta stadium. Depending on which of the following options you`ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.',
	'editor_java'=>'Editeur Java',
	'editor_linenumbers'=>'numéros des lignes',
	'editor_mode'=>'Éditeur',
	'editor_normal_font_color'=>'Par defaut',
	'editor_pi_tag_font_color'=>'Code PHP',
	'editor_plaintext'=>'Textarea sans formattage',
	'editor_plugin'=>'PlugIn-Editeur',
	'editor_tooltips'=>'Tooltips sur we:tags',
	'editor_we_attribute_font_color'=>'attributs de webEdition',
	'editor_we_tag_font_color'=>'tags de webEdition',
	'email'=>'Email',
	'error_deprecated'=>'notices obsolètes',
	'error_displaying'=>'Affichage d`erreur',
	'error_display'=>'Afficher les erreurs',
	'error_errors'=>'Erreurs',
	'error_log'=>'Protocoler les erreurs',
	'error_mail'=>'Envoyer les erreurs par e-mail',
	'error_mail_address'=>'Adresse',
	'error_mail_not_saved'=>'Les erreurs ne vont pas être envoyé à l`adresse insérere parce que l`adresse est défectueux!\n\nLes autres préférences ont été enregistrées avec succès.',
	'error_notices'=>'Renseignements',
	'error_notices_warning'=>'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',
	'error_no_object_found'=>'Page d`erreur pour objets non-existant',
	'error_types'=>'Erreurs à traiter',
	'error_use_handler'=>'Activer le traitement des<br/>erreurs de webEdition',
	'error_warnings'=>'Avertissements',
	'ever'=>'toujours',
	'extensions_information'=>'Mise des extensions de fichier par défaut pour les pages statiques et dynamiques ici.',
	'force_glossary_action'=>'Forcer l`action',
	'force_glossary_check'=>'Forcer la vérification du glossaire',
	'forever'=>'Pour toujours',
	'formmailConfirm'=>'Fonction de confirmation formmail',
	'formmailSpan'=>'Dans un délai de',
	'formmailTrials'=>'Requêtes autorisés',
	'formmailViaWeDoc'=>'Appeler formmail par le document webEdition',
	'formmail_information'=>'Saisissez ici tous les adresses e-mail, aux quelles des formulaires avec la fonction-formmail  (&lt;we:form type="formmail" ..&gt;) sont être envoyés.<br/><br/>Si aucune adresse e-mail est saisie ici, il n`est pas possible d`envoyer des formulaires avec la fonction-Formmail!',
	'formmail_log'=>'Historique de formmail',
	'formmail_recipients'=>'Destinataire-Formmail',
	'general_directoryindex_hide'=>'Cache les noms de fichier de DirectoryIndex dans affichage',
	'general_directoryindex_hide_description'=>'For the tags <we:a>, <we:href>, <we:link>, <we:linklist>, <we:listview>, <we:url> you can use the attribute `hidedirindex`.',
	'general_objectseourls'=>'Créer SEO-URLs pour objets',
	'general_objectseourls_description'=>'For the tags <we:link>, <we:linklist>, <we:listview>, <we:object> you can use the attribute `objectseourls`.',
	'general_seoinside'=>'Usage dans webEdition',
	'general_seoinside_description'=>'If DirectoryIndex- file names and object SEO urls are used within webEdition, webEdition can not identify internal links and clicks on these links do not open the editor. With the following options, you can decide if they are are used in editmode and in the preview.',
	'glossary_publishing'=>'Verification avant publication',
	'height'=>'Hauteur',
	'hidenameattribinweform_default'=>'Pas d`édition de name=xyz dans we:form (XHTML strict)',
	'hidenameattribinweimg_default'=>'Pas d`édition de name=xyz dans we:img (HTML 5)',
	'hide_expert'=>'Cacher les préférences d`expert',
	'hide_predefined'=>'Cacher les tailles préréglées',
	'hooks'=>'Hooks',
	'hooks_information'=>'The use of hooks allows for the execution of arbitrary any PHP code during storing, publishing, unpublishing and deleting of any content type in webEdition.<br/>
	Further information can be found in the online documentation.<br/><br/>Allow execution of hooks?',
	'html'=>'Site-HTML',
	'html_extensions'=>'Extension-HTML',
	'inlineedit_default'=>'Préférences standard pour<br/>l`attribut-<em>inlineedit</em> dans la <br/>&lt;we:textarea&gt;',
	'install_editor_plugin'=>'Pour que vous puissiez utilisé le PlugIn dans votre navigateur, vous deviez l`installer d`abord.',
	'install_editor_plugin_text'=>'Le Plugin-Editeur de webEdition est installé...',
	'install_plugin'=>'Pour que vous puissiez utiliser le Plugin-Editeur avec votre Navigateur, il est nécéssaire d`installer le PlugIn ActiveX pour Mozilla.',
	'ip_address'=>'adresse IP',
	'juplod_not_installed'=>'JUpload n`est pas installé !',
	'langlink_abandoned_options'=>'<b>Notice:</b><br>From version 6.27 onwards the following two options are set "true", and can not be changed anymore. Thus setting of language links will allways be done recursively.',
	'langlink_headline'=>'Support for setting links between different languages',
	'langlink_information'=>'With this option, you can set the links to corresponding language versions of documents/objects in the backend and open/create etc. these documents/oobjects.<br/>For the frontend you can display these links in a listview type=languagelink.<br/><br/>For folders, you can define a <b>document</b> in each language, which is used if for a document within the folder no corresponding document in the other language is set.',
	'langlink_support'=>'activé',
	'langlink_support_backlinks'=>'Créer automatiquement des backlinks',
	'langlink_support_backlinks_information'=>'Back links can be generated automatically for documents/objects (not folders). The other document should not be open in an editor tab!',
	'langlink_support_recursive'=>'Generate language links recursive',
	'langlink_support_recursive_information'=>'Setting of langauge links can be done recursively for documents/objects (but not folders). This sets all possible links and tries to establish the language-circle as fast as possible. The other documents should not be open in an editor tab!',
	'language_already_exists'=>'Cette langue existe déjà.',
	'language_country_missing'=>'Choisissez un pays',
	'language_notice'=>'Le changement de langue prendra effet après le redémarrage de webEdition.',
	'locale_add'=>'Rajouter une langue',
	'locale_countries'=>'Pays',
	'locale_information'=>'Add all languages for which you would provide a web page.<br/><br/>This preference will be used for the glossary check and the spellchecking.',
	'locale_languages'=>'Langue',
	'logFormmailRequests'=>'Enregistre les requètes formmail',
	
	'login'=>array(
		'deactivateWEstatus'=>'hide the webEdition version status',
		'login'=>'LogIn',
		'windowtypeboth'=>'both, as POPUP and in the same window',
		'windowtypepopup'=>'only as POPUP',
		'windowtypesame'=>'only in the same window',
		'windowtypes'=>'Allow to start webEdition',
	),
	'log_is_empty'=>'L`historique est vide !',
	'mailer_information'=>'Adjust whether webEditionin should dispatch emails via the integrated PHP function or a seperate SMTP server should be used.<br/><br/>When using a SMTP mail server, the risk that messages are classified by the receiver as a "Spam" is lowered.',
	'mailer_php'=>'Utiliser fonction php mail ()',
	'mailer_smtp'=>'Utiliser serveur SMTP',
	'mailer_type'=>'type du mailer',
	'maximize'=>'Maximaliser',
	
	'message_reporting'=>array(
		'headline'=>'Notifications',
		'information'=>'You can decide on the respective check boxes whether you like to receive a notice for webEdition operations as for example saving, publishing or deleting.',
		'show_errors'=>'Afficher les erreurs',
		'show_notices'=>'Afficher les notices',
		'show_warnings'=>'Afficher les avertissements',
	),
	'module_activation'=>array(
		'headline'=>'Activation du module',
		'information'=>'Here you can activate or deactivate your modules if you do not need them.<br/>Deactivated modules improve the overall performance of webEdition. <br/>For some modules, you have to restart webEdition to activate.<br/>The Shop module requires the Customer module, the Workflow module requires the ToDo-Messaging module.',
	),
	'module_object'=>'Module de base de données-/ Objects',
	'more_days'=>'%s jours',
	'more_hours'=>'%s heures',
	'more_minutes'=>'%s minutes',
	'more_weeks'=>'%s semaines',
	'more_years'=>'%s années',
	'must_register'=>'Doit être enregistré',
	'navigation'=>'Navigation',
	'navigation_directoryindex_description'=>'After a change, a rebuild is required (i.e. navigation cache, objects ...)',
	'navigation_directoryindex_hide'=>'de la navigation',
	'navigation_directoryindex_names'=>'DirectoryIndex file names (comma separated, incl. file extensions, i.e. `index.php,index.html`',
	'navigation_entries_from_document'=>'Create new navigation entries from the document as',
	'navigation_entries_from_document_folder'=>'Repertoire',
	'navigation_entries_from_document_item'=>'Entrée',
	'navigation_objectseourls'=>'dans la navigation',
	'navigation_rules_continue'=>'Continue to evaluate navigation rules after a first match',
	'never'=>'jamais',
	'no'=>'non',
	'objectlinks_directoryindex_hide'=>'des liens vers objets',
	'off'=>'off',
	'on'=>'on',
	'pagelogger_dir'=>'Répertoire de pageLogger',
	'performance'=>'Here you can set an appropriate performance level. The performance level should be adequate to the server system. If the system has limited resources (memory, timeout etc.) choose a slow level, otherwise choose a fast level.',
	'phpLocalScope'=>'Tag-Parser: <br/>assume PHP local scope==global scope',
	'phpLocalScope_information'=>'`If you just use we:tags in your templates, please select the option "no" (this is the standard value).<br/>
	If you use your own PHP code in templates, make sure, that all PHP variables which are used as attributs to we:tags, are saved in the $GLOBALS array.<br/>
	<br/>To ensure backwards compatibility with old PHP code, which store PHP variables in the local scope (i.e. $X=1;), you can select the option "yes". Be aware, that in this case, you might encounter problems while sending e-mails, i.e. in the Newsletter- and Shop-Module as welöl as with the we:sendMail-tag.<br/>
	In order to switch to the standard "no", please replace in your templates the php code in the following manner: replace $x=1; to $GLOBALS["x"]=1;.<br/><br/>We strongly recommend to switch to the standard setting "no".',
	'predefined'=>'Tailles préréglées',
	'preload'=>'Chargement des préférences en cours, un moment s`il vous plaît ...',
	'preload_wait'=>'Chargement des préférences',
	'proxyaddr'=>'Adresse',
	'proxypass'=>'Mot de passe',
	'proxyport'=>'Port',
	'proxyuser'=>'Nom d`utilisateur',
	'proxy_information'=>'Specify your Proxy settings for your server here, if your server uses a proxy for the connection with the Internet.',
	'question_change_to_seem_start'=>'Voulez-vous changer au document choisi?',
	'removefirstparagraph_default'=>'Default value for the<br/><em>removefirstparagraph</em> attribute in<br/>&lt;we:textarea&gt;',
	'safari_wysiwyg'=>'Emploi l´editeur WYSIWYG (vérsion beta)',
	'saved'=>'Les préférences ont été enregistré avec succès.',
	'saved_successfully'=>'Préférences enregistrés',
	'save'=>'Enregistrement des préférences en cours, un moment s`il vous plaît ...',
	'save_wait'=>'Enregistrement des préférence',
	'seem'=>'seeMode',
	'seem_deactivate'=>'désactiver  le seeMode',
	'seem_startdocument'=>'Page d`accueil du seeMode',
	'seem_start_type_cockpit'=>'Cockpit',
	'seem_start_type_document'=>'Document',
	'seem_start_type_object'=>'Objet',
	'seem_start_type_weapp'=>'WE-App',
	'seoinside_hideineditmode'=>'Cacher dans le editmode',
	'seoinside_hideinwebedition'=>'Cacher dans l`aperçu',
	'showinputs_default'=>'Préférences standard pour l`attribut<br/><em>showinputs</em> dans <br/>&lt;we:img&gt;',
	'show_debug_frame'=>'afficher le Debug-Frame',
	'show_expert'=>'Afficher les préférences d`expert',
	'show_predefined'=>'Afficher les tailles préréglées',
	'sidebar'=>'Sidebar',
	'sidebar_deactivate'=>'désactiver',
	'sidebar_document'=>'Document',
	'sidebar_show_on_startup'=>'afficher au demarrage',
	'sidebar_width'=>'largeur en pixel',
	'smtp_auth'=>'authentification',
	'smtp_encryption'=>'transfert sécurisé',
	'smtp_encryption_none'=>'non',
	'smtp_encryption_ssl'=>'SSL',
	'smtp_encryption_tls'=>'TLS',
	'smtp_halo'=>'SMTP-Halo',
	'smtp_password'=>'Mot de passe',
	'smtp_port'=>'Port SMTP',
	'smtp_server'=>'Serveur SMTP',
	'smtp_timeout'=>'timeout SMTP',
	'smtp_username'=>'Nom d`utilisateur',
	'specify'=>'Spécifier',
	'start_automatic'=>'Démarrer automatiquement',
	'static'=>'Sites statiques',
	'suppress404code'=>'empêcher 404 not found',
	
	'tab'=>array(
		'advanced'=>'Avancé',
		'backup'=>'Sauvegarde',
		'cache'=>'Cache',
		'cockpit'=>'Cockpit',
		'countries'=>'Pays',
		'defaultAttribs'=>'we:tag defaults',
		'editor'=>'Editeur',
		'email'=>'Email',
		'error_handling'=>'Traitement des Erreurs',
		'extensions'=>'Extensions de fichier',
		'language'=>'Langues',
		'message_reporting'=>'Notifications',
		'modules'=>'Modules',
		'proxy'=>'Server-Proxy',
		'recipients'=>'Formmail',
		'seolinks'=>'Liens SEO',
		'system'=>'Système',
		'ui'=>'Surface',
		'validation'=>'Validation',
		'versions'=>'Versioning',
	),
	'tab_glossary'=>'Glossaire',
	'taglinks_directoryindex_hide'=>'Configuration par defaut pour tags',
	'taglinks_objectseourls'=>'Configuration par defaut pour tags',
	'templates'=>'Templates',
	'thumbnail_dir'=>'Repertoire pour miniatures',
	'tree_count'=>'Nombre des objects à afficher',
	'tree_count_description'=>'Cette valeure définit le nombre maximal des entrées affichées dans la navigation gauche.',
	'tree_title'=>'Menu d`abre',
	'unblock'=>'débloquer',
	'urlencode_objectseourls'=>'URLencode les URLs SEO',
	'useauth'=>'Le serveur utilise <br/>l`authentification HTTP dans <br/>le répertoire webEdition',
	'useproxy'=>'Utiliser un Server-Proxy pour<br/>la mise à jour en direct',
	'use_it'=>'Utiliser',
	'use_jeditor'=>'Utiliser',
	'use_jupload'=>'Utiliser java upload',
	'versioning'=>'Versioning',
	'versioning_activate_text'=>'Activate versioning for some or all content types.',
	'versioning_anzahl'=>'Nombre',
	'versioning_anzahl_text'=>'Number of versions which will be created for each document or object.',
	'versioning_create'=>'Créer version',
	'versioning_create_text'=>'Determine which actions provoke new versions. Either if you publish or if you save, unpublish, delete or import files, too.',
	'versioning_templates_text'=>'Define special values for the <b>versioning of templates</b>',
	'versioning_time'=>'Durée',
	'versioning_time_text'=>'If you specify a time period, only versions are saved which are created in this time until today. Older versions will be deleted.',
	'versioning_wizard'=>'Open Versions-Wizard',
	'versioning_wizard_text'=>'Open the Version-Wizard to delete or reset versions.',
	'versions_create_always'=>'toujours',
	'versions_create_publishing'=>'que en cas de publication',
	'versions_create_tmpl_always'=>'toujours',
	'versions_create_tmpl_publishing'=>'que par un bouton specifique',
	'version_all'=>'tous',
	'we_doctype_workspace_behavior'=>'Comportement du choix du type-de-document',
	'we_doctype_workspace_behavior_0'=>'Standard',
	'we_doctype_workspace_behavior_1'=>'Inverse',
	'we_doctype_workspace_behavior_hint0'=>'Le répertoire standard d`un type-de-document doit être dans l`éspace de travail de l`utilisateur, pour que l`utilisateur puisse choisir le type-de-document.',
	'we_doctype_workspace_behavior_hint1'=>'L`éspace de travail doit être dans le répertoire standard de l`utilisateur, pour que l`utilisateur puisse  choisir le type-de-document.',
	'we_extensions'=>'Extension-webEdition',
	'we_max_upload_size'=>'Taille maximale de téléchargement<br/>dans les textes de notification',
	'we_max_upload_size_hint'=>'(en Mega Octet, 0=automatique)',
	'we_new_folder_mod'=>'Droits d`accès pour des<br/>nouveauxnew répertoires.',
	'we_new_folder_mod_hint'=>'(stander est 755)',
	
	'we_scheduler_trigger'=>array(
		'cron'=>'external cron-job',
		'description'=>'Choose when the scheduler should be triggered.<br/>The options before and after page delivery trigger only on dynamic pages<br/>Before page delivery can cause longer page loading.<br/>Cron-job should be used whereever possible, but requires to call <code>webEdition/triggerWEtasks.php</code>',
		'head'=>'Trigger of the scheduler',
		'postDoc'=>'after delivery of the page',
		'preDoc'=>'before delivery of the page',
	),
	'width'=>'Largeur',
	'wysiwyglinks_directoryindex_hide'=>'in links from the WYSIWYG editor',
	'wysiwyglinks_objectseourls'=>'in links from the WYSIWYG editor',
	'wysiwyg_type'=>'Préférences standard pour l`editeur de <em>textarea</em>',
	'xhtml_debug_explanation'=>'Le Débogage-XHTML vous aide à créer des site-web valide. Optionel chaque édition d`un we:Tag peut être vérifié sur sa validité 	et si besoin sur des attributs défectueux. Considérez s`il vous plaît que ce processus nécessite du temps et il considerable d`effectuer cette option seulement quand vous créez un nouveau site.',
	'xhtml_debug_headline'=>'Débogage-XHTML',
	'xhtml_debug_html'=>'Activer le Débogage-XHTML',
	'xhtml_default'=>'Préférences standard pour l`attribut <em>xml</em> dans les we:Tags',
	'xhtml_remove_wrong'=>'Enlever les attributs défectueux',
	'xhtml_show_wrong_error_log_html'=>'Dans le Error-Log (PHP)',
	'xhtml_show_wrong_headline'=>'Notification en cas d`attributs défectueux',
	'xhtml_show_wrong_html'=>'Activer',
	'xhtml_show_wrong_js_html'=>'Comme Message-JavaScript',
	'xhtml_show_wrong_text_html'=>'Comme texte',
	'yes'=>'oui',
);
<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: users.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_users = array(
		'user_same' => "Omistajaa ei voida poistaa!",
		'grant_owners_ok' => "Omistaja on muutettu!",
		'grant_owners_notok' => "Virhe muutettaessa omistajaa!",
		'grant_owners' => "Vaihda omistajaa",
		'grant_owners_expl' => "Vaihda kyseisen kansion kaikkien tiedostojen ja hakemistojen omistajaa.",
		'make_def_ws' => "Oletus",
		'user_saved_ok' => "Käyttäjä '%s' on tallennettu!",
		'group_saved_ok' => "Ryhmä '%s' X on tallennettu!",
		'alias_saved_ok' => "Alias '%s' on tallennettu!",
		'user_saved_nok' => "Käyttäjää '%s' ei voitu tallentaa!",
		'nothing_to_save' => "Ei tallennettavaa!",
		'username_exists' => "Käyttäjänimi '%s' on jo olemassa!",
		'username_empty' => "Käyttäjänimi on tyhjä!",
		'user_deleted' => "Käyttäjä '%s' on poistettu!",
		'nothing_to_delete' => "Ei poistettavaa!",
		'delete_last_user' => "Yrität poistaa viimeista järjestelmänvalvojan oikeuksilla olevaa käyttäjää. Poistaminen voi estää järjestelmän käytön! Täten poistaminen ei ole mahdollista.",
		'modify_last_admin' => "Käyttäjistä yhden on oltava järjestelmänvalvoja.\n Et voi muuttaa viimeisen järjestelmänvalvojan oikeuksia",
		'user_path_nok' => "Polku on virheellinen!",
		'user_data' => "Käyttäjätiedot",
		'first_name' => "Etunimi",
		'second_name' => "Sukunimi",
		'username' => "Käyttäjänimi",
		'password' => "Salasana",
		'workspace_specify' => "Määritä työtila",
		'permissions' => "Oikeudet",
		'user_permissions' => "Käyttäjän oikeudet",
		'admin_permissions' => "Järjestelmänvalvojan oikeudet",
		'password_alert' => "Salasanan on oltava vähintään 4 kirjaiminen.",
		'delete_alert_user' => "Kaikki käyttäjätiedot käyttäjältä '%s' poistetaan.\\n Oletko varma että haluat tehdä tämän?",
		'delete_alert_alias' => "Kaikki aliastiedot aliakselle '%s' poistetaan.\\n Oletko varma?",
		'delete_alert_group' => "Kaikki ryhmätiedot ryhmälle '%s' poistetaan. Oletko varma?",
		'created_by' => "Luonut:",
		'changed_by' => "Muutettu:",
		'no_perms' => "Sinulla ei ole oikeuksia tehdä tätä!",
		'publish_specify' => "Käyttäjä voi julkaista.",
		'work_permissions' => "Työoikeudet",
		'control_permissions' => "Hallintaoikeudet",
		'log_permissions' => "Kirjautumisoikeudet",
		'acces_temp_denied' => "Pääsy väliaikaisesti evätty!",
		'description' => "Kuvaus",
		'group_data' => "Ryhmän tiedot",
		'group_name' => "Ryhmän tiedot",
		'group_member' => "Ryhmän jäsenyys",
		'group' => "Ryhmä",
		'address' => "Osoite",
		'houseno' => "Talon/asunnon numero",
		'state' => "Lääni",
		'PLZ' => "Postinumero",
		'city' => "Kunta",
		'country' => "Maa",
		'tel_pre' => "Suuntanumero",
		'fax_pre' => "Suuntanumero faksille",
		'telephone' => "Puhelin",
		'fax' => "Faksi",
		'mobile' => "Matkapuhelin",
		'email' => "Sähköposti",
		'general_data' => "Yleistiedot",
		'workspace_documents' => "Työtilan dokumentit",
		'workspace_templates' => "Työtilan sivupohjat",
		'workspace_objects' => "Työtilan objektit",
		'save_changed_user' => "Käyttäjää on muokattu.\\nHaluatko tallentaa muutokset?",
		'not_able_to_save' => "Tietoja ei ole tallennettu niiden virheellisyyden takia!",
		'cannot_save_used' => "Tilaa ei voida muuttaa koska se on 'käsittelyssä'!",
		'geaendert_von' => "Muokannut",
		'geaendert_am' => "Muokattu",
		'angelegt_am' => "Perustettu",
		'angelegt_von' => "Perustaja",
		'status' => "Tila",
		'value' => " Arvo ",
		'gesperrt' => "rajattu",
		'freigegeben' => "avoin",
		'gelöscht' => "poistettu",
		'ohne' => "ilman",
		'user' => "Käyttäjä",
		'usertyp' => "Käyttäjätyyppi",
		'search' => "haku",
		'search_results' => "Hakutulos",
		'search_for' => "Hae",
		'inherit' => "Peri oikeudet käyttäjäryhmältä.",
		'inherit_ws' => "Peri dokumenttien työtilat käyttäjäryhmältä.",
		'inherit_wst' => "Peri sivupohjien työtilat käyttäjäryhmältä.",
		'inherit_wso' => "Peri objektien työtilat käyttäjäryhmältä",
		'organization' => "Organisaatio",
		'give_org_name' => "Organisaation nimi",
		'can_not_create_org' => "Organisaatiota ei saatu luotua",
		'org_name_empty' => "Organisaation nimi on tyhjä",
		'salutation' => "Tervehdys",
		'sucheleer' => "Etsintäsana on tyhjä!",
		'alias_data' => "Aliaksen tiedot",
		'rights_and_workspaces' => "Oikeudet ja<br>työtilat",
		'workspace_navigations' => "Työtilan navigaatio",
		'inherit_wsn' => "Peri navigaation työtilat käyttäjäryhmältä",
		'workspace_newsletter' => "Työtilan uutiskirjeet",
		'inherit_wsnl' => "Peri uutiskirjeiden työtilat käyttäjäryhmältä",
		'delete_user_same' => "Et voi poistaa omaa käyttäjätiliäsi.",
		'delete_group_user_same' => "Et voi poistaa omaa käyttäjäryhmääsi",
		'login_denied' => "Pääsy kielletty",
		'workspaceFieldError' => "VIRHE: Virheellinen työtilamerkintä!",
		'noGroupError' => "Virhe: Virheellinen merkintä ryhmän kentässä!",
		'CreatorID' => "Created by: ", // TRANSLATE
		'CreateDate' => "Created at: ", // TRANSLATE
		'ModifierID' => "Modified by: ", // TRANSLATE
		'ModifyDate' => "Modified at: ", // TRANSLATE
		'lastPing' => "Last Ping: ", // TRANSLATE
		'lostID' => "ID: ", // TRANSLATE
		'lostID2' => " (deleted)", // TRANSLATE
);

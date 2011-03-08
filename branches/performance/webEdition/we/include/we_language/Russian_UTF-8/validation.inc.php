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
$l_validation = array(
		'headline' => 'Онлайн проверка (диагностика) документа',
//  variables for checking html files.
		'description' => 'С целью проверки документа на доступность можно выбрать соответствующую службу в сети.',
		'available_services' => 'Имеющиеся в наличие службы',
		'category' => 'Категория',
		'service_name' => 'Название службы',
		'service' => 'Служба',
		'host' => 'Хост',
		'path' => 'Путь',
		'ctype' => 'Тип контента',
		'ctype' => 'Функция целевого сервера по определению типа представляемого файла (text/html oder text/css)',
		'fileEndings' => 'Расширения',
		'fileEndings' => 'Следует включить все возможные расширения файлов, предназначенных для проверки данной службой: (.html,.css).',
		'method' => 'Метод',
		'checkvia' => 'Представить посредством',
		'checkvia_upload' => 'загрузки файла',
		'checkvia_url' => 'передачи URL',
		'varname' => 'имя переменной',
		'varname' => 'Следует вставить имя поля файла или url',
		'additionalVars' => 'Дополнительные параметры',
		'additionalVars' => 'выборочно: var1=wert1&var2=wert2&...',
		'result' => 'результат',
		'active' => 'действительный',
		'active' => 'Здесь можно скрыть временную службу.',
		'no_services_available' => 'Для данного типа файла не существует соответствующей службы проверки.',
//  the different predefined services
		'adjust_service' => 'Установка службы проверки данных',
		'art_custom' => 'Услуги, настраиваемые пользователем ',
		'art_default' => 'Услуги по умолчпнию',
		'category_xhtml' => '(X)HTML', // TRANSLATE
		'category_links' => 'Ссылки',
		'category_css' => 'Cascading Style Sheets', // TRANSLATE
		'category_accessibility' => 'Доступность',
		'edit_service' => array(
				'new' => 'Новая служба',
				'saved_success' => 'Служба успешно сохранена.',
				'saved_failure' => 'Не удалось сохранить данную службу.',
				'delete_success' => 'Служба успешно удалена.',
				'delete_failure' => 'Не удалось удалить данную службу.',
				'servicename_already_exists' => 'A service with this name already exists.', // TRANSLATE
		),
//  services for html
		'service_xhtml_upload' => '(X)HTML проверка W3C посредством загрузки файла',
		'service_xhtml_url' => '(X)HTML проверка W3C посредством передачи url',
//  services for css
		'service_css_upload' => 'Проверка CSS посредством загрузки файла',
		'service_css_url' => 'Проверка CSS посредством передачи url',
		'connection_problems' => '<strong> Ошибка при попытке соединения с данной службой</strong><br /><br />Примите во внимание: опция "передача url" работает только в случае, если система webEdition доступна в сети интернет (то есть за пределами локальной сети). На системы, установленные локально (localhost), данная опция  не распространяется.<br /><br />Кроме того, в случае применения защитных мер (firewalls) и прокси-серверов для систем, доступных в сети интернет, также иногда возникают трудности.<br /><br />HTTP-ответ: %',
);
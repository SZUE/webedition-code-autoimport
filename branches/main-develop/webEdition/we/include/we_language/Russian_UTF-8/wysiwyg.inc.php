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
 * Language file: wysiwyg.inc.php
 * Provides language strings.
 * Language: English
 */
include_once(dirname(__FILE__) . "/wysiwyg_js.inc.php");

$l_wysiwyg = array_merge($l_wysiwyg, array(
		'window_title' => "Редактировать поле %s'",
		'format' => "Формат",
		'fontsize' => "Размер шрифта",
		'fontname' => "Название шрифта",
		'css_style' => "Стиль CSS",
		'normal' => "Обычный",
		'h1' => "1 Заголовок",
		'h2' => "2 Заголовок",
		'h3' => "3 Заголовок",
		'h4' => "4 Заголовок",
		'h5' => "5 Заголовок",
		'h6' => "6 Заголовок",
		'pre' => "Отформатированный",
		'address' => "Адрес",
		'spellcheck' => 'Spellchecking', // TRANSLATE

		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Вырезать",
		'copy' => "Копировать",
		'paste' => "Вставить",
		'insert_row' => "Вставить строку",
		'delete_rows' => "Удалить строки",
		'insert_colmn' => "Вставить столбец",
		'delete_colmns' => "Удалить столбцы",
		'insert_cell' => "Вставить ячейку",
		'delete_cells' => "Удалить ячейки",
		'merge_cells' => "Слить ячейки",
		'split_cell' => "Разделить ячейки",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Нижний индекс",
		'superscript' => "Верхний индекс",
		'justify_full' => "Центровка текста вширь",
		'strikethrought' => "Перечеркнутый",
		'removeformat' => "Удалить форматирование",
		'removetags' => "Remove tags, styles and comments", // TRANSLATE
		'editcell' => "Редактировать ячейку таблицы",
		'edittable' => "Редактировать таблицу",
		'insert_row2' => "Вставить строки",
		'delete_rows2' => "Удалить строки",
		'insert_colmn2' => "Вставить столбец",
		'delete_colmns2' => "Удалить столбцы",
		'insert_cell2' => "Вставить ячейку",
		'delete_cells2' => "Удалить ячейку",
		'merge_cells2' => "Слить ячейки",
		'split_cell2' => "Разделить ячейку",
		'insert_edit_table' => "Вставить/редактировать таблицу",
		'insert_edit_image' => "Вставить/редактировать графику",
		'edit_style_class' => "Редактировать класс (стиль)",
		'insert_br' => "Вставить разрыв строки (SHIFT + RETURN)",
		'insert_p' => "Вставить абзац",
		'edit_sourcecode' => "Редактировать код",
		'show_details' => "Показать детали",
		'rtf_import' => "Импортировать RTF",
		'unlink' => "Удалить гиперссылку",
		'hyperlink' => "Вставить/редактировать гиперссылку",
		'back_color' => "Цвет заднего плана",
		'fore_color' => "Цвет переднего плана",
		'outdent' => "Втяжка",
		'indent' => "Отступ",
		'unordered_list' => "Ненумерованный список",
		'ordered_list' => "Нумерованный список",
		'justify_right' => "Выровнять вправо",
		'justify_center' => "Центрировать",
		'justify_left' => "Выровнять влево",
		'underline' => "Подчеркнуть",
		'italic' => "Курсив",
		'bold' => "Жирный шрифт",
		'fullscreen' => "Открыть редактор в режиме крупного экрана",
		'edit_source' => "Редактировать код",
		'fullscreen_editor' => "Редактор крупного экрана",
		'table_props' => "Свойства таблицы",
		'insert_table' => "Вставить таблицу",
		'edit_stylesheet' => "Редактировать таблицу стилей",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL", // TRANSLATE
		'image_url' => "URL графики",
		'width' => "Ширина",
		'height' => "Высота",
		'hspace' => "Расстояние по горизонтали",
		'vspace' => "Расстояние по вертикали",
		'border' => "Границы",
		'altText' => "Альтернативный текст",
		'alignment' => "Центровка",
		'external_image' => "графика извне",
		'internal_image' => "графика внутри webEdition",
		'bgcolor' => "Цвет фона",
		'cellspacing' => "Расстояние от ячеек",
		'cellpadding' => "Внутреннее расстояние",
		'rows' => "Ряды",
		'cols' => "Столбцы",
		'edit_table' => "Редактировать таблицу",
		'colspan' => "Colspan", // TRANSLATE
		'halignment' => "Гориз.выравнивание", // has to be short !!
		'valignment' => "Верт.выравнивание", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Выбрать цвет",
		'parent_class' => "Исходная область",
		'region_class' => "Только выбор",
		'edit_classname' => "Редактировать имя класса таблицы стилей",
		'emaillink' => "E-Mail", // TRANSLATE
		'clean_word' => "Очистить код MS Word",
		'addcaption' => "Добавить надпись",
		'removecaption' => "Удалить надпись",
		'anchor' => "Якорь",
		'edit_hr' => "Горизонтальная линия",
		'color' => "Цвет",
		'noshade' => "Без затенения",
		'strikethrough' => "Перечеркнуть",
		'nothumb' => "Без иконок",
		'thumbnail' => "Иконки",
		'acronym' => "Акроним",
		'acronym_title' => "Редактировать акроним",
		'abbr' => "Abbreviation", // TRANSLATE
		'abbr_title' => "Edit Abbreviation", // TRANSLATE
		'title' => "Заголовок",
		'language' => "Язык",
		'language_title' => "Редактировать язык",
		'link_lang' => "Ссылка",
		'href_lang' => "Ссылка указывает на страницу",
		'paragraph' => "Знак абзаца",
		'summary' => "Аннотация",
		'isheader' => "Является заголовком",
		'keyboard' => "Клавиатура",
		'relation' => "Отношение",
		'fontsize' => "Font size", // TRANSLATE
				));
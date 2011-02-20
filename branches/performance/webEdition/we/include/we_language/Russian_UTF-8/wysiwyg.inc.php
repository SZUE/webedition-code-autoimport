<?php

/**
 * webEdition CMS
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
include_once(dirname(__FILE__)."/wysiwyg_js.inc.php");

$l_wysiwyg["window_title"] = "Редактировать поле %s'";

$l_wysiwyg["format"] = "Формат";
$l_wysiwyg["fontsize"] = "Размер шрифта";
$l_wysiwyg["fontname"] = "Название шрифта";
$l_wysiwyg["css_style"] = "Стиль CSS";

$l_wysiwyg["normal"] = "Обычный";
$l_wysiwyg["h1"] = "1 Заголовок";
$l_wysiwyg["h2"] = "2 Заголовок";
$l_wysiwyg["h3"] = "3 Заголовок";
$l_wysiwyg["h4"] = "4 Заголовок";
$l_wysiwyg["h5"] = "5 Заголовок";
$l_wysiwyg["h6"] = "6 Заголовок";
$l_wysiwyg["pre"] = "Отформатированный";
$l_wysiwyg["address"] = "Адрес";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking'; // TRANSLATE

/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Вырезать";
$l_wysiwyg["copy"] = "Копировать";
$l_wysiwyg["paste"] = "Вставить";
$l_wysiwyg["insert_row"] = "Вставить строку";
$l_wysiwyg["delete_rows"] = "Удалить строки";
$l_wysiwyg["insert_colmn"] = "Вставить столбец";
$l_wysiwyg["delete_colmns"] = "Удалить столбцы";
$l_wysiwyg["insert_cell"] = "Вставить ячейку";
$l_wysiwyg["delete_cells"] = "Удалить ячейки";
$l_wysiwyg["merge_cells"] = "Слить ячейки";
$l_wysiwyg["split_cell"] = "Разделить ячейки";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Нижний индекс";
$l_wysiwyg["superscript"] = "Верхний индекс";
$l_wysiwyg["justify_full"] = "Центровка текста вширь";
$l_wysiwyg["strikethrought"] = "Перечеркнутый";
$l_wysiwyg["removeformat"] = "Удалить форматирование";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; // TRANSLATE
$l_wysiwyg["editcell"] = "Редактировать ячейку таблицы";
$l_wysiwyg["edittable"] = "Редактировать таблицу";
$l_wysiwyg["insert_row2"] = "Вставить строки";
$l_wysiwyg["delete_rows2"] = "Удалить строки";
$l_wysiwyg["insert_colmn2"] = "Вставить столбец";
$l_wysiwyg["delete_colmns2"] = "Удалить столбцы";
$l_wysiwyg["insert_cell2"] = "Вставить ячейку";
$l_wysiwyg["delete_cells2"] = "Удалить ячейку";
$l_wysiwyg["merge_cells2"] = "Слить ячейки";
$l_wysiwyg["split_cell2"] = "Разделить ячейку";
$l_wysiwyg["insert_edit_table"] = "Вставить/редактировать таблицу";
$l_wysiwyg["insert_edit_image"] = "Вставить/редактировать графику";
$l_wysiwyg["edit_style_class"] = "Редактировать класс (стиль)";
$l_wysiwyg["insert_br"] = "Вставить разрыв строки (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Вставить абзац";
$l_wysiwyg["edit_sourcecode"] = "Редактировать код";
$l_wysiwyg["show_details"] = "Показать детали";
$l_wysiwyg["rtf_import"] = "Импортировать RTF";
$l_wysiwyg["unlink"] = "Удалить гиперссылку";
$l_wysiwyg["hyperlink"] = "Вставить/редактировать гиперссылку";
$l_wysiwyg["back_color"] = "Цвет заднего плана";
$l_wysiwyg["fore_color"] = "Цвет переднего плана";
$l_wysiwyg["outdent"] = "Втяжка";
$l_wysiwyg["indent"] = "Отступ";
$l_wysiwyg["unordered_list"] = "Ненумерованный список";
$l_wysiwyg["ordered_list"] = "Нумерованный список";
$l_wysiwyg["justify_right"] = "Выровнять вправо";
$l_wysiwyg["justify_center"] = "Центрировать";
$l_wysiwyg["justify_left"] = "Выровнять влево";
$l_wysiwyg["underline"] = "Подчеркнуть";
$l_wysiwyg["italic"] = "Курсив";
$l_wysiwyg["bold"] = "Жирный шрифт";
$l_wysiwyg["fullscreen"] = "Открыть редактор в режиме крупного экрана";
$l_wysiwyg["edit_source"] = "Редактировать код";
$l_wysiwyg["fullscreen_editor"] = "Редактор крупного экрана";
$l_wysiwyg["table_props"] = "Свойства таблицы";
$l_wysiwyg["insert_table"] = "Вставить таблицу";
$l_wysiwyg["edit_stylesheet"] = "Редактировать таблицу стилей";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL"; // TRANSLATE
$l_wysiwyg["image_url"] = "URL графики";
$l_wysiwyg["width"] = "Ширина";
$l_wysiwyg["height"] = "Высота";
$l_wysiwyg["hspace"] = "Расстояние по горизонтали";
$l_wysiwyg["vspace"] = "Расстояние по вертикали";
$l_wysiwyg["border"] = "Границы";
$l_wysiwyg["altText"] = "Альтернативный текст";
$l_wysiwyg["alignment"] = "Центровка";

$l_wysiwyg["external_image"] = "графика извне";
$l_wysiwyg["internal_image"] = "графика внутри webEdition";

$l_wysiwyg["bgcolor"] = "Цвет фона";
$l_wysiwyg["cellspacing"] = "Расстояние от ячеек";
$l_wysiwyg["cellpadding"] = "Внутреннее расстояние";
$l_wysiwyg["rows"] = "Ряды";
$l_wysiwyg["cols"] = "Столбцы";
$l_wysiwyg["edit_table"] = "Редактировать таблицу";
$l_wysiwyg["colspan"] = "Colspan"; // TRANSLATE
$l_wysiwyg["halignment"] = "Гориз.выравнивание"; // has to be short !!
$l_wysiwyg["valignment"] = "Верт.выравнивание";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Выбрать цвет";
$l_wysiwyg["parent_class"] = "Исходная область";
$l_wysiwyg["region_class"] = "Только выбор";
$l_wysiwyg["edit_classname"] = "Редактировать имя класса таблицы стилей";
$l_wysiwyg["emaillink"] = "E-Mail"; // TRANSLATE
$l_wysiwyg["clean_word"] = "Очистить код MS Word";
$l_wysiwyg["addcaption"] = "Добавить надпись";
$l_wysiwyg["removecaption"] = "Удалить надпись";
$l_wysiwyg["anchor"] = "Якорь";

$l_wysiwyg["edit_hr"] = "Горизонтальная линия";
$l_wysiwyg["color"] = "Цвет";
$l_wysiwyg["noshade"] = "Без затенения";
$l_wysiwyg["strikethrough"] = "Перечеркнуть";

$l_wysiwyg["nothumb"] = "Без иконок";
$l_wysiwyg["thumbnail"] = "Иконки";

$l_wysiwyg["acronym"] = "Акроним";
$l_wysiwyg["acronym_title"] = "Редактировать акроним";
$l_wysiwyg["abbr"] = "Abbreviation"; // TRANSLATE
$l_wysiwyg["abbr_title"] = "Edit Abbreviation"; // TRANSLATE
$l_wysiwyg["title"] = "Заголовок";
$l_wysiwyg["language"] = "Язык";
$l_wysiwyg["language_title"] = "Редактировать язык";
$l_wysiwyg["link_lang"] = "Ссылка";
$l_wysiwyg["href_lang"] = "Ссылка указывает на страницу";
$l_wysiwyg["paragraph"] = "Знак абзаца";

$l_wysiwyg["summary"] = "Аннотация";
$l_wysiwyg["isheader"] = "Является заголовком";

$l_wysiwyg["keyboard"] = "Клавиатура";

$l_wysiwyg["relation"] = "Отношение";

$l_wysiwyg["fontsize"] = "Font size"; // TRANSLATE

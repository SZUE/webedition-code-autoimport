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

$GLOBALS["l_wysiwyg"]["window_title"] = "Редактировать поле %s'";

$GLOBALS["l_wysiwyg"]["format"] = "Формат";
$GLOBALS["l_wysiwyg"]["fontsize"] = "Размер шрифта";
$GLOBALS["l_wysiwyg"]["fontname"] = "Название шрифта";
$GLOBALS["l_wysiwyg"]["css_style"] = "Стиль CSS";

$GLOBALS["l_wysiwyg"]["normal"] = "Обычный";
$GLOBALS["l_wysiwyg"]["h1"] = "1 Заголовок";
$GLOBALS["l_wysiwyg"]["h2"] = "2 Заголовок";
$GLOBALS["l_wysiwyg"]["h3"] = "3 Заголовок";
$GLOBALS["l_wysiwyg"]["h4"] = "4 Заголовок";
$GLOBALS["l_wysiwyg"]["h5"] = "5 Заголовок";
$GLOBALS["l_wysiwyg"]["h6"] = "6 Заголовок";
$GLOBALS["l_wysiwyg"]["pre"] = "Отформатированный";
$GLOBALS["l_wysiwyg"]["address"] = "Адрес";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking'; // TRANSLATE

/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$GLOBALS["l_wysiwyg"]["cut"] = "Вырезать";
$GLOBALS["l_wysiwyg"]["copy"] = "Копировать";
$GLOBALS["l_wysiwyg"]["paste"] = "Вставить";
$GLOBALS["l_wysiwyg"]["insert_row"] = "Вставить строку";
$GLOBALS["l_wysiwyg"]["delete_rows"] = "Удалить строки";
$GLOBALS["l_wysiwyg"]["insert_colmn"] = "Вставить столбец";
$GLOBALS["l_wysiwyg"]["delete_colmns"] = "Удалить столбцы";
$GLOBALS["l_wysiwyg"]["insert_cell"] = "Вставить ячейку";
$GLOBALS["l_wysiwyg"]["delete_cells"] = "Удалить ячейки";
$GLOBALS["l_wysiwyg"]["merge_cells"] = "Слить ячейки";
$GLOBALS["l_wysiwyg"]["split_cell"] = "Разделить ячейки";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$GLOBALS["l_wysiwyg"]["subscript"] = "Нижний индекс";
$GLOBALS["l_wysiwyg"]["superscript"] = "Верхний индекс";
$GLOBALS["l_wysiwyg"]["justify_full"] = "Центровка текста вширь";
$GLOBALS["l_wysiwyg"]["strikethrought"] = "Перечеркнутый";
$GLOBALS["l_wysiwyg"]["removeformat"] = "Удалить форматирование";
$GLOBALS["l_wysiwyg"]["removetags"] = "Remove tags, styles and comments"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["editcell"] = "Редактировать ячейку таблицы";
$GLOBALS["l_wysiwyg"]["edittable"] = "Редактировать таблицу";
$GLOBALS["l_wysiwyg"]["insert_row2"] = "Вставить строки";
$GLOBALS["l_wysiwyg"]["delete_rows2"] = "Удалить строки";
$GLOBALS["l_wysiwyg"]["insert_colmn2"] = "Вставить столбец";
$GLOBALS["l_wysiwyg"]["delete_colmns2"] = "Удалить столбцы";
$GLOBALS["l_wysiwyg"]["insert_cell2"] = "Вставить ячейку";
$GLOBALS["l_wysiwyg"]["delete_cells2"] = "Удалить ячейку";
$GLOBALS["l_wysiwyg"]["merge_cells2"] = "Слить ячейки";
$GLOBALS["l_wysiwyg"]["split_cell2"] = "Разделить ячейку";
$GLOBALS["l_wysiwyg"]["insert_edit_table"] = "Вставить/редактировать таблицу";
$GLOBALS["l_wysiwyg"]["insert_edit_image"] = "Вставить/редактировать графику";
$GLOBALS["l_wysiwyg"]["edit_style_class"] = "Редактировать класс (стиль)";
$GLOBALS["l_wysiwyg"]["insert_br"] = "Вставить разрыв строки (SHIFT + RETURN)";
$GLOBALS["l_wysiwyg"]["insert_p"] = "Вставить абзац";
$GLOBALS["l_wysiwyg"]["edit_sourcecode"] = "Редактировать код";
$GLOBALS["l_wysiwyg"]["show_details"] = "Показать детали";
$GLOBALS["l_wysiwyg"]["rtf_import"] = "Импортировать RTF";
$GLOBALS["l_wysiwyg"]["unlink"] = "Удалить гиперссылку";
$GLOBALS["l_wysiwyg"]["hyperlink"] = "Вставить/редактировать гиперссылку";
$GLOBALS["l_wysiwyg"]["back_color"] = "Цвет заднего плана";
$GLOBALS["l_wysiwyg"]["fore_color"] = "Цвет переднего плана";
$GLOBALS["l_wysiwyg"]["outdent"] = "Втяжка";
$GLOBALS["l_wysiwyg"]["indent"] = "Отступ";
$GLOBALS["l_wysiwyg"]["unordered_list"] = "Ненумерованный список";
$GLOBALS["l_wysiwyg"]["ordered_list"] = "Нумерованный список";
$GLOBALS["l_wysiwyg"]["justify_right"] = "Выровнять вправо";
$GLOBALS["l_wysiwyg"]["justify_center"] = "Центрировать";
$GLOBALS["l_wysiwyg"]["justify_left"] = "Выровнять влево";
$GLOBALS["l_wysiwyg"]["underline"] = "Подчеркнуть";
$GLOBALS["l_wysiwyg"]["italic"] = "Курсив";
$GLOBALS["l_wysiwyg"]["bold"] = "Жирный шрифт";
$GLOBALS["l_wysiwyg"]["fullscreen"] = "Открыть редактор в режиме крупного экрана";
$GLOBALS["l_wysiwyg"]["edit_source"] = "Редактировать код";
$GLOBALS["l_wysiwyg"]["fullscreen_editor"] = "Редактор крупного экрана";
$GLOBALS["l_wysiwyg"]["table_props"] = "Свойства таблицы";
$GLOBALS["l_wysiwyg"]["insert_table"] = "Вставить таблицу";
$GLOBALS["l_wysiwyg"]["edit_stylesheet"] = "Редактировать таблицу стилей";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$GLOBALS["l_wysiwyg"]["url"] = "URL"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["image_url"] = "URL графики";
$GLOBALS["l_wysiwyg"]["width"] = "Ширина";
$GLOBALS["l_wysiwyg"]["height"] = "Высота";
$GLOBALS["l_wysiwyg"]["hspace"] = "Расстояние по горизонтали";
$GLOBALS["l_wysiwyg"]["vspace"] = "Расстояние по вертикали";
$GLOBALS["l_wysiwyg"]["border"] = "Границы";
$GLOBALS["l_wysiwyg"]["altText"] = "Альтернативный текст";
$GLOBALS["l_wysiwyg"]["alignment"] = "Центровка";

$GLOBALS["l_wysiwyg"]["external_image"] = "графика извне";
$GLOBALS["l_wysiwyg"]["internal_image"] = "графика внутри webEdition";

$GLOBALS["l_wysiwyg"]["bgcolor"] = "Цвет фона";
$GLOBALS["l_wysiwyg"]["cellspacing"] = "Расстояние от ячеек";
$GLOBALS["l_wysiwyg"]["cellpadding"] = "Внутреннее расстояние";
$GLOBALS["l_wysiwyg"]["rows"] = "Ряды";
$GLOBALS["l_wysiwyg"]["cols"] = "Столбцы";
$GLOBALS["l_wysiwyg"]["edit_table"] = "Редактировать таблицу";
$GLOBALS["l_wysiwyg"]["colspan"] = "Colspan"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["halignment"] = "Гориз.выравнивание"; // has to be short !!
$GLOBALS["l_wysiwyg"]["valignment"] = "Верт.выравнивание";  // has to be short !!
$GLOBALS["l_wysiwyg"]["color"] = "Color";
$GLOBALS["l_wysiwyg"]["choosecolor"] = "Выбрать цвет";
$GLOBALS["l_wysiwyg"]["parent_class"] = "Исходная область";
$GLOBALS["l_wysiwyg"]["region_class"] = "Только выбор";
$GLOBALS["l_wysiwyg"]["edit_classname"] = "Редактировать имя класса таблицы стилей";
$GLOBALS["l_wysiwyg"]["emaillink"] = "E-Mail"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["clean_word"] = "Очистить код MS Word";
$GLOBALS["l_wysiwyg"]["addcaption"] = "Добавить надпись";
$GLOBALS["l_wysiwyg"]["removecaption"] = "Удалить надпись";
$GLOBALS["l_wysiwyg"]["anchor"] = "Якорь";

$GLOBALS["l_wysiwyg"]["edit_hr"] = "Горизонтальная линия";
$GLOBALS["l_wysiwyg"]["color"] = "Цвет";
$GLOBALS["l_wysiwyg"]["noshade"] = "Без затенения";
$GLOBALS["l_wysiwyg"]["strikethrough"] = "Перечеркнуть";

$GLOBALS["l_wysiwyg"]["nothumb"] = "Без иконок";
$GLOBALS["l_wysiwyg"]["thumbnail"] = "Иконки";

$GLOBALS["l_wysiwyg"]["acronym"] = "Акроним";
$GLOBALS["l_wysiwyg"]["acronym_title"] = "Редактировать акроним";
$GLOBALS["l_wysiwyg"]["abbr"] = "Abbreviation"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["abbr_title"] = "Edit Abbreviation"; // TRANSLATE
$GLOBALS["l_wysiwyg"]["title"] = "Заголовок";
$GLOBALS["l_wysiwyg"]["language"] = "Язык";
$GLOBALS["l_wysiwyg"]["language_title"] = "Редактировать язык";
$GLOBALS["l_wysiwyg"]["link_lang"] = "Ссылка";
$GLOBALS["l_wysiwyg"]["href_lang"] = "Ссылка указывает на страницу";
$GLOBALS["l_wysiwyg"]["paragraph"] = "Знак абзаца";

$GLOBALS["l_wysiwyg"]["summary"] = "Аннотация";
$GLOBALS["l_wysiwyg"]["isheader"] = "Является заголовком";

$GLOBALS["l_wysiwyg"]["keyboard"] = "Клавиатура";

$GLOBALS["l_wysiwyg"]["relation"] = "Отношение";

$GLOBALS["l_wysiwyg"]["fontsize"] = "Font size"; // TRANSLATE

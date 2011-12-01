<?php
/*
 *******************************************
 plugin JUpload for Coppermine Photo Gallery
 *******************************************

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 ********************************************
 $Revision: 185 $
 $Author: etienne_sf $
 $Date: 2008-03-12 20:26:16 +0100 (mer., 12 mars 2008) $
 ********************************************
 *
 * Allows easy upload to the gallery, through a java applet. 
 * 
 * Up to date version of this script can be retrieved with the full JUpload package, here:
 * 
 * http://etienne.sf.free.fr/wiki
 * 
 * Directly here:
 * http://forum.coppermine-gallery.net/index.php/board,100.0.html
 * 
 * Support is available on this forum:
 * http://coppermine-gallery.net/forum/index.php?topic=43432
 * 
 * The applet is published on sourceforge:
 * http://jupload.sourceforge.net
 * 
 */

// Maintainer: "Abdulrhman Alkhodiry" <almooheb@gmail.com>  

// ------------------------------------------------------------------------- //
// File jupload.php
// ------------------------------------------------------------------------- //

if (defined('JUPLOAD_PHP')) {
	$lang_jupload_php = array_merge (
		$lang_jupload_php,
		array(
		  'link_title' => 'جي ابلود',
		  'link_comment' => 'رفع الصور الى الموقع, بستخدام جي ابلود',
		  'perm_denied' => 'ليست لديك الصلاحية لتنفيذ هذا الامر.<BR><BR>اذا لم تسجل الدخول, الرجاء تسجيل <a href="$1">الدخول</a> اولا',
		  'select_album' => 'الرجاء, اختيار الالبوم, الذي ستقوم برفع الصور إليه',
		  'button_update_album' => 'تحديث الالبوم',
		  'button_create_album' => 'انشاء الالبوم',
		  'success' => 'تم التنفيذ بنجاح !',
		  'error_select_album' => 'الرجاء, اختيار الالبوم اولا',
		  'error_album_name' => 'الرجاء تسمية الالبوم.',
		  'error_album_already_exists' => 'يوجد البوم بهاذا الاسم.<BR><BR>الرجاء الرجوع الى <I>الخلف</I> بالمتصفح لاختيار اسم آخر للالبوم.',
		  'album_name' => 'اسم الالبوم',
		  'album_presentation' => 'يجب عليك اختيار الالبوم. الصور التي سترسل الى الموقع سيتم تخزينها في الالبوم المحدد. <BR>اذا كان لا يوجد لديك اي إلبوم, قائمة الالبومات خالية. انقر على \'انشاء\' button to create your first album.',
		  'album_description' => 'وصف الالبوم',
		  'add_pictures' => 'اضافة صورة الى الالبوم المحدد',
		  'max_upload_size' => 'الحجم الاقصى للصورة هو $1 KB',
		  'upload_presentation' => 'اذا المربع بالاسفل لايستطيع عرض برنامج الجافا, وشريط الحالة بالاسفل يوضح وجود خطأ, الحل الافضل هو تثبيت ملفات الجافا.<BR>بعد ذلك, رفع الصور للموقع سهل جدا! فقط قم بالنقر على <B>استعراض</B> لاختيار الملفات او قم بسحب الملف و القاءه على المستعرض , ثم انقر على <B>رفع الملفات</B> لرفع الصور الى الموقع.'
		. "<BR>لستخدام <U>صفحة الرفع القديمة</U>, <a href='upload.php'>اضغط هنا</a>.",
		  'album' => 'البوم الصور',
		  //Since 2.1.0
		  'java_not_enabled' => 'المتصفح لا يستطيع عرض برامج الجافا _ ابليت. برنامج الرفع يحتاج جافا. تستطيع تحميل مشغل الجافا بسهولة من <a href="http:\\java.sun.com\jre\">موقع جافا</a>',
		  //Since 3.0.0
		  'picture_data_explanation' => 'اضغط على الرابط, وقم بأدخال البيانات في الاسفل, اذا اردت حفظ البيانات لكل الصور التى سترفع.',
		  'quota_used' => 'انت تستخدم $1 MB ($2%) من $3 MB المساحة الاجمالية.',
		  'quota_about_full' => 'احذف بعض الصور, او قم بطلب زيادة المساحة للموقع من الشركة المستضيفة.',
		  //Since 3.2.0
		  'need_approval' => 'في انتظار موافقة مدير الموقع على الصور, قبل ان تستطيع رأيتهم في الموقع.'
		)
	);
}

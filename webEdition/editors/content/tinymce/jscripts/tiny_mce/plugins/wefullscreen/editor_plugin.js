(function(){tinymce.create('tinymce.plugins.WefullscreenPlugin',{init:function(d,e){d.addCommand('mceWefullscreen',function(){var a=d.selection;var weParams=d.getParam('weFullscrenParams');d.windowManager.open({file:e+'/../../../../../wysiwyg/fullscreenEditDialog.php?we_dialog_args[editor]=tinyMce'+'&we_dialog_args[editname]=tinyMce'+'&we_dialog_args[outsideWE]='+weParams.outsideWE+'&we_dialog_args[xml]='+weParams.xml+'&we_dialog_args[removeFirstParagraph]='+weParams.removeFirstParagraph+'&we_dialog_args[baseHref]='+weParams.baseHref+'&we_dialog_args[charset]='+weParams.charset+'&we_dialog_args[cssClasses]='+weParams.cssClasses+'&we_dialog_args[bgcolor]='+weParams.bgcolor+'&we_dialog_args[language]='+weParams.language+'&we_dialog_args[screenWidth]='+weParams.screenWidth+'&we_dialog_args[screenHeight]='+weParams.screenHeight+'&we_dialog_args[className]='+weParams.className+'&we_dialog_args[fontnames]='+weParams.fontnames+'&we_dialog_args[propString]='+weParams.propString+'&we_dialog_args[contentCss]='+weParams.contentCss+'&we_dialog_args[origName]='+weParams.origName+'&we_dialog_args[tinyParams]='+weParams.tinyParams,popup_css:false,width:screen.availWidth-20,height:screen.availHeight-70,inline:1},{plugin_url:e})});d.addButton('wefullscreen',{title:tinyMceGL.wefullscreen.tooltip,cmd:'mceWefullscreen',image:e+'/img/fullscreen.gif'});d.onNodeChange.add(function(a,b,n,c){b.setDisabled('wefullscreen',false)})},getInfo:function(){return{longname:'Wefullscreen plugin',author:'webEdition e.V',authorurl:'http://www.webedition.org',infourl:'http://www.webedition.org'}}});tinymce.PluginManager.add('wefullscreen',tinymce.plugins.WefullscreenPlugin)})();
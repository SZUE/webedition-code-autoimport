(function(){tinymce.create('tinymce.plugins.WespellcheckerPlugin',{init:function(d,e){d.addCommand('mceWespellchecker',function(){var a=d.selection;d.windowManager.open({file:e+'/../../../../../../webEdition/we/include/we_modules/spellchecker/weSpellchecker.php?we_dialog_args[editor]=tinyMce&we_dialog_args[editname]=tinyMce',popup_css:false,width:500+parseInt(d.getLang('wespellchecker.delta_width',0)),height:490+parseInt(d.getLang('wespellchecker.delta_height',0)),inline:1},{plugin_url:e,some_custom_arg:'custom arg'})});d.addButton('wespellchecker',{title:'we.tt_wespellchecker',cmd:'mceWespellchecker'});d.onNodeChange.add(function(a,b,n,c){b.setDisabled('wespellchecker',false)})},getInfo:function(){return{longname:'Wespellchecker plugin',author:'webEdition e.V',authorurl:'http://www.webedition.org',infourl:'http://www.webedition.org'}}});tinymce.PluginManager.add('wespellchecker',tinymce.plugins.WespellcheckerPlugin)})();

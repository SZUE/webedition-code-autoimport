(function(){tinymce.create("tinymce.plugins.AdvancedLinkPlugin",{init:function(a,b){this.editor=a;a.addCommand("mceAdvLink",function(){a.isWeLinkInitialized=false;var c=a.selection;if(c.isCollapsed()&&!a.dom.getParent(c.getNode(),"A")){return}var wehref="";if(a.dom.getParent(c.getNode(),'A')!== null){wehref=a.dom.getParent(c.getNode(),'A').href};a.windowManager.open({file:b+"/../../../../../wysiwyg/linkDialog.php?we_dialog_args[editor]=tinyMce&we_dialog_args[href]="+encodeURIComponent(wehref)+"&we_dialog_args[cssclasses]="+a.getParam('weClassNames_urlEncoded'),width:600+parseInt(a.getLang("advlink.delta_width",0)),popup_css:false,height:600+parseInt(a.getLang("advlink.delta_height",0)),inline:1},{plugin_url:b})});a.addButton("link",{title:tinyMceGL.welink.tooltip,cmd:"mceAdvLink"});a.addShortcut("ctrl+k","advlink.advlink_desc","mceAdvLink");a.onNodeChange.add(function(d,c,f,e){c.setDisabled("link",e&&f.nodeName!="A");c.setActive("link",f.nodeName=="A"&&!f.name)})},getInfo:function(){return{longname:"Advanced link",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("advlink",tinymce.plugins.AdvancedLinkPlugin)})();
/* JCE Editor - 2.3.2.4 | 27 March 2013 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2013 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
var ColorPicker={settings:{},init:function(){var self=this,ed=tinyMCEPopup.editor,color=tinyMCEPopup.getWindowArg('input_color')||'#FFFFFF';$('#tmp_color').val(color).colorpicker($.extend(this.settings,{dialog:true,insert:function(){return ColorPicker.insert();},close:function(){return tinyMCEPopup.close();}}));$('button#insert').button({icons:{primary:'ui-icon-check'}});$('#jce').css('display','block');},insert:function(){var color=$("#colorpicker_color").val(),f=tinyMCEPopup.getWindowArg('func');tinyMCEPopup.restoreSelection();if(f)
f(color);tinyMCEPopup.close();}};tinyMCEPopup.onInit.add(ColorPicker.init,ColorPicker);
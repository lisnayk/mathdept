/* JCE Editor - 2.3.2.4 | 27 March 2013 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2013 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
JCEMediaBox={Popup:{addons:{},setAddons:function(n,o){if(typeof this.addons[n]=='undefined'){this.addons[n]={};}
$.extend(this.addons[n],o);},getAddons:function(n){if(n){return this.addons[n];}
return this.addons;},getAddon:function(v,n){var t=this,cp=false,r;var addons=this.getAddons(n);$.each(addons,function(addon,o){var fn=o[addon]||function(){};r=fn.call(this,v);if(typeof r!='undefined'){cp=r;}});return cp;}},trim:function(s){return $.trim(s);},setDimensions:function(w,h){$.Plugin.setDimensions(w,h,'jcemediabox_popup_');}};WFPopups.addPopup('jcemediabox',{setup:function(){var self=this;$('#jcemediabox_popup_icon').change(function(){self.setIcon();});$('span.add','#jcemediabox_popup_params').click(function(){var $item=$('li:first','#jcemediabox_popup_params').clone().appendTo('#jcemediabox_popup_params');$('input',$item).val('');$('span.add',$item).hide();$('span.remove',$item).css('display','inline-block').click(function(){$item.remove();});});},check:function(n){return/jce(popup|_popup|lightbox)/.test(n.className);},getMediaType:function(n){var mt;switch(n.type){case'image/gif':case'image/jpeg':case'image/png':case'image/*':case'image':mt='image';break;case'iframe':mt='iframe';break;case'director':case'application/x-director':mt='application/x-director';break;case'windowsmedia':case'mplayer':case'application/x-mplayer2':mt='application/x-mplayer2';break;case'quicktime':case'video/quicktime':mt='video/quicktime';break;case'real':case'realaudio':case'audio/x-pn-realaudio-plugin':mt='audio/x-pn-realaudio-plugin';break;case'divx':case'video/divx':mt='video/divx';break;case'flash':case'application/x-shockwave-flash':mt='application/x-shockwave-flash';break;case'ajax':case'text/xml':case'text/html':mt='text/html';break;}
if(!mt&&n.href){var o=JCEMediaBox.Popup.getAddon(n.href);if(o&&o.type){mt=o.type;}}
return mt||n.type||'';},remove:function(n){var ed=tinyMCEPopup.editor;$.each(['jcepopup','jcelightbox','jcebox','icon-left','icon-right','icon-top-left','icon-top-right','icon-bottom-left','icon-bottom-right','noicon','noshow','autopopup-single','autopopup-multiple'],function(i,v){ed.dom.removeClass(n,v);});},convertData:function(s){var a=[];function trim(s){return s.replace(/:"([^"]+)"/,function(a,b){return':"'+b.replace(/^\s+|\s+$/,'').replace(/\s*::\s*/,'::')+'"';});}
s=s.replace(/:"([^"]+)"/,function(a,b){return':"'+b.replace(/^\s+|\s+$/,'').replace(/\s*::\s*/,'::')+'"';});if(/^{[\w\W]+}$/.test(s)){return $.parseJSON(trim(s));}
if(/\w+\[[^\]]+\]/.test(s)){s=s.replace(/([\w]+)\[([^\]]+)\](;)?/g,function(a,b,c,d){return'"'+b+'":"'+tinymce.DOM.encode(c)+'"'+(d?',':'');});return $.parseJSON('{'+trim(s)+'}');}},getAttributes:function(n,index){var ed=tinyMCEPopup.editor,data={},rv,v;index=index||0;index=index||0;var title=ed.dom.getAttrib(n,'title');var rel=ed.dom.getAttrib(n,'rel');var icon=/noicon/g.test(n.className);var hide=/noshow/g.test(n.className);if(/(autopopup(.?|-single|-multiple))/.test(n.className)){v=/autopopup-multiple/.test(n.className)?'autopopup-multiple':'autopopup-single';$('#jcemediabox_popup_autopopup').val(v);}
$('#jcemediabox_popup_icon').val(icon?0:1);$('#jcemediabox_popup_icon_position').prop('disabled',icon);$('#jcemediabox_popup_hide').val(hide?1:0);if(s=/icon-(top-right|top-left|bottom-right|bottom-left|left|right)/.exec(n.className)){$('#jcemediabox_popup_icon_position').val(s[0]);}
var relRX='(alternate|stylesheet|start|next|prev|contents|index|glossary|copyright|chapter|section|subsection|appendix|help|bookmark|nofollow|licence|tag|friend)';var json=ed.dom.getAttrib(n,'data-json')||ed.dom.getAttrib(n,'data-mediabox');if(json){data=this.convertData(json);}
if(rel&&/\w+\[.*\]/.test(rel)){var ra='';if(rv=new RegExp(relRX,'g').exec(rel)){ra=rv[1];rel=rel.replace(new RegExp(relRX,'g'),'');}
data=this.convertData($.trim(rel));data.rel=ra;}else{var group=$.trim(rel.replace(new RegExp(relRX,'g'),''));$('#jcemediabox_popup_group').val(group);}
var params=[];if(/::/.test(data.title)){var parts=data.title.split('::');if(parts.length>1){data.caption=parts[1];}
data.title=parts[0];}
$.each(data,function(k,v){if($('#jcemediabox_popup_'+k).get(0)){v=tinymce.DOM.decode(v);if(k=='title'||k=='caption'){$('input[name^="jcemediabox_popup_'+k+'"]').eq(index).val(v);}else{$('#jcemediabox_popup_'+k).val(v);}
delete data[k];}});var x=0;$.each(data,function(k,v){if(v!==''){if(x==0){$('li:first input.name','#jcemediabox_popup_params').val(k);$('li:first input.value','#jcemediabox_popup_params').val(v);}else{var $item=$('li:first','#jcemediabox_popup_params').clone().appendTo('#jcemediabox_popup_params');$('input.name',$item).val(k);$('input.value',$item).val(decodeURIComponent(v));$('span.add',$item).hide();$('span.remove').css('display','inline-block');}}
x++;});$('#jcemediabox_popup_mediatype').val(this.getMediaType(n));$.extend(data,{src:ed.dom.getAttrib(n,'href'),type:ed.dom.getAttrib(n,'type')||''});return data;},setAttributes:function(n,args,index){var self=this,ed=tinyMCEPopup.editor;index=index||0;this.remove(n);index=index||0;ed.dom.addClass(n,'jcepopup');var auto=$('#jcemediabox_popup_autopopup').val();if(auto){ed.dom.addClass(n,auto);}
var data={};tinymce.each(['group','width','height'],function(k){var v=$('#jcemediabox_popup_'+k).val();if(v==''||v==null){if(args[k]){v=args[k];}else{return;}}
data[k]=v;});tinymce.each(['title','caption'],function(k){var v=$('input[name^=jcemediabox_popup_'+k+']').eq(index).val();if(v==''||v==null||typeof v==='undefined'){if(args[k]){v=args[k];}else{return;}}
data[k]=v;});$('li','#jcemediabox_popup_params').each(function(){var k=$('input.name',this).val();var v=$('input.value',this).val();if(k!==''&&v!==''){data[k]=v;}});$.extend(data,args.data||{});if(data.title&&data.caption){data.title=data.title+'::'+data.caption;delete data.caption;}
var mt=$('#jcemediabox_popup_mediatype').val()||n.type||data.type||'';ed.dom.setAttrib(n,'type',mt);if(data.type){delete data.type;}
var props=$.map(data,function(v,k){return k+'['+v+']';});var rel=ed.dom.getAttrib(n,'rel','');if(rel){rel=rel.replace(/([a-z0-9]+)(\[([^\]]+)\]);?/gi,'');}
ed.dom.setAttrib(n,'rel',$.trim(rel+' '+props.join(';')));ed.dom.setAttrib(n,'data-json','');ed.dom.setAttrib(n,'data-mediabox','');if($('#jcemediabox_popup_icon').val()==0){ed.dom.addClass(n,'noicon');}else{ed.dom.addClass(n,$('#jcemediabox_popup_icon_position').val());}
if($('#jcemediabox_popup_hide').val()==1){ed.dom.addClass(n,'noshow');}
ed.dom.setAttrib(n,'target','_blank');},setIcon:function(){var v=$('#jcemediabox_popup_icon').val();if(parseInt(v)){$('#jcemediabox_popup_icon_position').removeAttr('disabled');}else{$('#jcemediabox_popup_icon_position').attr('disabled','disabled');}},onSelect:function(){},onSelectFile:function(args){$.each(args,function(k,v){$('#jcemediabox_popup_'+k).val(v);});}});

var EditInPlace=function(){};EditInPlace.defaults={id:false,save_url:false,type:'text',select_text:false,size:false,max_size:100,rows:false,cols:50,options:false,escape_function:escape,on_blur:'',mouseover_class:'eip_mouseover',editfield_class:'eip_mouseover',savebutton_text:'SAVE',savebutton_class:'eip_savebutton',cancelbutton_text:'CANCEL',cancelbutton_class:'eip_cancelbutton',saving_text:'Saving...',saving_class:'eip_saving',empty_text:'Click To Edit',empty_class:'eip_empty',edit_title:'Click To Edit',click:'click',savefailed_text:'Failed to save changes.',ajax_data:false,is_empty:false,orig_text:'',mouseover_observer:false,mouseout_observer:false,mouseclick_observer:false};EditInPlace.options={};EditInPlace.makeEditable=function(args){var id=args['id'];var id_opt=EditInPlace._getOptionsReference(id);for(var i in EditInPlace.defaults){id_opt[i]=EditInPlace.defaults[i];}
for(var i in args){id_opt[i]=args[i];}
id_opt['orig_text']=$(id).innerHTML;if($(id).innerHTML==''){id_opt['is_empty']=true;$(id).innerHTML=id_opt['empty_text'];Element.addClassName(id,id_opt['empty_class']);}
id_opt['mouseover_observer']=EditInPlace._mouseOver.bindAsEventListener();id_opt['mouseout_observer']=EditInPlace._mouseOut.bindAsEventListener();id_opt['mouseclick_observer']=EditInPlace._mouseClick.bindAsEventListener();Event.observe(id,'mouseover',id_opt['mouseover_observer']);Event.observe(id,'mouseout',id_opt['mouseout_observer']);Event.observe(id,id_opt['click'],id_opt['mouseclick_observer']);$(id).title=id_opt['edit_title'];return(id_opt);};EditInPlace._getOptionsReference=function(id){if(!EditInPlace.options[id]){EditInPlace.options[id]={};}
return(EditInPlace.options[id]);};EditInPlace._mouseOver=function(event){var id=Event.element(event).id;var id_opt=EditInPlace._getOptionsReference(id);Element.addClassName(id,id_opt['mouseover_class']);};EditInPlace._mouseOut=function(event){var id=Event.element(event).id;var id_opt=EditInPlace._getOptionsReference(id);Element.removeClassName(id,id_opt['mouseover_class']);};EditInPlace._mouseClick=function(event){var id=Event.element(event).id;var id_opt=EditInPlace._getOptionsReference(id);var form=EditInPlace._formField(id)+EditInPlace._formButtons(id);Element.hide(id);new Insertion.After(id,form);Field.focus(id+'_edit');Event.observe(id+'_save','click',EditInPlace._saveClick);Event.observe(id+'_cancel','click',EditInPlace._cancelClick);Event.observe(id+'_edit','blur',function(event){switch(id_opt['on_blur']){case'cancel':EditInPlace._cancelClick(false,id);break;case'save':EditInPlace._saveClick(false,id);break;default:break;}});};EditInPlace._saveClick=function(event,id){if(!id){id=Event.element(event).id.replace(/_save$/,'');}
var id_opt=EditInPlace._getOptionsReference(id);var new_text=id_opt['escape_function']($F(id+'_edit'));var params='id='+id+'&content='+new_text;if(id_opt['type']=='select'){params+='&option_name='+$(id+'_option_'+new_text).innerHTML;}
if(id_opt['ajax_data']){for(var i in id_opt['ajax_data']){var url_data=id_opt['escape_function'](id_opt['ajax_data'][i]);params+='&'+i+'='+url_data;}}
var saving='<span class="'
+id_opt['saving_class']+'">'+id_opt['saving_text']+'</span>\n';Event.stopObserving(id,'mouseover',id_opt['mouseover_observer']);Event.stopObserving(id,'mouseout',id_opt['mouseout_observer']);Event.stopObserving(id,id_opt['click'],id_opt['mouseclick_observer']);Element.remove(id+'_editor');$(id).innerHTML=saving;Element.show(id);var ajax_req=new Ajax.Request(id_opt['save_url'],{method:'post',postBody:params,onSuccess:function(t){EditInPlace._saveComplete(id,t);},onFailure:function(t){EditInPlace._saveFailed(id,t);}});};EditInPlace._cancelClick=function(event,id){if(!id){id=Event.element(event).id.replace(/_cancel$/,'');}
var id_opt=EditInPlace._getOptionsReference(id);Element.remove(id+'_editor');Element.removeClassName(id,id_opt['editfield_class']);if($(id).innerHTML==''){id_opt['id_empty']=true;$(id).innerHTML=id_opt['empty_text'];Element.addClassName(id,id_opt['empty_class']);}
Element.show(id);};EditInPlace._saveComplete=function(id,t){var id_opt=EditInPlace._getOptionsReference(id);if(t.responseText==''){id_opt['is_empty']=true;$(id).innerHTML=id_opt['empty_text'];Element.addClassName(id,id_opt['empty_class']);}
else{id_opt['is_empty']=false;Element.removeClassName(id,id_opt['empty_class']);$(id).innerHTML=t.responseText;}
id_opt['orig_text']=t.responseText;Event.observe(id,'mouseover',id_opt['mouseover_observer']);Event.observe(id,'mouseout',id_opt['mouseout_observer']);Event.observe(id,id_opt['click'],id_opt['mouseclick_observer']);};EditInPlace._saveFailed=function(id,t){var id_opt=EditInPlace.getOptionsReference(id);$(id).innerHTML=id_opt['orig_text'];Element.removeClassName(id,id_opt['editfield_class']);alert(id_opt['savefailed_text']);Event.observe(id,'mouseover',id_opt['mouseover_observer']);Event.observe(id,'mouseout',id_opt['mouseout_observer']);Event.observe(id,id_opt['click'],id_opt['mouseclick_observer']);};EditInPlace._formField=function(id){var id_opt=EditInPlace._getOptionsReference(id);if(id_opt['is_empty']==true){Element.removeClassName(id,id_opt['empty_class']);$(id).innerHTML='';}
var field='<span id="'+id+'_editor">\n';if(id_opt['type']=='text'){var size=id_opt['orig_text'].length+15;if(size>id_opt['max_size']){size=id_opt['max_size'];}
size=(id_opt['size']?id_opt['size']:size);field+='<input id="'+id+'_edit" class="'
+id_opt['editfield_class']+'" name="'+id
+'_edit" type="text" size="'+size+'" value="'
+id_opt['orig_text']+'"';if(id_opt['select_text']){field+='onfocus="this.select();"';}
field+=' /><br />\n';}
else if(id_opt['type']=='textarea'){var rows=(id_opt['orig_text'].length/id_opt['cols'])+4;rows=(id_opt['rows']?id_opt['rows']:rows);field+='<textarea id="'+id+'_edit" class="'
+id_opt['editfield_class']+'" name="'+id+'_edit" rows="'
+rows+'"cols="'+id_opt['cols']+'"'
if(id_opt['select_text']){field+='onfocus="this.select();"';}
field+='>'+id_opt['orig_text']+'</textarea><br />\n';}
else if(id_opt['type']=='select'){field+='<select id="'+id+'_edit" class="'
+id_opt['editfield_class']+'" name="'+id+'_edit">\n';for(var i in id_opt['options']){field+='<option id="'+id+'_option_'+i+'" name="'+id
+'_option_'+i+'" value="'+i+'"';if(id_opt['options'][i]==$(id).innerHTML){field+=' selected="selected"';}
field+='>'+id_opt['options'][i]+'</option>\n';}
field+='</select>\n';}
return(field);};EditInPlace._formButtons=function(id){var id_opt=EditInPlace._getOptionsReference(id);return('<span><input id="'+id+'_save" class="'+id_opt['savebutton_class']
+'" type="button" value="'+id_opt['savebutton_text']
+'" /> OR <input id="'+id+'_cancel" class="'
+id_opt['cancelbutton_class']+'" type="button" value="'
+id_opt['cancelbutton_text']+'" /></span></span>');};
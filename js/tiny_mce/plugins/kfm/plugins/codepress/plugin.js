function plugin_codepress(){
	this.name='codepress';
	this.title=kfm.lang.EditTextFile;
	this.mode=0; //only one file
	this.defaultOpener=1;
	this.writable=2;
	this.category='edit';
	this.extensions=['asp','autoit','css','csharp','html','tpl','htm','java','j','js','perl','php','ruby','sql','txt','vbscript','vba','xsl','xml'];
	this.doFunction=function(files){
		var F=File_getInstance(files[0]);
		var url='plugins/codepress/codepress.php?id='+files[0]+'&random='+Math.random();
		kfm_pluginIframeShow(url);
		if(F.writable)kfm_pluginIframeButton('codepress_save('+files[0]+')','Save');
	}
}
kfm_addHook(new plugin_codepress());

function codepress_save(id){
	var text;
	var txtarea=document.getElementById('plugin_iframe_element').contentWindow.document.getElementById('editor_area');
	if(txtarea)text=txtarea.value; // browser is not supported, if codepress works, the id is changed to editor_area_cp
	else text=document.getElementById('plugin_iframe_element').contentWindow.editor_area.getCode();
	x_kfm_saveTextFile(id,text,kfm_showMessage);
}

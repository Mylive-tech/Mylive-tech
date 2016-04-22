/**
 * JavaScript setting for tinymce integration
 * @author Praveen Rajan
 */ 
(function() {
    tinymce.create('tinymce.plugins.cvglink', {
        init : function(ed, url) {
	    	ed.addCommand('cvg', function() {
				ed.windowManager.open({
					file : ajaxurl + '?action=cvg_tinymce',
					width : 360 ,
					height : 200 ,
					inline : 1
				}, {
					plugin_url : url 
				});
			});
			ed.addButton('cvglink', {
				title : 'Cool Video Gallery Toolbar',
				cmd : 'cvg',
				image : url+'/video-mce.png',
			});
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('cvglink', tinymce.plugins.cvglink);
})();
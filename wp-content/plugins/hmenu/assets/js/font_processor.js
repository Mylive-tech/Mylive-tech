//PROCESS FONTS

//load
jQuery(function(){
	//functions
	process_font_pack();
});

//process the font pack
function process_font_pack(callback){
	//load the html
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			'action': 'hmenu_process_file'
		},
		dataType: "json"
	}).done(function(response){	
		//console_log(response + " callback -> " + callback);
		//check data
		if(typeof callback !== 'undefined'){
			eval(""+ callback +"("+response+");");
		}
	}).fail(function(){
		 //page error
	});		
}
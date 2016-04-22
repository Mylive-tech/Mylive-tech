<?php

	/*
		notes:
		------
		- All actions are prefixed by the plugin prefix
			e.g. if the plugin prefix is "hplugin_" and the action name is "get_data", the actions, as referenced by the ajax call, will be "hplugin_get_data"
		- Ensure that all "actions" are unique
		- User registrations are registered for administrators as well to ensure that functionality remains the same if logged in
	*/

	#ADMIN AJAX CALLS
	$backend_ajax_calls = array( //all methods must be contained by the backend class				
		//font load
		array('action' => 'load_fonts','method' => 'get_fonts'),
		//validate mega menu
		array('action' => 'load_validate_mega','method' => 'validate_mega'),
		//validate custom nav items
		array('action' => 'load_validate_custom','method' => 'validate_custom'),
		//validate custom nav items
		array('action' => 'load_validate_custom_method','method' => 'validate_custom_method')
	);
	$class_update_ajax_calls = array( //all methods must be contained by the backend class
		//update object
		array('action' => 'send_update_object','method' => 'update_object'),
		//update object
		array('action' => 'run_delete_menu','method' => 'delete_menu')
	);
	$class_insert_ajax_calls = array( //all methods must be contained by the backend class
		//menu insert
		array('action' => 'transfer_menu','method' => 'insert_menu')
	);
	$class_get_ajax_calls = array( //all methods must be contained by the backend class
		//menu load
		array('action' => 'load_menus','method' => 'get_menus'),
		//main menu object
		array('action' => 'load_menu_object','method' => 'get_main_menu_object'),
		//main menu object
		array('action' => 'load_presets','method' => 'get_presets'),
		//main menu object
		array('action' => 'load_pages','method' => 'get_pages'),
		//main locations
		array('action' => 'load_locations','method' => 'get_menu_locations')
	);
	$class_generate_ajax_calls = array( //all methods must be contained by the backend class
		//generate files
		array('action' => 'generate','method' => 'generate_files')
	);
	
	#USER AJAX CALLS
	$frontend_ajax_calls = array( //all methods must be contained by the frontend class
		//font load
		array('action' => 'load_frontend_fonts','method' => 'get_frontend_fonts'),
		//check menu status
		array('action' => 'check_menu_status','method' => 'check_menu_status'),
		//mega content load
		array('action' => 'load_frontend_mega','method' => 'get_mega_content'),
		//get count
		array('action' => 'get_count','method' => 'get_count')
	);
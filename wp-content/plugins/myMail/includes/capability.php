<?php
$mymail_capabilities = apply_filters('mymail_capabilities', array(

		'edit_newsletters' => array(
			'title' => __('edit campaigns' ,'mymail'),
			'roles' => array('contributor', 'author', 'editor')
		),

		'publish_newsletters' => array(
			'title' => __('send campaigns' ,'mymail'),
			'roles' => array('author', 'editor')
		),

		'delete_newsletters' => array(
			'title' => __('delete campaigns' ,'mymail'),
			'roles' => array('contributor', 'author', 'editor')
		),

		'edit_others_newsletters' => array(
			'title' => __('edit others campaigns' ,'mymail'),
			'roles' => array('editor')
		),

		'delete_others_newsletters' => array(
			'title' => __('delete others campaigns' ,'mymail'),
			'roles' => array('editor')
		),

		'duplicate_newsletters' => array(
			'title' => __('duplicate campaigns' ,'mymail'),
			'roles' => array('author', 'editor')
		),

		'duplicate_others_newsletters' => array(
			'title' => __('duplicate others campaigns' ,'mymail'),
			'roles' => array('author', 'editor')
		),

		'mymail_edit_autoresponders' => array(
			'title' => __('edit autoresponders' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_edit_others_autoresponders' => array(
			'title' => __('edit others autoresponders' ,'mymail'),
			'roles' => array('editor')
		),


		'mymail_change_template' => array(
			'title' => __('change template' ,'mymail'),
			'roles' => array('editor')
		),
		'mymail_save_template' => array(
			'title' => __('save template' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_see_codeview' => array(
			'title' => __('see codeview' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_change_plaintext' => array(
			'title' => __('change text version' ,'mymail'),
			'roles' => array('editor')
		),


		'mymail_edit_subscribers' => array(
			'title' => __('edit subscribers' ,'mymail'),
			'roles' => array( 'editor')
		),

		'mymail_add_subscribers' => array(
			'title' => __('add subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_delete_subscribers' => array(
			'title' => __('delete subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_edit_forms' => array(
			'title' => __('edit forms' ,'mymail'),
			'roles' => array( 'editor')
		),

		'mymail_add_forms' => array(
			'title' => __('add forms' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_delete_forms' => array(
			'title' => __('delete forms' ,'mymail'),
			'roles' => array('editor')
		),


		'mymail_manage_subscribers' => array(
			'title' => __('manage subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_import_subscribers' => array(
			'title' => __('import subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_import_wordpress_users' => array(
			'title' => __('import WordPress Users' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_export_subscribers' => array(
			'title' => __('export subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_bulk_delete_subscribers' => array(
			'title' => __('bulk delete subscribers' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_add_lists' => array(
			'title' => __('add lists' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_edit_lists' => array(
			'title' => __('edit lists' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_delete_lists' => array(
			'title' => __('delete lists' ,'mymail'),
			'roles' => array('editor')
		),



		'mymail_manage_templates' => array(
			'title' => __('manage templates' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_edit_templates' => array(
			'title' => __('edit templates' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_delete_templates' => array(
			'title' => __('delete templates' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_upload_templates' => array(
			'title' => __('upload templates' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_update_templates' => array(
			'title' => __('update templates' ,'mymail'),
			'roles' => array()
		),

		'mymail_dashboard_widget' => array(
			'title' => __('see dashboard widget' ,'mymail'),
			'roles' => array('editor')
		),

		'mymail_manage_capabilities' => array(
			'title' => __('manage capabilities' ,'mymail'),
			'roles' => array()
		),

));

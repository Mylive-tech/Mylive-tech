<?php

	$id = isset($_GET['ID']) ? intval($_GET['ID']) : NULL;

	$currentpage = isset($_GET['tab']) ? $_GET['tab'] : 'structure';

	$is_new = isset($_GET['new']);

	if(!$is_new){
		if(!($form = $this->get($id, true))){
			wp_die('<h2>'.__('This form does not exist or has been deleted!', 'mymail').'</h2>');
		}

	}else{

		if(!current_user_can('mymail_add_forms' )) wp_die(__("You don't have the right permission to add new forms", 'mymail'));

		$form = $this->get_empty();
		$form->submit = mymail_text('submitbutton');
		$form->fields = array((object) array(
			'field_id' => 'email',
			'error_msg' => '',
			'name' => mymail_text('email'),
			'required' => true
		));
		if(isset($_POST['mymail_data'])){

			$form = (object) wp_parse_args( $_POST['mymail_data'], (array) $form );

		}

	}
	$timeformat = get_option('date_format').' '.get_option('time_format');
	$timeoffset = mymail('helper')->gmt_offset(true);
	$customfields = mymail()->get_custom_fields();

	$now = time();

	$tabindex = 1;


	$defaultfields = array(
		'email' => mymail_text('email'),
		'firstname' => mymail_text('firstname'),
		'lastname' => mymail_text('lastname'),
	);
	if ($customfields) {
		foreach ($customfields as $field => $data) {
			$defaultfields[$field] = $data['name'];
		}
	}

?>
<div class="wrap<?php echo ($is_new) ? ' new' : '' ?>">
<form id="form_form" action="<?php echo add_query_arg(array('ID' => $id)) ?>" method="post">
<?php wp_nonce_field( 'mymail_nonce' ); ?>
<div id="icon-edit" class="icon32"></div>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<p class="alignright">
	<?php if(!$is_new && current_user_can('mymail_delete_forms')) : ?>
		<input type="submit" name="delete" class="button button-large" value="<?php _e('delete Form', 'mymail' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this form?', 'mymail' ); ?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary" value="<?php _e('Save', 'mymail' ); ?>">
</p>
<div id="icon-edit" class="icon32"></div>
<h2>
<?php

if($is_new){
	_e('Add new Form', 'mymail');
}else{
	_e('Edit Form', 'mymail');
?>
<input type="hidden" id="ID" name="mymail_data[ID]" value="<?php echo intval($form->ID) ?>">
<?php if(current_user_can('mymail_add_forms' )) : ?>
<a href="edit.php?post_type=newsletter&page=mymail_forms&new" class="add-new-h2"><?php _e( 'add New', 'mymail' ); ?></a>
<?php endif; ?>
<?php } ?>
</h2>
<?php if(!$is_new) : ?>
<h2 class="nav-tab-wrapper">

	<a class="nav-tab <?php echo ('structure' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_forms&ID=<?php echo $id ?>"><?php _e('Fields', 'mymail')?></a>

	<a class="nav-tab <?php echo ('design' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_forms&ID=<?php echo $id ?>&tab=design"><?php _e('Design', 'mymail')?></a>

	<a class="nav-tab <?php echo ('settings' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_forms&ID=<?php echo $id ?>&tab=settings"><?php _e('Settings', 'mymail')?></a>

	<a class="nav-tab <?php echo ('use' == $currentpage) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mymail_forms&ID=<?php echo $id ?>&tab=use"><?php _e('Use it!', 'mymail')?></a>

</h2>
<?php endif; ?>
<div id="titlewrap">
	<input type="text" class="widefat" name="mymail_data[name]" size="30" value="<?php echo $form->name ?>" id="title" spellcheck="true" autocomplete="off" placeholder="Enter Form Name">
</div>
<?php if($currentpage == 'structure') :?>
<?php if(!$is_new) : ?>

<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php _e('Design', 'mymail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<?php endif; ?>

<p class="description"><?php _e('Define the structure of your form below. Drag available fields in the left area to add them to your form. Rearrange fields by dragging fields around', 'mymail') ?></p>
<div id="form-builder">
	<fieldset id="form-structure">
		<legend><?php _e('Form Fields', 'mymail') ?></legend>

		<ul class="form-order sortable"><?php

				foreach($form->fields as $field){
					?><li class="field-<?php echo $field->field_id ?>">
				<label><?php echo $field->name ?></label>
				<div>
				<span class="label"><?php _e('Label', 'mymail') ?>:</span>
				<input class="label widefat" type="text" name="mymail_structure[fields][<?php echo $field->field_id ?>]" data-name="mymail_structure[fields][<?php echo $field->field_id ?>]" value="<?php echo esc_attr($field->name) ?>" title="<?php _e('define a label for this field', 'mymail'); ?>" placeholder="<?php echo $field->name ?>">
					<span class="alignright required-field"><input type="checkbox" name="mymail_structure[required][<?php echo $field->field_id ?>]" data-name="mymail_structure[required][<?php echo $field->field_id ?>]" class="form-order-check-required" value="1" <?php checked($field->required) ?> <?php if($field->field_id == 'email') echo ' disabled'; ?>> <?php _e('required', 'mymail'); ?>
					<a class="field-remove" title="<?php _e('remove field', 'mymail'); ?>">&#10005;</a>

					</span>
				</div>
				<div>
				<span class="label"><?php _e('Error Message', 'mymail') ?>:</span>
				<input class="label widefat error-msg" type="text" name="mymail_structure[error_msg][<?php echo $field->field_id ?>]" data-name="mymail_structure[error_msg][<?php echo $field->field_id ?>]" value="<?php echo esc_attr($field->error_msg) ?>" title="<?php _e('define an error message for this field', 'mymail'); ?>" placeholder="<?php _e('Error Message (optional)', 'mymail') ?>">
				</div>
				</li><?php

				}

				?></ul>
				<h4><label><?php _e('Button Label', 'mymail' ); ?>: <input type="text" name="mymail_data[submit]" class="widefat regular-text" value="<?php echo esc_attr($form->submit); ?>" placeholder="<?php echo mymail_text('submitbutton')?>" ></label></h4>
	</fieldset>

	<fieldset id="form-fields">

	<legend><?php _e('Available Fields', 'mymail') ?></legend>

		<ul class="form-order sortable"><?php

				$used = wp_list_pluck( $form->fields, 'field_id' );

				$fields = array_intersect_key($defaultfields, array_flip(array_keys(array_diff_key($defaultfields, array_flip($used)))));

				foreach($fields as $field_id => $name){

					?><li class="field-<?php echo $field_id ?>">
				<label><?php echo $name ?></label>
				<div>
				<span class="label"><?php _e('Label', 'mymail') ?>:</span>
				<input class="label widefat" type="text" data-name="mymail_structure[fields][<?php echo $field_id ?>]" value="<?php echo esc_attr($name) ?>" title="<?php _e('define a label for this field', 'mymail'); ?>" placeholder="<?php echo $name ?>">
					<span class="alignright required-field"><input type="checkbox" data-name="mymail_structure[required][<?php echo $field_id ?>]" class="form-order-check-required" value="1"> <?php _e('required', 'mymail'); ?>
					<a class="field-remove" title="<?php _e('remove field', 'mymail'); ?>">&#10005;</a>
					</span>
				</div>
				<div>
				<span class="label"><?php _e('Error Message', 'mymail') ?>:</span>
				<input class="label widefat error-msg" type="text" name="mymail_structure[error_msg][<?php echo $field_id ?>]" data-name="mymail_structure[error_msg][<?php echo $field_id ?>]" value="" title="<?php _e('define an error message for this field', 'mymail'); ?>" placeholder="<?php _e('Error Message (optional)', 'mymail') ?>">
				</div>
				</li><?php

				}

				?></ul>
				<p class="description"><?php printf(__('add more custom fields on the %s.', 'mymail'), '<a href="options-general.php?page=newsletter-settings#subscribers">'.__('Settings Page', 'mymail').'</a>') ?></p>

	</fieldset>
</div>

<?php if(!$is_new) : ?>

<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php _e('Design', 'mymail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<?php endif; ?>


<?php elseif ($currentpage == 'design') :  ?>

<?php

	$style = $form->style;

 ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php _e('back to Fields', 'mymail' ); ?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php _e('define the Options', 'mymail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<div id="form-preview">


	<fieldset id="design">
		<legend><?php _e('Form Design', 'mymail') ?></legend>

	<p><label><input type="checkbox" id="themestyle" checked> <?php _e('Include your Theme\'s style.css', 'mymail') ?></label></p>
	<p class="description clear"><?php _e('Your form may look different depending on the place you are using it!', 'mymail') ?></p>
	<div id="form-design">
		<iframe id="form-design-iframe" width="100%" height="500" allowTransparency="true" frameborder="0" scrolling="no" src="<?php echo MYMAIL_URI.'form.php?id='.$id.'&s=1&edit='.wp_create_nonce('mymailiframeform' ); ?>"></iframe>
	</div>
	<div id="form-design-options">
	<div class="form-design-options-nav">
		<div class="designnav contextual-help-tabs hide-if-no-js">
		<ul>
			<li><a href="#tab-global" class="nav"><?php _e('Global', 'mymail') ?></a></li>
			<li><a href="#tab-buttons" class="nav"><?php _e('Button', 'mymail') ?></a></li>
			<li><a href="#tab-fields" class="nav"><?php _e('Fields', 'mymail') ?></a></li>
			<li><a href="#tab-messages" class="nav"><?php _e('Info Messages', 'mymail') ?></a></li>
		</ul>
		</div>
	</div>
	<div class="form-design-options-tabs">

		<div class="designtab" id="tab-global">
			<ul>
			<li><label><?php _e('Label Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-wrapper label', 'color'); ?>" data-selector=".mymail-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Input Text Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-wrapper .input', 'color'); ?>" data-selector=".mymail-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Input Background Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-wrapper .input', 'background-color'); ?>" data-selector=".mymail-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Input Focus Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-wrapper .input:focus', 'background-color'); ?>" data-selector=".mymail-wrapper .input:focus" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Required Asterisk', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, 'label span.mymail-required', 'color'); ?>" data-selector="label span.mymail-required" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			</ul>
		</div>

		<div class="designtab" id="tab-buttons">
		<ul>
			<li><label><?php _e('Background Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.submit-button', 'background-color'); ?>" data-selector=".submit-button" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Text Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.submit-button', 'color'); ?>" data-selector=".submit-button" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Hover Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.submit-button:hover', 'background-color'); ?>" data-selector=".submit-button:hover" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Hover Text Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.submit-button:hover', 'color'); ?>" data-selector=".submit-button:hover" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
		</ul>
		</div>

		<div class="designtab" id="tab-fields">
		<ul>
			<?php foreach ($form->fields as $field_id => $field) { ?>
				<li><strong><?php echo $field->name ?></strong><ul>
				<li><label><?php _e('Label', 'mymail') ?></label>
					<input class="color-field" value="<?php $this->_get_style($style, '.mymail-'.$field_id.'-wrapper label', 'color'); ?>" data-selector=".mymail-<?php echo $field_id ?>-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a>
				</li>
				<li><label><?php _e('Input', 'mymail') ?></label>
					<input class="color-field" value="<?php $this->_get_style($style, '.mymail-'.$field_id.'-wrapper .input', 'color'); ?>" data-selector=".mymail-<?php echo $field_id ?>-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a>
				</li>
				<li><label><?php _e('Input Background', 'mymail') ?></label>
					<input class="color-field" value="<?php $this->_get_style($style, '.mymail-'.$field_id.'-wrapper .input', 'background-color'); ?>" data-selector=".mymail-<?php echo $field_id ?>-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a>
				</li>
				</ul></li>
			<?php } ?>

		</ul>
		</div>

		<div class="designtab" id="tab-messages">
		<ul>
			<li><label><?php _e('Success message Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-form-info.success', 'color'); ?>" data-selector=".mymail-form-info.success" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Success message Background', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-form-info.success', 'background-color'); ?>" data-selector=".mymail-form-info.success" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Error message Color', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-form-info.error', 'color'); ?>" data-selector=".mymail-form-info.error" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
			<li><label><?php _e('Error message Background', 'mymail') ?>:</label> <input class="color-field" value="<?php $this->_get_style($style, '.mymail-form-info.error', 'background-color'); ?>" data-selector=".mymail-form-info.error" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php _e('custom style', 'mymail' ); ?></a></li>
		</ul>
		</div>

	</div>
	</div>

	</fieldset>


	<input type="hidden" name="mymail_design[style]" value="<?php echo esc_attr(json_encode($form->style)) ?>" id="style">
	<div class="clear"></div>
	<fieldset>
		<legend><?php _e('Custom Style', 'mymail') ?></legend>
		<p class="description"><?php _e('add custom CSS to your form', 'mymail') ?></p>
		<div id="custom-style-wrap" class="wrapper">
		<div class="wrapper-left">
		<textarea id="custom-style" class="code" name="mymail_design[custom]"><?php echo esc_textarea($form->custom_style) ?></textarea>
		</div>
		<div class="wrapper-right">
		<input type="text" class="widefat" placeholder="<?php _e('Selector Prefix', 'mymail') ?>" id="custom-style-prefix">
		<select id="custom-style-samples" multiple>
			<option value=""><?php _e('Form selector', 'mymail') ?></option>
			<option value=" .mymail-wrapper"><?php _e('Field wrapper', 'mymail') ?></option>
			<optgroup label="<?php _e('Custom Field Wrapper divs', 'mymail') ?>">
			<?php foreach ($defaultfields as $key => $field) { ?>
			<option value=" .mymail-<?php echo $key ?>-wrapper"><?php echo $field ?></option>
			<?php } ?>
			</optgroup>
			<optgroup label="<?php _e('Custom Field Inputs', 'mymail') ?>">
			<?php foreach ($defaultfields as $key => $field) { ?>
			<option value=" .mymail-<?php echo $key ?>-wrapper input.input"><?php echo $field ?></option>
			<?php } ?>
			</optgroup>
			<optgroup label="<?php _e('Other', 'mymail') ?>">
			<option value=" label .mymail-required"><?php _e('Required Asterisk', 'mymail') ?></option>
			</optgroup>
		</select>
		</div>
		</div>
	</fieldset>
</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php _e('back to Fields', 'mymail' ); ?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php _e('define the Options', 'mymail' ); ?> &raquo;" class="button-primary button-small"></span></p>

<?php elseif ($currentpage == 'settings') :  ?>

<?php $is_profile = mymail_option('profile_form', 0) == $form->ID ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php _e('back to Design', 'mymail' ); ?>" class="button-primary button-small"></span></p>

<div id="form-options">
		<div class="subtab form" id="form-tab-<?php echo $id ?>">

		<fieldset>
			<legend><?php _e('Form Options', 'mymail') ?></legend>
				<p><label><input type="hidden" name="mymail_data[asterisk]" value="0"><input type="checkbox" name="mymail_data[asterisk]" value="1" <?php checked($form->asterisk) ?>> <?php _e('show asterisk on required fields', 'mymail'); ?></label>
				</p>

				<p><label><input type="hidden" name="mymail_data[inline]" value="0"><input type="checkbox" name="mymail_data[inline]" value="1" <?php checked($form->inline) ?>> <?php _e('place labels inside input fields', 'mymail'); ?></label>
				</p>

				<p><label><input type="hidden" name="mymail_data[prefill]" value="0"><input type="checkbox" name="mymail_data[prefill]" value="1" <?php checked($form->prefill) ?>> <?php _e('fill fields with known data if user is logged in', 'mymail'); ?></label>
				</p>

				<p><label><input type="hidden" name="mymail_data[redirect]" value=""><input id="redirect-cb" type="checkbox" <?php checked(!!$form->redirect) ?>> <?php _e('redirect after submit', 'mymail'); ?></label>
				<input type="text" id="redirect-tf" name="mymail_data[redirect]" class="widefat regular-text" value="<?php echo $form->redirect; ?>" placeholder="https://www.example.com" >
				</p>

				<p><label><input type="hidden" name="mymail_data[overwrite]" value="0"><input type="checkbox" name="mymail_data[overwrite]" value="1" <?php checked($form->overwrite) ?>> <?php _e('allow users to update their data with this form', 'mymail'); ?></label>
				</p>
		</fieldset>

		<fieldset>
			<legend><?php _e('Profile', 'mymail') ?></legend>
				<p><label><input type="hidden" name="profile_form" value="0"><input type="checkbox" name="profile_form" value="1" <?php checked($is_profile) ?> <?php if($is_profile) echo 'disabled' ?>> <?php _e('use this form as user profile.', 'mymail'); ?>

					</label>
				</p>
					<?php if(!$is_profile) :
						if($profile_form = mymail('forms')->get(mymail_option('profile_form', 0), false, false)){
							?>
							<p class="description"><?php echo sprintf(__('Currently %s is your profile form', 'mymail'), '<a href="edit.php?post_type=newsletter&page=mymail_forms&ID='.$profile_form->ID.'&tab=settings">'.$profile_form->name.'</a>') ?></p>
							<?php
						}
					?><?php endif; ?>

		</fieldset>

		<fieldset>
			<legend><?php _e('List Options', 'mymail') ?></legend>
				<p>
				<label><input type="hidden" name="mymail_data[userschoice]" value="0"><input type="checkbox" name="mymail_data[userschoice]" class="mymail_userschoice" value="1" <?php checked($form->userschoice); ?>> <?php _e('users decide which list they subscribe to', 'mymail'); ?></label>
				<br> &nbsp; <label><input type="hidden" name="mymail_data[dropdown]" value="0"><input type="checkbox" name="mymail_data[dropdown]" class="mymail_dropdown" value="1" <?php checked($form->dropdown) ?><?php disabled(!$form->userschoice) ?>> <?php _e('show drop down instead of check boxes', 'mymail'); ?></label>
				</p>
				<fieldset>
				<legend class="mymail_userschoice_td" <?php if($form->userschoice) { echo ' style="display:none"';} ?>><?php _e('subscribe new users to', 'mymail'); ?></legend>
				<legend class="mymail_userschoice_td" <?php if(!$form->userschoice) { echo ' style="display:none"';} ?>><?php _e('users can subscribe to', 'mymail'); ?></legend>

				<?php mymail('lists')->print_it(NULL, NULL, 'mymail_data[lists]', false, $form->lists);?>

				<p><label><input type="hidden" name="mymail_data[precheck]" value="0"><input type="checkbox" name="mymail_data[precheck]" value="1" <?php checked($form->precheck) ?>> <?php _e('checked by default', 'mymail'); ?></label>
				</p>
				</fieldset>
				<p><label><input type="hidden" name="mymail_data[addlists]" value="0"><input type="checkbox" name="mymail_data[addlists]" value="1" <?php checked($form->addlists) ?>> <?php _e('assign new lists automatically to this form', 'mymail'); ?></label>
				</p>

		</fieldset>

		<fieldset>
			<legend><?php _e('Double Opt In', 'mymail') ?></legend>

				<p><label><input type="radio" name="mymail_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="0" <?php checked(!$form->doubleoptin) ?>> [Single-Opt-In] <?php _e('new subscribers are subscribed instantly without confirmation.' ,'mymail') ?></label>
				</p>
				<p><label><input type="radio" name="mymail_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="1" <?php checked($form->doubleoptin) ?>> [Double-Opt-In] <?php _e('new subscribers must confirm their subscription.' ,'mymail') ?></label>
				</p>
				<div id="double-opt-in-field" class="double-opt-in-field" <?php if(!$form->doubleoptin) echo ' style="display:none"';?>>

					<fieldset>
						<legend><?php _e('Confirmation Settings', 'mymail'); ?></legend>
					<table class="nested">

						<tr>
							<td colspan="2">
							<table class="form-table">
								<tr valign="top">
									<td scope="row" width="200"><label for="mymail_text_subject"><?php _e('Subject', 'mymail'); ?>: <code>{subject}</code></label></td>
									<td><input type="text" id="mymail_text_subject" name="mymail_data[subject]" value="<?php echo esc_attr($form->subject); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mymail_text_headline"><?php _e('Headline', 'mymail'); ?>: <code>{headline}</code></label></td>
									<td><input type="text" id="mymail_text_headline" name="mymail_data[headline]" value="<?php echo esc_attr($form->headline); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mymail_text_link"><?php _e('Linktext', 'mymail'); ?>:</label> <code>{link}</code></td>
									<td><input type="text" id="mymail_text_link" name="mymail_data[link]" value="<?php echo esc_attr($form->link); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mymail_text_content"><?php _e('Text', 'mymail'); ?>: <code>{content}</code></label><p class="description"><?php echo sprintf(__('The text new subscribers get when Double-Opt-In is selected. Use %s for the link placeholder. Basic HTML is allowed' ,'mymail'),'<code>{link}</code>'); ?></p></td>
									<td><textarea id="mymail_text_content" name="mymail_data[content]" rows="10" cols="50" class="large-text"><?php echo esc_attr($form->content); ?></textarea></td>
								</tr>
								<tr><td><?php _e('used template file', 'mymail'); ?></td><td>
									<select name="mymail_data[template]">
									<?php
										$templatefiles = mymail('templates')->get_files(mymail_option('default_template'));
										foreach($templatefiles as $slug => $filedata){
											if($slug == 'index.html') continue;
									?>
										<option value="<?php echo $slug ?>"<?php selected($slug == $form->template) ?>><?php echo esc_attr($filedata['label'])?> (<?php echo $slug ?>)</option>
									<?php
										}
									?>
									</select>
									</td>
								</tr>

								<tr>
									<td><?php _e('Resend Confirmation' ,'mymail') ?></td>
									<td><div><input type="checkbox" name="mymail_data[resend]" value="1" <?php checked($form->resend) ?>> <?php echo sprintf(__('resend confirmation %1$s times with a delay of %2$s hours if user hasn\'t confirmed the subscription' ,'mymail'), '<input type="text" name="mymail_data[resend_count]" value="'.esc_attr($form->resend_count).'" class="small-text">', '<input type="text" name="mymail_data[resend_time]" value="'.esc_attr($form->resend_time).'" class="small-text">') ?></div></td>
								</tr>

								<tr><td><?php _e('redirect after confirm', 'mymail'); ?></td><td><input type="text" name="mymail_data[confirmredirect]" class="widefat" value="<?php if(isset($form->confirmredirect)) echo $form->confirmredirect; ?>" placeholder="http://www.example.com" ></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label><input type="hidden" name="mymail_data[vcard]" class="vcard" value="0"><input type="checkbox" name="mymail_data[vcard]" class="vcard" value="1" <?php checked($form->vcard); ?> data-id="<?php echo $id; ?>"> <?php _e('attach vCard to all confirmation mails' ,'mymail') ?></label>
									<div id="vcard-field" <?php if(!$form->vcard) echo ' style="display:none"';?> class="vcard-field">
									<p class="description"><?php echo sprintf(__('paste in your vCard content. You can use %s to generate your personal vcard' ,'mymail'),'<a href="http://vcardmaker.com/" class="external">vcardmaker.com</a>'); ?></p>
									<?php $vcard = $form->vcard_content ? $form->vcard_content : $this->get_vcard(); ?><textarea name="mymail_data[vcard_content]" rows="10" cols="50" class="large-text code"><?php echo esc_attr($vcard); ?></textarea>
									</div>

									</td>
								</tr>

							</table>
							</td>
						</tr>

					</table>
					</fieldset>
				</div>
		</fieldset>

		</div>

</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php _e('back to Design', 'mymail' ); ?>" class="button-primary button-small"></span></p>

<?php elseif ($currentpage == 'use') :  ?>


<p class="section-nav"><span class="alignleft"><input type="submit" name="settings" value="&laquo; <?php _e('back to Settings', 'mymail' ); ?>" class="button-primary button-small"></span></p>

<div class="clear">

	<fieldset>
		<legend><?php _e('Shortcode', 'mymail') ?></legend>
		<p class="description"><?php _e('Use this shortcode wherever they are excepted.', 'mymail') ?><?php echo sprintf(__('Read more about shortcodes at %s', 'mymail'), '<a href="https://codex.wordpress.org/Shortcode">WordPress Codex</a>') ?></p>
		<input type="text" class="code widefat" value="[newsletter_signup_form id=<?php echo $id ?>]">
	</fieldset>
	<fieldset>
		<legend><?php _e('Widget', 'mymail') ?></legend>

		<p class="description"><?php echo sprintf(__('Use this form as a %s in one of your sidebars', 'mymail'), '<a href="widgets.php">'.__('widget', 'mymail').'</a>') ?></p>

	</fieldset>

	<fieldset>
		<legend><?php _e('Subscribe Button', 'mymail') ?></legend>
		<p class="description"><?php _e('Embed a button where users can subscribe on any website', 'mymail') ?></p>

<?php
		$subscribercount = mymail('subscribers')->get_count('kilo');
		$embeddedcode = mymail('forms')->get_subscribe_button();

?>
	<div class="wrapper">

	<fieldset>
			<legend><?php _e('Button Style', 'mymail') ?></legend>
		<?php $styles = array('default', 'wp', 'twitter', 'flat', 'minimal') ?>
		<ul class="subscriber-button-style">
		<?php foreach ($styles as $i => $style) { ?>
			<li><label>
			<input type="radio" name="subscriber-button-style" value="<?php echo esc_attr($style) ?>" <?php checked( !$i ); ?>>
			<div class="btn-widget design-<?php echo $style ?> count">
				<div class="btn-count"><i></i><u></u><a><?php echo $subscribercount ?></a></div>
				<a class="btn"><?php echo $form->submit; ?></a>
			</div>
			</label></li>
		<?php } ?>
		</ul>

	</fieldset>

	<div class="clear"></div>

	<div class="wrapper-left">

		<fieldset>
			<legend><?php _e('Button Options', 'mymail') ?></legend>

		<div class="button-options-wrap">

			<p><?php _e('Popup width', 'mymail') ?>:
				<input type="text" id="buttonwidth" placeholder="480" value="480" class="small-text"></p>

			<fieldset>
				<legend><?php _e('Label', 'mymail') ?></legend>
					<p><label><input type="radio" name="buttonlabel" value="default" checked>
					<?php _e('Use Form Default', 'mymail') ?></label></p>
					<p><input type="radio" name="buttonlabel" value="custom">
					<input type="text" id="buttonlabel" placeholder="<?php echo esc_attr($form->submit); ?>" value="<?php echo esc_attr($form->submit); ?>"></p>

			</fieldset>
			<fieldset>
				<legend><?php _e('Subscriber Count', 'mymail') ?></legend>
				<p><label><input type="checkbox" id="showcount" checked> <?php _e('Display subscriber count', 'mymail') ?></label></p>
				<p><label><input type="checkbox" id="ontop"> <?php _e('Count above Button', 'mymail') ?></label></p>
			</fieldset>

		</div>

		</fieldset>

	</div>

	<div class="wrapper-right">

		<fieldset>
				<legend><?php _e('Preview and Code', 'mymail') ?></legend>

			<p><?php _e('Test your button', 'mymail') ?> &hellip;</p>
				<div class="button-preview">
					<?php echo $embeddedcode; ?>
				</div>

			<p>&hellip; <?php _e('embed it somewhere', 'mymail') ?> &hellip;</p>
				<div class="code-preview">
					<textarea class="code" readonly></textarea>
				</div>
			<p>&hellip; <?php _e('or use this shortcode on your site', 'mymail') ?></p>
				<div class="shortcode-preview">
					<input type="text" class="widefat code" readonly>
				</div>

		</fieldset>

	</div>

	</fieldset>

	<fieldset id="form-html">
		<legend><?php _e('Form HTML', 'mymail') ?></legend>
		<fieldset>
			<legend><?php _e('iFrame Version', 'mymail') ?></legend>

				<?php $embedcode = '<iframe width="%s" height="%s" allowTransparency="true" frameborder="0" scrolling="no" style="border:none" src="'.MYMAIL_URI . 'form.php?id='.$id.'%s"></iframe>';?>

				<div>
					<label><?php _e('width', 'mymail'); ?>: <input type="text" class="small-text embed-form-input" value="100%"></label>
					<label><?php _e('height', 'mymail'); ?>: <input type="text" class="small-text embed-form-input" value="500"></label>
					<label title="<?php _e('check this option to include the style.css of your theme into the form', 'mymail'); ?>"><input type="checkbox" value="1" class="embed-form-input" checked> <?php _e('include themes style.css', 'mymail'); ?></label>
					<textarea class="widefat code embed-form-output" data-embedcode="<?php echo esc_attr($embedcode) ?>"><?php echo esc_textarea($embedcode) ?></textarea>
				</div>
		</fieldset>
		<fieldset>
			<legend><?php _e('HTML Version', 'mymail') ?></legend>

				<div>
				<?php
					$form = mymail('form')->id($id);
					$form->add_class('extern');
					$form->prefill(false);
					$form->ajax(false);
					$form->embed_style(false);
					$form->referer('extern');
				?>
					<textarea class="widefat code form-output"><?php echo esc_textarea($form->render(false))?></textarea>
				</div>
		</fieldset>
	</fieldset>


</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="settings" value="&laquo; <?php _e('back to Settings', 'mymail' ); ?>" class="button-primary button-small"></span></p>

<?php endif; ?>
<hr>
<p class="alignright">
	<input type="submit" name="save" class="button button-primary" value="<?php _e('Save', 'mymail' ); ?>">
</p>
</form>
</div>

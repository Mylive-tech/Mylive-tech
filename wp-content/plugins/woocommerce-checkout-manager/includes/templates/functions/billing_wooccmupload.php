<?php
/**
 * WooCommerce Checkout Manager
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_upload_billing_scripts() {

	global $woocommerce;

	$options = get_option( 'wccs_settings3' );

	// Check if we have any buttons
	if( empty( $options['billing_buttons'] ) )
		return;

	foreach( $options['billing_buttons'] as $btn ) {

		if( $btn['type'] <> 'wooccmupload' )
			continue;
?>
<!-- Billing section: File upload -->
<script type="text/javascript">
jQuery(document).ready(function($){

	$("#billing_<?php echo $btn['cow']; ?>_field").magnificPopup({
		delegate: "a.wooccm-zoom-special", // child items selector, by clicking on it popup will open
		type: "image",
		zoom: {
			enabled: true,
			duration: 400,
			easing: "ease-out"
		}
	});

	(function post_image_content() {

		var input = document.getElementById("billing_<?php echo $btn['cow']; ?>_file"),
		formdata = false, loadfiles, formnames = [], loadfiles = [];

		$("#billing_<?php echo $btn['cow']; ?>_files_button_wccm").click( function(){
		$("#billing_<?php echo $btn['cow']; ?>_field input[type=file]").click();
			return false;
		});

		if (window.FormData) {
			formdata = new FormData();
		}

		function showUploadedItem ( source, getname, filetype ) {

			var 
			list = document.getElementById("billing_<?php echo $btn['cow']; ?>_field"),
			li   = document.createElement("span"),
			name   = document.createElement("name"),
			span   = document.createElement("span"),
			zoom   = document.createElement("a"),
			edit   = document.createElement("a"),
			dele   = document.createElement("a"),
			a   = document.createElement("a"),
			spana   = document.createElement("spana"),
			img  = document.createElement("img");

			name.innerHTML = getname;
			edit.innerHTML = "Edit";
			dele.innerHTML = "Delete";

			if (filetype.match("image.*")) {
				img.src = source;
				a.href = source;
				a.title = getname;
				edit.href = source;
				zoom.href = source;
				zoom.title = getname;
				zoom.innerHTML = "Zoom <img style=display:none />";
				li.appendChild(a);
				a.appendChild(img);
				a.className = "wooccm-zoom-special wooccm-image-holder mfp-zoom";
				zoom.className = "wooccm-zoom-special wooccm_zoom wooccm-btn wooccm-btn-zoom";
				edit.className = "wooccm_edit wooccm-btn wooccm-btn-edit enable";
			}else{
				zoom.innerHTML = "Zoom";
				li.appendChild(spana);
				spana.appendChild(img);
				spana.className = "wooccm-image-holder";
				zoom.className = "wooccm_zoom wooccm-btn disable";
				edit.className = "wooccm_edit wooccm-btn disable";
			}
			if ( ( false === filetype.match("application/ms.*") && false === filetype.match("application/x.*") && false === filetype.match("audio.*") && false === filetype.match("text.*") && false === filetype.match("video.*") ) || ( 0 === filetype.length || !filetype) ) {
				img.src = "<?php echo site_url('wp-includes/images/media/interactive.png'); ?>";
			}
			if (filetype.match("application/ms.*")) {
				img.src = "<?php echo site_url('wp-includes/images/media/spreadsheet.png'); ?>";
			}
			if (filetype.match("application/x.*")) {
				img.src = "<?php echo site_url('wp-includes/images/media/archive.png'); ?>";
			}
			if (filetype.match("audio.*")) {
				img.src = "<?php echo site_url('wp-includes/images/media/audio.png'); ?>";
			}
			if (filetype.match("text.*")) {
				img.src = "<?php echo site_url('wp-includes/images/media/text.png'); ?>";
			}
			if (filetype.match("video.*")) {
				img.src = "<?php echo site_url('wp-includes/images/media/video.png'); ?>";
			}

			li.title = getname;
			dele.title = getname;
			edit.title = getname;
			li.appendChild(name);
			li.appendChild(span);
			span.appendChild(zoom);
			span.appendChild(edit);
			span.appendChild(dele);
			list.appendChild(li);
			li.className = "wooccm_each_file";
			name.className = "wooccm_name";
			dele.id = "wooccm_dele";
			dele.className = "wooccm_dele wooccm-btn wooccm-btn-danger";
			span.className = "container";

		}

		input.addEventListener("change", function (evt) {
			$("#billing_<?php echo $btn['cow']; ?>_field").block({ message: null, overlayCSS: { background: '#fff no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

			var count = $("#billing_<?php echo $btn['cow']; ?>_files_button_wccm").data("count") || 0;
			$("#billing_<?php echo $btn['cow']; ?>_files_button_wccm").data("count", ++count);

			var img, reader, file, iname, len = this.files.length;

			for ( i = 0; i < len; i++ ) {

				file = this.files[i];

				if ( window.FileReader ) {
					reader = new FileReader();
					reader.onload = (function(theFile){
						var fileName = theFile.name,
						filetype = theFile.type;
						return function(e){
						showUploadedItem( e.target.result, fileName, filetype );
					};
					})(file); 
					reader.readAsDataURL(file);
				}

				formdata.append("billing_<?php echo $btn['cow']; ?>[]", file);
				formnames.push(file.name);
				loadfiles.push(file);

				$.ajax({
					url: "<?php echo admin_url('/admin-ajax.php?action=wooccm_front_endupload&name=billing_'.$btn['cow'].''); ?>",
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					success: function (res) {
						var result = $.parseJSON(res), new_val;
						document.getElementById("billing_<?php echo $btn['cow']; ?>").value = result;
						/* @mod - Test formatting change */
/*
						new_val = document.getElementById("billing_<?php echo $btn['cow']; ?>").value.split("||");
						new_val[0] = result[0];
						new_val[1] += result[1] + ",";
						document.getElementById("billing_<?php echo $btn['cow']; ?>").value = new_val[0] + "||" + new_val[1];
*/

						$("#billing_<?php echo $btn['cow']; ?>_field").unblock();
					}
				});

			}

			if( formdata ) {
				$("#billing_<?php echo $btn['cow']; ?>_field").unblock();
			}

		}, false);

		$("#caman_content #wooccmtoolbar #save").click( function(){

			$("#caman_content #wooccmtoolbar").block({ message: null, overlayCSS: { background: '#fff no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

			var title = this.title, the_file, wooxtro = document.getElementById("wooccmactualimage").getAttribute("data-caman-hidpi"), listing, store = [], count = $(".wooccm_each_file").each(function(){});
			formdata = new FormData();

			for(var t = 0; t < count.length; t++) {
				if( count[t].getAttribute("wooccm-attach-id") ){
					break;
				}
				store.push(count[t]);
			}

			if( store.length !== 0 ){
				listing = document.getElementById("billing_<?php echo $btn['cow']; ?>").value.split("||");
				listing = listing[1].split(",");
				listing = listing.filter(Number);

				$.each(listing, function(index, value){
					$(store[index]).attr("wooccm-attach-id", value);
				});
			}

			$(".wooccm_each_file").each(function(){
				if( this.title === title) {

					var currentgutz = this;
					this.firstElementChild.href = wooxtro;
					this.firstElementChild.firstElementChild.src = wooxtro;
					this.lastElementChild.firstElementChild.href = wooxtro;
					this.lastElementChild.lastElementChild.previousElementSibling.href = wooxtro;	

					var byteString;
					var dataURI = wooxtro; //data:image/gif;base64,R0lGODlhyAAiALM...DfD0QAADs

					if (dataURI.split(',')[0].indexOf('base64') >= 0)
						byteString = atob(dataURI.split(',')[1]);
					else
						byteString = unescape(dataURI.split(',')[1]);

					// separate out the mime component
					var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

					// write the bytes of the string to a typed array
					var ia = new Uint8Array(byteString.length);
					for (var i = 0; i < byteString.length; i++) {
						ia[i] = byteString.charCodeAt(i);
					}

					var extension = mimeString.split('/')[1];
					var fileName = "image_" + new Date().getTime() + "." + extension;
					var blob = new Blob([ia], {type:mimeString});

					the_file = new File([blob], title, {
						type: "image/png"
					});

					formdata.append("billing_<?php echo $btn['cow']; ?>[]", the_file);
					var remove = this.getAttribute("wooccm-attach-id");

					$.ajax({
						url: "<?php echo admin_url('/admin-ajax.php?action=wooccm_front_enduploadsave&name=billing_'.$btn['cow'].'&remove='); ?>" + remove,
						type: "POST",
						data: formdata,
						processData: false,
						contentType: false,
						success: function (res) {
							var result = $.parseJSON(res);
							$(currentgutz).attr("wooccm-attach-id", result);
							$("#billing_<?php echo $btn['cow']; ?>").val(function(index, value) {
								return value.replace(remove, result);
							});
							$("#caman_content #wooccmtoolbar").unblock();
							alert("billing_<?php echo ( !empty( $options['checkness']['picture_success'] ) ? $options['checkness']['picture_success'] : 'Picture Saved' ); ?>");
						}
					});

				}
			});

		});

		$(document).on('click','.wooccm_dele',function(){

			var c = confirm("<?php echo ( !empty( $options['checkness']['file_delete'] ) ? $options['checkness']['file_delete'] : 'Delete' ); ?> " + this.title + " ?"), listing, store = [], count = $(".wooccm_each_file").each(function(){});
			if (c==true) {

				$("#billing_<?php echo $btn['cow']; ?>_field").block({ message: null, overlayCSS: { background: '#fff no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

				for(var t = 0; t < count.length; t++) {
					if( count[t].getAttribute("wooccm-attach-id") ){
						break;
					}
					store.push(count[t]);
				}

				if( store.length !== 0 ){
					listing = document.getElementById("billing_<?php echo $btn['cow']; ?>").value.split("||");
					listing = listing[1].split(",");
					listing = listing.filter(Number);

					$.each(listing, function(index, value){
						$(store[index]).attr("wooccm-attach-id", value);
					});
				}

				var currentname = this.title, wooccmeachfile = document.getElementsByClassName("wooccm_each_file"), remove, shell;
				formdata = new FormData();

				for( i = 0; i < wooccmeachfile.length; i++ ){
					if(wooccmeachfile[i].title === currentname){
						remove = wooccmeachfile[i].getAttribute("wooccm-attach-id");
						shell = wooccmeachfile[i];
					}
				}

				$.ajax({
					url: "<?php echo admin_url('/admin-ajax.php?action=wooccm_front_enduploadsave&remove='); ?>" + remove,
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					success: function (res) {
						remove = remove + ",";
						$("#billing_<?php echo $btn['cow']; ?>").val(function(index, value) {
							return value.replace(remove, "");
						});

						shell.outerHTML = "";

						$("#billing_<?php echo $btn['cow']; ?>_field").unblock();
					}
				});

			}

		});

		$("#caman_content #wooccmtoolbar #close").click( function(){
			var li = document.getElementById( "wooccmactualimage" );
			li.outerHTML = "";
			$("#caman_content .FilterValue").each(function(){
				$(this).text("0");
			});
			$("#caman_content .FilterSetting input").each(function(){
				$(this).val("0");
			});
			$("#PresetFilters a").each(function(){
				$(this).removeClass("Active");
			});
			$("#caman_content").hide();
			$("html").css("overflow", "visible");
		});

		$(document).on('click','.wooccm_edit',function(){
			return false;
		});

		$(document).on('click','.wooccm_edit.enable',function(){
			var imageinbase = this.href,
			li = document.getElementById( "wooccmimageeditorpro" ),
			data = document.createAttribute("data-caman-hidpi"),
			img  = document.createElement("img");
			data.value = imageinbase;
			$("#caman_content #wooccmtoolbar #save").attr("title", this.title);
			if( $(".wooccmimageeditor img").length == 0 ){
				img.id = "wooccmactualimage";
				img.src = imageinbase;
				img.setAttributeNode(data);
				li.appendChild(img);
				$.getScript( "<?php echo plugins_url( 'includes/pickers/caman/dist/caman_controls.js', WOOCCM_RELPATH ); ?>");
			}
			$("#caman_content").show();
			$("html").css("overflow", "hidden");
		});

	}());

});
</script>
<?php
	}

}
?>
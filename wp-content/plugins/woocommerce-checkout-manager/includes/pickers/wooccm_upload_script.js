<?php
/**
 * WooCommerce Checkout Manager
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function up_wooccmdrive(){
?>

<script type="text/javascript">
jQuery(document).ready(function($){
	$("#' . esc_attr( $key ) . '_field").magnificPopup({
  delegate: "a", // child items selector, by clicking on it popup will open
  type: "image",
  zoom: {
            enabled: true,
            duration: 400,
			easing: "ease-out"
   }
});

(function post_image_content() {
var input = document.getElementById("' . esc_attr( $key ) . '"),
    formdata = false, storenames = [];  

$("#' . esc_attr( $key ) . '_files_button_wccm").click( function(){
$("#' . esc_attr( $key ) . '_field input[type=file]").click();
return false;
});

if (window.FormData) {
    formdata = new FormData();
}

function showUploadedItem ( source, getname, filetype ) {
													var 
													list = document.getElementById("' . esc_attr( $key ) . '_field"),
													li   = document.createElement("span"),
													name   = document.createElement("name"),
													span   = document.createElement("span"),
													zoom   = document.createElement("a"),
													edit   = document.createElement("buttona"),
													dele   = document.createElement("buttona"),
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
																			zoom.href = source;
																			zoom.title = getname;
																			zoom.innerHTML = "Zoom <img style=display:none />";
																			li.appendChild(a);
																			a.appendChild(img);
																			a.className = "wooccm-image-holder mfp-zoom";
																			zoom.className = "wooccm_zoom wooccm-btn wooccm-btn-zoom";
																			edit.className = "wooccm_edit wooccm-btn wooccm-btn-edit";
																	}else{
																		zoom.innerHTML = "Zoom";
																		li.appendChild(spana);
																		spana.appendChild(img);
																		spana.className = "wooccm-image-holder";
																		zoom.className = "wooccm_zoom wooccm-btn disable";
																		edit.className = "wooccm_edit wooccm-btn disable";
																	}
																if ( ( false === filetype.match("application/ms.*") && false === filetype.match("application/x.*") && false === filetype.match("audio.*") && false === filetype.match("text.*") && false === filetype.match("video.*") ) || ( 0 === filetype.length || !filetype) ) {
																		  img.src = "'.site_url('wp-includes/images/media/interactive.png').'";
																	}
																if (filetype.match("application/ms.*")) {
																		  img.src = "'.site_url('wp-includes/images/media/spreadsheet.png').'";
																	}
																if (filetype.match("application/x.*")) {
																		  img.src = "'.site_url('wp-includes/images/media/archive.png').'";
																	}
																if (filetype.match("audio.*")) {
																		  img.src = "'.site_url('wp-includes/images/media/audio.png').'";
																	}
																if (filetype.match("text.*")) {
																		  img.src = "'.site_url('wp-includes/images/media/text.png').'";
																	}
																if (filetype.match("video.*")) {
																		  img.src = "'.site_url('wp-includes/images/media/video.png').'";
																	}
																	
													li.title = getname;
													dele.title = getname;
													li.appendChild(name);
													li.appendChild(span);
													span.appendChild(zoom);
													span.appendChild(edit);
													span.appendChild(dele);
													list.appendChild(li);
													li.className = "wooccm_each_file";
													name.className = "wooccm_name";
													dele.className = "wooccm_dele wooccm-btn wooccm-btn-danger";

													
$("span.wooccm_dele[title='"+ getname + "']").click( function() {
	storenames.push( getname );	
});		
	
					
									}
												

input.addEventListener("change", function (evt) {
    var img, reader, file, iname, len = this.files.length, loadfiles = [].slice.call( this.files );

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
    }
	
					console.dir(storenames);
				
}, false);
}());
});
</script>
<?php
}
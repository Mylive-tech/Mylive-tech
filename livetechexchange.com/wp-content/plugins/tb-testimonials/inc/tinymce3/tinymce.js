function init() {
    tinyMCEPopup.resizeToInnerSize();
}

function insert_testimonial( post_type ) {

    var tag;

    if( is_listing() )
    {
        tag = "[" + post_type;

        tag += " id='" + jQuery( '#what-to-output option:selected' ).val() + "'";
        tag += " template='" + jQuery( '#output-template option:selected' ).val() + "'";

        if( jQuery( '#category option:selected').val() != 'all' )
            tag += " cat='" + jQuery( '#category option:selected').val() + "'";

        tag += " order='" + jQuery( '#order option:selected').val() + "'";
        tag += " orderby='" + jQuery( '#orderby option:selected').val() + "'";

        tag += "]";
    }
    else
    {
       tag = "[" + post_type;

       tag += " id='" + jQuery( '#what-to-output option:selected' ).val() + "'";
       tag += " template='" + jQuery( '#output-template option:selected' ).val() + "'";

       tag += "]";
    }

    if(window.tinyMCE) {
        //TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
        window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tag );
        //Peforms a clean up of the current editor HTML.
        //tinyMCEPopup.editor.execCommand('mceCleanup');
        //Repaints the editor. Sometimes the browser has graphic glitches.
        tinyMCEPopup.editor.execCommand('mceRepaint');
        tinyMCEPopup.close();
    }
    return false;
}

jQuery( function($){
   $('#what-to-output').change(function(){ maybe_hide_listing_options(); });
});

function maybe_hide_listing_options()
{
    if( ! is_listing() )
        hide_listing_options();
    else
        show_listing_options();

    resize_shortcode_gen_window
}

function hide_listing_options(){
    jQuery( '#listing-categories, #listing-options' ).fadeOut( 'fast', function(){
        jQuery( '#no-option-notice' ).fadeIn( 'fast' ).show();
    });
}

function show_listing_options(){
    jQuery( '#listing-categories, #listing-options' ).fadeIn( 'fast', function(){
        jQuery( '#no-option-notice' ).fadeOut( 'fast' ).hide();
    });
}

function is_listing(){
    return jQuery( '#what-to-output').val() == 'all' ? true : false;
}

function resize_shortcode_gen_window(){
    console.log( jQuery('body#tbt-shortcode-generator'));
    jQuery(document).parent().css('height', jQuery('body#tbt-shortcode-generator').height() );
}
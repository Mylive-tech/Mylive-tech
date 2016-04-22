jQuery('document').ready(function($){
    if( $('#wc-pos-register-data').length ){

        $('#edit_wc_pos_registers').unbind('submit').submit(function() {
            if(note_request == 1 && !submit_f){
                $('#overlay_order_comments').show();
                submit_f = true;
                
                return false;
            }

            if( !$('#pos_c_user_id').length && $('#billing_email').val() == '' && $('#pos_sent_email_receipt').val() == 'yes'){
                var email = prompt(wc_pos_params.i18n_ereceipt, '');
                if(email != '' && email != null){
                    var data = 'billing_email='+email;
                    
                    if($('input[name="customer_details"]').length){
                        $('input[name="customer_details"]').val(data);
                    }
                    else{
                        
                        $('#wc-pos-customer-data #customer_items_list tr td.name').append('<input type="hidden" name="customer_details" value="'+ data +'" />');
                    }

                    
                }
            }
            

            var $form = $( this );
            
            var $wpcontent = $( '#wpcontent' );

            if ( $wpcontent.is( '.processing' ) ) {
                return false;
            }

            $wpcontent.addClass( 'processing' );

            var block_data = $wpcontent.data();

            if ( block_data["blockUI.isBlocked"] != 1 ) {
                $wpcontent.block({message: null, overlayCSS: {background: '#fff url(' + wc_pos_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
            }

            $.ajax({
                type:       'POST',
                url:        wc_pos_params.ajax_url+'?action=wc_pos_checkout',
                data:       $form.serialize(),
                success:    function( code ) {
                    var result = '';

                    try {
                        // Get the valid JSON only from the returned string
                        if ( code.indexOf( '<!--WC_START-->' ) >= 0 )
                            code = code.split( '<!--WC_START-->' )[1]; // Strip off before after WC_START

                        if ( code.indexOf( '<!--WC_END-->' ) >= 0 )
                            code = code.split( '<!--WC_END-->' )[0]; // Strip off anything after WC_END

                        // Parse
                        result = $.parseJSON( code );

                        if ( result.result === 'success' ) {
                            
                            if(typeof result.redirect != 'undefined'){
                                if ( result.redirect.indexOf( "https://" ) != -1 || result.redirect.indexOf( "http://" ) != -1 ) {
                                    window.location = result.redirect;
                                } else {
                                    window.location = decodeURI( result.redirect );
                                }
                            }else{
                                
                                wc_pos_reload_register(result.new_order_id);
                                $(result.messages).find('li').each(function(index, el) {
                                    toastr.success($(el).html());
                                });
                                ion.sound.play("succesful_order");

                                if (typeof result.print_receipt != 'undefined'){
                                    $('#printing_receipt').show();
                                    if(typeof wc_pos_change_user == 'undefined'){
                                        $wpcontent.removeClass( 'processing' ).unblock();
                                    }
                                    wc_pos_printing_receipt(result.print_receipt);
                                }else if(typeof wc_pos_change_user != 'undefined'){
                                    afterPrint();
                                }
                            }
                        } else if ( result.result === 'failure' ) {
                            
                            if( typeof result.messages != 'undefined'){
                                toastr.error(result.messages);
                            }else{
                                toastr.error('Result failure');    
                            }
                            $wpcontent.unblock();
                            $form.unblock();
                            ion.sound.play("error");
                            throw 'Result failure';
                        } else {
                            toastr.error('Invalid response');
                            ion.sound.play("error");
                            throw 'Invalid response';
                        }
                    }

                    catch( err ) {

                        if ( result.reload === 'true' ) {
                            window.location.reload();
                            return;
                        }

                        // Add new errors
                        if ( result.messages ) {
                            //$form.prepend( result.messages );
                            if(result.result == 'failure'){
                                $(result.messages).find('li').each(function(index, el) {
                                    toastr.error($(el).html());
                                });
                                ion.sound.play("error");
                            }else if(result.result == 'success'){
                                $(result.messages).find('li').each(function(index, el) {
                                    toastr.success($(el).html());
                                });
                                ion.sound.play("succesful_order");
                            }
                        } else {
                            //$form.prepend( code );
                            console.log(code);
                        }

                        
                    }
                },
                dataType: 'html'
            }).always(function() {
                if(typeof wc_pos_change_user == 'undefined'){
                    $wpcontent.removeClass( 'processing' ).unblock();
                }
                $form.unblock();
                
                $form.find( '.input-text, select' ).blur();
                show_p = false;
                submit_f = false;
            });
            return false;
        });
    }
    if(typeof wc_pos_change_user != 'undefined'){
        function afterPrint() {
            window.location.href= wc_pos_change_user;
        }
        window.onafterprint =  afterPrint;
        if ('matchMedia' in window) {
            window.matchMedia('print').addListener(function(media) {
                if (!media.matches) {
                    $(document).one('mouseover', afterPrint );
                }
            });
        }

    }
    $('.wc_pos_register_void').on('click', function() {
            if (confirm(wc_pos_params.void_register_notice)) {
                $('#post-body').block({message: null, overlayCSS: {background: '#fff url(' + wc_pos_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
                    wc_pos_register_void();
                    var order_id   = $('#order_id').val();
                    var register_id = $('#id_register').val();
                    $.ajax({
                        type: 'POST',
                        url: wc_pos_params.ajax_url,
                        data: {
                            action      : 'wc_pos_void_register',
                            security    : wc_pos_params.void_register_nonce,
                            order_id    : order_id,
                            register_id : register_id,
                        },
                        success: function(response) {
                            if (response) {
                                response = $.parseJSON( response );
                                if(response.result == 'ok'){
                                    $('#order_id').val(response.order_id);
                                }
                            }
                        },
                        
                    })
                    .always( function(response) {
                        $('#post-body').unblock();
                    });
            }
        });
    function wc_pos_reload_register(order_id){     
     wc_pos_register_void();
     $('#order_id').val(order_id);
     $('.stripe_token').remove();
     $('#order_payment_popup input.select_payment_method').attr('disabled', 'disabled');
     $('#overlay_order_payment').hide();
     $('#order_payment_popup input[type=text], #order_payment_popup input[type=number], #order_payment_popup select').val('');
     $('#sync_data').trigger('click');
    }
    function wc_pos_register_void(){
        $('#order_discount_prev').val('');
        $('#order_comments').val('');
        $('#wc-pos-register-data #order_items_list').html('');
        $('#customer_items_list').html(default_customer);
        
        $('#customer_items_list .tips').tipTip({
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            });
        $('#add_wc_pos_customer')[0].reset();
        
        $('tr.shipping_methods_register th, tr.shipping_methods_register td').html('');
        $('tr.shipping_methods_register').hide();

        clearCoupons();
        POScalculateTotal();
    }
    function wc_pos_printing_receipt(order_id){
        var data = {
            action    : 'wc_pos_printing_receipt',
            security  : wc_pos_params.printing_receipt_nonce,
            order_id  : order_id,
            receipt_ID: $('#print_receipt_ID').val(),
            outlet_ID : $('#outlet_ID').val(),
            register_ID : $('#id_register').val(),
        };
        var start_print    = false;
        var print_document ='';
        $.ajax({
            type: 'POST',
            async: false,
            url: wc_pos_params.ajax_url,
            data: data,
            success: function(response) {
                if (response) {
                    start_print    = true;
                    print_document = response;
                }
            }
        });

        if(start_print){
            if($('#printable').length)
                $('#printable').remove();
            var newHTML = $('<div id="printable">'+print_document+'</div>');
            delete_cookie('wc_point_of_sale_printing');
            $('body').append(newHTML);
            if( $('#print_barcode img').length ){
                var src = $('#print_barcode img').attr('src');
                if(src != ''){
                    $("<img>").load(function() {
                        window.print();
                        $('#printing_receipt').hide();
                    }).attr('src', src);
                }else{
                    window.print();
                    $('#printing_receipt').hide();    
                }
            }
            else if( $('#print_receipt_logo').length ){
                var src = $('#print_receipt_logo').attr('src');
                if(src != ''){
                    $("<img>").load(function() {
                        window.print();
                        $('#printing_receipt').hide();
                    }).attr('src', src);
                }else{
                    window.print();
                    $('#printing_receipt').hide();    
                }
            }
            else{
                window.print();
                $('#printing_receipt').hide();
            }
        }
    }

});
    
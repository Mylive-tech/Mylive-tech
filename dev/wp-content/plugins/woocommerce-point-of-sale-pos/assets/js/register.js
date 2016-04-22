/* Default Guest Customer Loading */
var store_data        = [];
var default_customer  = '';
var default_guest     = '';

var submit_f = false;
var notes = false;

var runKeypad, POScalculateTotal,  runVariantion, find_matching_variations, variations_match, wc_coupons, clearCoupons;




jQuery(document).ready(function($) {

/* change the state according country */
$(document).on('change', '#billing_country, #shipping_country', function() {

  var country = $(this).val();
  var id = $(this).attr('id').replace('_countries', '');

  var data = {
      action: 'wc_pos_loading_states',
      country: country,
      id: id
  };

  var billing_state  = $('#billing_state').val();
  
  var shipping_state = $('#shipping_state').val();
  xhr = $.ajax({
      type: 'POST',
      url: wc_pos_params.ajax_url,
      data: data,
      beforeSend: function(xhr) {
          $('#order_customer_popup').block({
            message: null,
            overlayCSS: {
              background: '#fff',
              opacity: 0.6
            }
          });
      },
      complete: function(xhr) {
          if($(id == 'billing_country' && 'select#billing_state').length > 0){
              if( $().select2 ){
                  $('select#billing_state').select2();
              }else{
                  $('select#billing_state').chosen();
              }                    
          }                

          if($(id == 'shipping_country' && 'select#shipping_state').length > 0){
              if( $().select2 ){
                  $('select#shipping_state').select2(); 
              }else{
                  $('select#shipping_state').chosen(); 
              }
          }                

          $('#order_customer_popup').unblock()
      },
      success: function(response) {
          var j_data = JSON.parse(response);
          var html = $($.parseHTML($.trim(j_data.state_html)));
          if(id == 'billing_country'){
              $('#billing_state').remove();
              if($('#billing_state_chosen').length > 0){
                  $('#billing_state_chosen').remove();
              }
              if($('#s2id_billing_state').length > 0){
                  $('#s2id_billing_state').remove();
              }
              $('label[for="billing_state"]').after($(html));
              $('label[for="billing_state"]').html(j_data.state_label + ' <span class="required">*</span>');
              $('label[for="billing_postcode"]').html(j_data.zip_label + ' <span class="required">*</span>');
              $('label[for="billing_city"]').html(j_data.city_label + ' <span class="required">*</span>');
              $('#billing_state').val(billing_state);
          }
          if(id == 'shipping_country'){
              $('#shipping_state').remove();
              if($('#shipping_state_chosen').length > 0){
                  $('#shipping_state_chosen').remove();
              }
              if($('#s2id_shipping_state').length > 0){
                  $('#s2id_shipping_state').remove();
              }
              $('label[for="shipping_state"]').after($(html));
              $('label[for="shipping_state"]').html(j_data.state_label + ' <span class="required">*</span>');
              $('label[for="shipping_postcode"]').html(j_data.zip_label + ' <span class="required">*</span>');
              $('label[for="shipping_city"]').html(j_data.city_label + ' <span class="required">*</span>');
              $('#shipping_state').val(shipping_state);
          }
      }
  });
});

  $('#wc-pos-register-data').on('click', '.add_custom_meta', function() {
    $('#product_custom_meta_table tbody').html('');
    var tr     = '';
    var pr_id  = $(this).closest('tr').attr('id');    

    $('#add_custom_meta_product_id').val(pr_id);
    $(this).closest('tr').find('.pos_pr_variations').each(function(index, el) {
        var attrlabel = $(el).data('attrlabel');
        var metaval   = $(el).val();
        tr += '<tr>\
                    <td class="meta_label"><input type="text" class="meta_label_value" value="'+attrlabel+'"></td>\
                    <td class="meta_attribute"><input type="text"  class="meta_attribute_value" value="'+metaval+'"></td>\
                    <td class="remove_meta"><span href="#" class="remove_custom_product_meta tips"></span></td>\
                </tr>';
    });
    if(tr == ''){
        tr = '<tr>\
                    <td class="meta_label"><input type="text" class="meta_label_value"></td>\
                    <td class="meta_attribute"><input type="text"  class="meta_attribute_value" ></td>\
                    <td class="remove_meta"><span href="#" class="remove_custom_product_meta tips"></span></td>\
                </tr>';
    }
    $('#product_custom_meta_table tbody').append(tr);

    $('#add_product_custom_meta_overlay_popup').show();
    return false;
  });
  
  $('#save_product_custom_meta').click(function() {
      var pr_id = $('#add_custom_meta_product_id').val();

      var attr = '';
      $('#product_custom_meta_table tbody tr').each(function(index, el) {
        
          var attrlabel = $(el).find('.meta_label_value').val();
          var metaval   = $(el).find('.meta_attribute_value').val();
          if(attrlabel != '' && metaval != '')
              attr +='<li class="pos_pr_variations_li"><span class="meta_label">'+attrlabel+'</span><span class="meta_value"><p>'+metaval+'</p><input type="hidden" data-attrlabel="'+attrlabel+'" value="'+metaval+'" name="variations['+pr_id+']['+attrlabel+']" class="pos_pr_variations"></span></li>';
      });

      var $ul = $('tr#'+pr_id).find('ul.display_meta');
      
      $ul.find('.pos_pr_variations_li').remove();
      $ul.append(attr);
      $('#add_product_custom_meta_overlay_popup').hide();
      $('#product_custom_meta_table tbody').html('');
      return false;
  });

  $('#add_product_custom_meta_overlay_popup').on('click', '.remove_custom_product_meta', function() {
      $(this).closest('tr').remove();
      var count = $('#product_custom_meta_table tbody tr').length;
      if(!count){
          var html = '<tr>\
                      <td class="meta_label"><input type="text" class="meta_label_value"></td>\
                      <td class="meta_attribute"><input type="text"  class="meta_attribute_value" ></td>\
                      <td class="remove_meta"><span href="#" class="remove_custom_product_meta tips"></span></td>\
                  </tr>';
          $('#product_custom_meta_table tbody').append(html);
      }
      return false;
  });

  $('#add_product_custom_meta').click(function() {
      var html = '<tr>\
                      <td class="meta_label"><input type="text" class="meta_label_value"></td>\
                      <td class="meta_attribute"><input type="text"  class="meta_attribute_value" ></td>\
                      <td class="remove_meta"><span href="#" class="remove_custom_product_meta tips"></span></td>\
                  </tr>';
      $('#product_custom_meta_table tbody').append(html);
  });

  $('body').on('click', function(evt) {

      if($(this).hasClass('var_tip_over') && !$(evt.target).closest('#var_tip').length && !$(evt.target).is('#var_tip') ){
          $('body').removeClass('var_tip_over');
          $('#var_tip').remove();
          $('td.hover').removeClass('hover');
      }
  });

  if( $().select2 ){
        jQuery('select#billing_country').select2();
        jQuery('select#shipping_country').select2();        
        if(jQuery('select#billing_state').length > 0){
            jQuery('select#billing_state').select2(); 
        }
        if(jQuery('select#shipping_state').length > 0){
            jQuery('select#shipping_state').select2(); 
        }
        jQuery('select.ajax_chosen_select_customer, #_register_default_customer').each(function() {
            var v,t;
            $(this).find('option:selected').each(function(index, el) {
                v = $(el).val();
                t = $(el).text();
            });
            var _id = $(this).attr('id');
            var _class = $(this).attr('class');
            var _name = $(this).attr('name');
            $(this).replaceWith('<input type="text" id="'+_id+'" class="'+_class+'" name="'+_name+'" />');
            $('input#'+_id).select2({
                allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                placeholder: $( this ).data( 'placeholder' ) ? $( this ).data( 'placeholder' ) : 'Search a customer',
                minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         wc_pos_params.ajax_url,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term    : term,
                            action  : 'wc_pos_json_search_customers',
                            security: wc_pos_params.search_customers
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                                    $.each( data, function( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    });
                                }
                      return { results: terms };
                    },
                    cache: true
                },
            });
            if(typeof v != 'undefined'){
                var preselect = {id: v, text: t};
                $('input#'+_id).select2('data', preselect);
            }
            
        });
        
    }else{
        jQuery('select#billing_country').chosen();
        jQuery('select#shipping_country').chosen();        
        if(jQuery('select#billing_state').length > 0){
            jQuery('select#billing_state').chosen(); 
        }
        if(jQuery('select#shipping_state').length > 0){
            jQuery('select#shipping_state').chosen(); 
        }        
        jQuery('select.ajax_chosen_select_customer, #_register_default_customer').ajaxChosen({
                method        : 'GET',
                url           : wc_pos_params.ajax_url,
                dataType      : 'json',
                afterTypeDelay: 100,
                minTermLength :  1,
                data:       {
                    action   : 'wc_pos_json_search_customers',
                    security : wc_pos_params.search_customers
                }
            }, function (data) {
                var terms = {};
                $.each(data, function (i, val) {
                    terms[i] = val;
                });
                return terms;
            });
    }
    if ($('#order_payment_popup').length > 0 ){
      $('.overlay_order_popup .media-menu a').click( function() {
          $parent = $(this).closest('.overlay_order_popup');
          $parent.find('.media-menu a').removeClass('active')
          $(this).addClass('active');
          var id  = $(this).attr('href');
          var txt = $(this).text();
          $parent.find('.media-frame-title h1').text(txt);
          $parent.find('.popup_section').hide();
          $(id).show();
          $('#coupon_tab div.messages').html('');
          if($(this).hasClass('payment_methods')){
            var selected_payment_method = $(this).data('bind');
            $('#payment_method_' + selected_payment_method).attr('checked', 'checked');
          }
          return false;
      });
      $('.wc_pos_register_pay').on('click', function() {
          if(note_request == 2 && !submit_f){
              $('#overlay_order_comments').show();
          }else{
              submit_f = true;
              $('#order_payment_popup input.select_payment_method').removeAttr('disabled');
              $('#overlay_order_payment').show();
              $('#order_payment_popup .media-menu a').first().click();

              if( !$('#payment_switch_wrap .bootstrap-switch-container').length)
                  $('.payment_switch').bootstrapSwitch();

              if( $('#pos_chip_pin_order_id').length ){
                var chip_pin_order_id = String($('#order_id').val());
                    chip_pin_order_id = pos_register_data.detail.prefix + chip_pin_order_id + pos_register_data.detail.suffix;
                $('#pos_chip_pin_order_id').text(chip_pin_order_id);
              }
          }
      });
    }
    $('.wc_pos_register_notes').on('click', function() {
        $('#overlay_order_comments').show();
        notes = true;
    });
     $('#save_order_comments').on('click', function() {
        if(note_request == 1 && !notes){
            submit_f = true;
            $('#edit_wc_pos_registers').submit();
        }else if(note_request == 2){
            submit_f = true;
            $('.wc_pos_register_pay').click();
        }
        notes = false;
        $('#overlay_order_comments').hide();
        $('#order_comments_error').hide();
    });

/******************************
 *********** Coupons **********
 ******************************/
$('#coupon_code').on('keypress', function(e) {
  var code = e.keyCode || e.which;
   if(code == 13) {
     applyCoupon();
     return false;
   }
});
$('#apply_coupon_btn').on('click', function(e) {
  applyCoupon();
});

$('body').on('click', '.span_clear_order_coupon', function(e) {
  var code = $(this).closest('tr.tr_order_coupon').find('.order_coupon_amount').val();        
  clearCoupons(code);
  POScalculateTotal();
});


/**********END COUPONS*************/

$('#btn_retrieve_from').click(function(){    
    retrieve_sales();
    return false;
});
$('#orders-search-input').keypress(function(event) {
  if ( event.which == 13 ) {
    retrieve_sales();
    return false;
  }  
});

$('#orders-search-submit').click(function(){
    retrieve_sales();
    return false;
});

$('#add_shipping_to_register').click(function(event) {
  $('#add_shipping_overlay_popup .box_content input').removeClass('error');
  $('#add_shipping_overlay_popup .custom-shipping-fields .validate-required input').removeClass('error');
  $('#add_shipping_overlay_popup .custom-shipping-fields .validate-required select').removeClass('error');
  if($('#pos_c_shipping_addr').length){
    var s_data = $('#pos_c_shipping_addr').val();
    if(s_data != ''){
      s_data = JSON.parse(s_data);
      $.each(s_data, function(index, value) {
        $('#custom_shipping_'+index).val( value );
      });
    }
  }
});
$('body').on('click', '.shopopup', function(event) {
  var modal = $(this).data('modal');
  $('#'+modal).show();
  return false;
});

$('#add_shipping_overlay_popup').on('keypress', 'input[type="text"], input[type="number"]', function(e){
    if (e.which == 13) {
        $('#add_custom_shipping').click();
    }
});
$('#add_custom_shipping').click(function(event) {
  var err = 0;
    $('#add_shipping_overlay_popup .box_content input').removeClass('error').each(function() {
        
        if($(this).val() == ''){
            $(this).addClass('error');
            err++;
        }
    });

    $('#add_shipping_overlay_popup .custom-shipping-fields .validate-required input').removeClass('error');
    $('#add_shipping_overlay_popup .custom-shipping-fields .validate-required select').removeClass('error');

    $('#add_shipping_overlay_popup .custom-shipping-fields .validate-required').each(function() {
        $(this).find('input:not(".select2-offscreen, .select2-input"), select, textarea').each(function(index, el) {
            if($(el).val() == ''){
                $(el).addClass('error');
                err++;
            }                
        });
    });
    $('#add_shipping_overlay_popup .custom-shipping-fields .validate-email').each(function() {
        var f = $(this).find('input');
        if ( !checkEmail( f.val() ) ) {
            f.addClass('error');
            err++;
        }
    });
    $('#add_shipping_overlay_popup .custom-shipping-fields .validate-phone').each(function() {
        var f = $(this).find('input');
        if ( !checkPhone( f.val() ) ) {
            f.addClass('error');
            err++;
        }
    });
    if(err > 0)
      return false;

    if( $('#customer_details_id').val() != ''){
      var user_id = $('#customer_details_id').val();
      $('#custom_customer_details_id').val(user_id);
      var data = {
          action: 'wc_pos_update_customer_shipping_address',
          form_data: $('#update_customer_shipping_fields').serialize()
      };


      xhr = $.ajax({
          type: 'POST',
          url: wc_pos_params.ajax_url,
          data: data,
          beforeSend: function(xhr) {
              $('#wc-pos-customer-data').block({
                message: null,
                overlayCSS: {
                  background: '#fff',
                  opacity: 0.6
                }
              });
          },
          complete: function(xhr) {
              $('#wc-pos-customer-data').unblock();
          },
          success: function(response) {
              var j_data;
              try{
                  // Get the valid JSON only from the returned string
                  if ( response.indexOf( '<!--WC_POS_START-->' ) >= 0 )
                      response = response.split( '<!--WC_POS_START-->' )[1]; // Strip off before after WC_POS_START

                  if ( response.indexOf( '<!--WC_POS_END-->' ) >= 0 )
                      response = response.split( '<!--WC_POS_END-->' )[0]; // Strip off anything after WC_POS_START

                  // Parse
                  j_data = JSON.parse(response);
                  
                  if (j_data.success == false) {
                  } else {
                      $('#pos_c_shipping_addr').val(JSON.stringify(j_data.s_addr));
                      $('#add_wc_pos_customer')[0].reset();
                      $('#customer_details_id').val('');
                      $('#custom_customer_details_id').val('');
                  }
                  
                      

              }catch(e){
                  console.log(e);
              }
                              
          }
      });
    }else{
      var country    = $('#custom_shipping_country').val();
      var first_name = $('#custom_shipping_first_name').val();
      var last_name  = $('#custom_shipping_last_name').val();
      var company    = $('#custom_shipping_company').val();
      var address_1  = $('#custom_shipping_address_1').val();
      var address_2  = $('#custom_shipping_address_2').val();
      var city       = $('#custom_shipping_city').val();
      var state      = $('#custom_shipping_state').val();
      var postcode   = $('#custom_shipping_postcode').val();

      $('#shipping_country').val(country);
      $('#shipping_first_name').val(first_name);
      $('#shipping_last_name').val(last_name);
      $('#shipping_company').val(company);
      $('#shipping_address_1').val(address_1);
      $('#shipping_address_2').val(address_2);
      $('#shipping_city').val(city);
      $('#shipping_state').val(state);
      $('#shipping_postcode').val(postcode);

      if( $('input[name="customer_details"]').length){
        var customer_details = $('#add_wc_pos_customer').serialize();
        $('input[name="customer_details"]').append(customer_details);
      }else{
        var customer_details = '<input type="hidden" name="customer_details" value="'+$('#add_wc_pos_customer').serialize()+'" />';
        $('td.remove_customer').append(customer_details);
      }
    }
    $('#ship-to-different-address-checkbox').attr('checked', 'checked').trigger('change');
    var title = $('#custom_shipping_title').val();
    var price = $('#custom_shipping_price').val();
    var method = $('#custom_shipping_method').val();
    
    var formatted_price = accounting.formatMoney(price, {
        symbol: wc_pos_params.currency_format_symbol,
        decimal: wc_pos_params.currency_format_decimal_sep,
        thousand: wc_pos_params.currency_format_thousand_sep,
        precision: wc_pos_params.currency_format_num_decimals,
        format: wc_pos_params.currency_format
    });
    var html = '<tr class="shipping_methods_register">\
    <th>\
      <input type="hidden" name="custom_shipping[0]" value="'+title+'" />\
      <input type="hidden" name="custom_shipping[1]" value="'+method+'" />\
      <input type="hidden" name="custom_shipping[2]" value="'+price+'" class="shipping_price" />\
      '+title+'\
    </th>\
    <td><strong>'+formatted_price+'</strong></td></tr>';

    if( $('tr.shipping_methods_register').length ){
      $('tr.shipping_methods_register').replaceWith( html );

    }else if( $('tr.tax_row').length ){
      $('tr.tax_row').before(html);
    }else{
      $('tr#tr_order_total_label').before(html);
    }
    $('tr.shipping_methods_register').show();

    $('#custom_shipping_table input').val('');
    $('#custom_shipping_table select').val('');
    $('#add_shipping_overlay_popup').hide();
    POScalculateTotal();
});

$('#add_custom_product_popup').on('click', '.remove_custom_product_meta', function() {
    $(this).closest('tr').remove();
    var count = $('#custom_product_meta_table tbody tr').length;
    if(!count)
        $('#custom_product_meta_table, #custom_product_meta_label').hide();
});
$('#add_custom_product_popup').on('keypress', 'input[type="text"], input[type="number"]', function(e){
    if (e.which == 13) {
        $('#add_custom_product').click();
    }
});
$('#add_custom_product').click(function() {
    var err = 0;
    $('#add_custom_product_popup .box_content input').removeClass('error').each(function() {
        
        if($(this).val() == ''){
            $(this).addClass('error');
            err++;
        }
    });
    if(err > 0)
        return false;

    var time         = new Date().getTime();
    var option       = {};
    option.title     = $('#custom_product_table input#custom_product_title').val();
    option.price     = $('#custom_product_table input#custom_product_price').val();
    option.quantity  = $('#custom_product_table input#custom_product_quantity').val();
    option.row_class = 'product_id_' + wc_pos_params.custom_pr_id + '_' + time;

    var meta_lines  = $('#custom_product_meta_table tbody tr');
    if(meta_lines.length){
        option.attributes = Array();
        meta_lines.each(function(index, el) {
            option.attributes[index] = {};
            option.attributes[index]['label']     = $('<div></div>').append($(el).find('.meta_label_value').val()).text().trim();
            option.attributes[index]['attribute'] = $('<div></div>').append($(el).find('.meta_attribute_value').val()).text().trim();
            option.attributes[index]['taxonomy']  = option.attributes[index]['label'];
            option.row_class += '_' + option.attributes[index]['attribute'];
        });
    }

    POSaddProduct(option, true);
    $('#custom_product_table input').val('');
    $('#custom_product_meta_table tbody tr').remove();
    $('#custom_product_meta_table, #custom_product_meta_label, #add_custom_product_overlay_popup').hide();
});
$('#add_custom_product_meta').click(function() {
    var html = '<tr>\
                    <td class="meta_label"><input type="text" class="meta_label_value"></td>\
                    <td class="meta_attribute"><input type="text"  class="meta_attribute_value"></td>\
                    <td class="remove_meta"><span href="#" class="remove_custom_product_meta tips"></span></td>\
                </tr>';
    $('#custom_product_meta_table tbody').append(html);
    $('#custom_product_meta_table, #custom_product_meta_label').show();
});

$(".cb-switcher").click(function(){
    var parent = $(this).parents('.switch');
    $('.selected',parent).removeClass('selected');
    $(this).addClass('selected');
});


$('body').on( 'update_variation_values', '#var_tip', function( event, variations ) {
            
  $variation_form = $( this );

  // Loop through selects and disable/enable options based on selections
  $variation_form.find( 'select' ).each( function( index, el ) {
    var current_attr_name, current_attr_select = $( el );

        // Reset options
        if ( ! current_attr_select.data( 'attribute_options' ) ) {
          current_attr_select.data( 'attribute_options', current_attr_select.find( 'option:gt(0)' ).get() );
        }

        current_attr_select.find( 'option:gt(0)' ).remove();
        current_attr_select.append( current_attr_select.data( 'attribute_options' ) );
        current_attr_select.find( 'option:gt(0)' ).removeClass( 'attached' );
        current_attr_select.find( 'option:gt(0)' ).removeClass( 'enabled' );
        current_attr_select.find( 'option:gt(0)' ).removeAttr( 'disabled' );

        // Get name from data-attribute_name, or from input name if it doesn't exist
        if ( typeof( current_attr_select.data( 'attribute_name' ) ) !== 'undefined' ) {
          current_attr_name = current_attr_select.data( 'attribute_name' );
        } else {
          current_attr_name = current_attr_select.attr( 'name' );
        }

        // Loop through variations
        for ( var num in variations ) {

          if ( typeof( variations[ num ] ) !== 'undefined' ) {

            var attributes = variations[ num ].attributes;

            for ( var attr_name in attributes ) {
              if ( attributes.hasOwnProperty( attr_name ) ) {
                var attr_val = attributes[ attr_name ];

                if ( attr_name === current_attr_name ) {

                  var variation_active = '';

                  if ( variations[ num ].variation_is_active ) {
                    variation_active = 'enabled';
                  }

                  if ( attr_val ) {

                    // Decode entities
                    attr_val = $( '<div/>' ).html( attr_val ).text();

                    // Add slashes
                    attr_val = attr_val.replace( /'/g, '\\\'' );
                    attr_val = attr_val.replace( /"/g, '\\\"' );

                    // Compare the meerkat
                    current_attr_select.find( 'option[value="' + attr_val + '"]' ).addClass( 'attached ' + variation_active );

                  } else {

                    current_attr_select.find( 'option:gt(0)' ).addClass( 'attached ' + variation_active );

                  }
                }
              }
            }
          }
        }

        // Detach unattached
        current_attr_select.find( 'option:gt(0):not(.attached)' ).remove();

        // Grey out disabled
        current_attr_select.find( 'option:gt(0):not(.enabled)' ).attr( 'disabled', 'disabled' );
  });

} );


$('body').on( 'check_variations', '#var_tip', function( event, exclude, focus ) {

  var all_attributes_chosen  = true,
      some_attributes_chosen = false,
      current_settings       = {},
      $form                  = $( this ),
      product_id             = parseInt( $form.data( 'id' ) ),
      $product_variations    = $form.data( 'product_variations' );

  $form.find( '#var_tip_content select' ).each( function() {
    var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );

    if ( $( this ).val().length === 0 ) {
      all_attributes_chosen = false;
    } else {
      some_attributes_chosen = true;
    }

    if ( exclude && attribute_name === exclude ) {
      all_attributes_chosen = false;
      current_settings[ attribute_name ] = '';
    } else {
      // Add to settings array
      current_settings[ attribute_name ] = $( this ).val();
    }
  });

  var matching_variations = find_matching_variations( $product_variations, current_settings );
  if ( all_attributes_chosen ) {

    var variation = matching_variations.shift();

    if ( variation ) {
      $form.trigger( 'found_variation', [ variation ] );
    } else {
      // Nothing found - reset fields
      $form.find( '#var_tip_content select' ).val( '' );

      if ( ! focus ) {
        $form.trigger( 'reset_data' );
      }
    }

  } else {

    $form.trigger( 'update_variation_values', [ matching_variations ] );
  }

} );
$('body').on('change', '#var_tip select', function() {
  $form = $( this ).closest( '#var_tip' ); 
  $form.trigger( 'check_variations', [ '', false ] );
  $( this ).blur();

});
$('body').on('focusin touchstart', '#var_tip select', function() {

  $form = $( this ).closest( '#var_tip' ); 
  $form.trigger( 'check_variations', [ $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' ), true ] );
  
});

/***************** FUNCTIONS *******************/
var z = '00';



popupChooseAttributes = function(value, need_attributes){

    if(typeof value.parent_attr == 'string'){
        var product_attributes = jQuery.parseJSON(value.parent_attr);
    }else{
        var product_attributes = value.parent_attr;
    }
    var html = '';
    for(var attr in product_attributes){

        if (product_attributes.hasOwnProperty(attr) ){
            var attribute = product_attributes[attr];

            if(need_attributes[attribute.name]){

                html += '<tr>\
                    <td>'+attribute.name+'</td>\
                <td><select data-label="'+attribute.name+'" data-taxonomy="'+attribute.taxonomy+'">';

                for(var opt in attribute.options){

                    if (attribute.options.hasOwnProperty(opt) ){

                        var options = attribute.options[opt];

                        html += '<option value="'+options.slug+'">'+options.name+'</option>'

                    }
                }

                html += '</select></td></tr>';

            }

        }

    }
    if(html){
        html = '<table>'+html+'</table>';
        $('#popup_choose_attributes_inner').html(html);
        $('#popup_choose_attributes_button').html('<input type="button" value="Add" class="button button-primary" id="add_pr_variantion_popup">');
        $('#popup_choose_attributes_inner').data('id', value.id);
        $('#popup_choose_attributes').show();
    }
    
}
runVariantion = function(el){
    var $ = jQuery;
    var id             = el.find('a').data( 'id');
    var all_variations = el.find('a').data( 'product_variations' );
    var html           = '';
    if(el.find('.hidden').length)
        html           = el.find('.hidden').html();

    $('body').removeClass('var_tip_over');
    $('#var_tip').remove();
    var qt = '';
    if(wc_pos_params.instant_quantity == 'yes'){
        qt = '<div class="qty"><input type="hidden" class="quantity" value=""/><div class="inline_quantity"></div></div>';
    }

    var var_tip = '<div id="var_tip" data-id="'+id+'" ><div id="var_tip_arrow"><div id="var_tip_arrow_inner"></div></div><div id="var_tip_content">'+html+qt+' <input type="button" id="add_pr_variantion" class="button button-primary" value="Add"/></div></div>';
    $('body').append(var_tip);
    $('#var_tip').data('product_variations', all_variations );
    var t = el.offset().top;
    var l = el.offset().left-68;
    var h = el.outerHeight();
    var new_t = t+h+2;

    el.addClass('hover');

    $('#var_tip').css({
        top: new_t,
        left: l
    }).show();
    $('body').addClass('var_tip_over');
    if(wc_pos_params.instant_quantity == 'yes' && isTouchDevice() == false){
        
        if(wc_pos_params.instant_quantity_keypad != 'yes'){
            $('#var_tip').addClass('hide_quantity_keypad');
        }

        $('#var_tip .inline_quantity').keypad({
            keypadOnly: false,
            keypadClass : 'quantity_keypad',
            separator  : '|', 
            layout     : [ $.keypad.MINUS+'|'+$.keypad.INPVAL+'|'+$.keypad.PLUS, '1|2|3|' + $.keypad.CLEAR, '4|5|6|' + $.keypad.BACK, '7|8|9|' + $.keypad.CLOSE, '0|.|00'],
            closeText  : '',
            plusText   : '+',
            minusText  : '-',
            inpvalText  : '0',
            minusStatus: 'Minus', 
            plusStatus : 'Plus', 
            inpvalStatus : 'Quantity', 
            prompt     : 'Quantity',
            onKeypress : function(key, value, inst) { 
                var val = inputQuantityVal(value);
                $('#var_tip input.quantity').val(val);
                $('.quantity_keypad .keypad-inpval').text(val);
            },
            beforeShow : function(div, inst) {
                var val = $('#var_tip input.quantity').val();
                $(inst.elem).find('input.keypad-keyentry').val(val);
                if(val == '') val = 1;
                $('.quantity_keypad .keypad-inpval').text(val);                        
            },
            onClose: function(value, inst) { 
                $('#add_pr_variantion').trigger('click');
            }
        }).keydown(function(ev){
            $('.keypad-popup').hide();
            if ( ev.which == 13 ) {
                ev.preventDefault();
            }
            var val = inputQuantityVal(this.value);
            $(this).val(val);
        });
    }
}
find_matching_variations = function( product_variations, settings ) {
    var matching = [];

    for ( var i = 0; i < product_variations.length; i++ ) {
        var variation = product_variations[i];
        var variation_id = variation.variation_id;

        if ( variations_match( variation.attributes, settings ) ) {
            matching.push( variation );
        }
    }

    return matching;
};
variations_match = function( attrs1, attrs2 ) {
    var match = true;

    for ( var attr_name in attrs1 ) {
        if ( attrs1.hasOwnProperty( attr_name ) ) {
            var val1 = attrs1[ attr_name ];
            var val2 = attrs2[ attr_name ];

            if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
                match = false;
            }
        }
    }

    return match;
};

if( jQuery('.post-locked-message.not_close').length == 0 && $('#edit_wc_pos_registers').length > 0){
    if (typeof indexedDB != 'undefined' || window.openDatabase) {
      jQuery('#add_product_id').select2({
                    minimumInputLength: 3,
                    multiple: true,
                    query: function (query) {
                      var term = query.term;   
                          term = term.toLowerCase();

                      var result = $.grep(store_data, function(e){
                        var text = e.text;
                            text = text.toLowerCase();
                         return text.indexOf(term) >= 0;
                      });

                      var data = {results: []};
                      data.results = result;
                      
                      query.callback(data);
                    }
                });
        openDb();
    }
    $('#edit_wc_pos_registers').on('input', 'input.quantity', function(){
        inputQuantityVal($(this));
    });
    $('#custom_product_quantity, #custom_shipping_price').on('input', function(){
        inputQuantityVal($(this));
    })
}


function retrieve_sales(){
    var id_register = $('#bulk-action-selector-top').val();
    var curent_id   = $('#id_register').val(); //retrieve_sales_popup_title
    var name        = $('#bulk-action-selector-top').find(":selected").data('name');
    var term = $('#orders-search-input').val();

    if(id_register == ''){
      return false;
    }
    $('body, #retrieve_sales_popup').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
    $('#retrieve_sales_popup_title i').html(name);
    if(typeof term == 'undefined')
      term = '';
    var data = {
        action: 'wc_pos_load_pending_orders',
        security    : wc_pos_params.load_pending_orders,
        register_id : id_register,
        term        : term,
        order_id    : $('#order_id').val()
    };
    $.ajax({
        type: 'POST',
        url: wc_pos_params.ajax_url,
        data: data,
        success: function(response) {
            if (response) {
                $('#retrieve_sales_popup_inner').html(response);
                $('#overlay_retrieve_sales').show();
                var tb_h = $('#retrieve_sales_popup_content_scroll table.orders').height();
                var ct_h = $('#retrieve_sales_popup_content_scroll').height();
                if(tb_h < ct_h){
                    $('#retrieve_sales_popup').css({
                        'bottom' : 'auto'
                    })
                }
                $('#overlay_retrieve_sales .tips').tipTip({
                    'attribute': 'data-tip',
                    'fadeIn': 50,
                    'fadeOut': 50,
                    'delay': 200
                });
                $('#overlay_retrieve_sales .show_order_items').click(function() {
                    $(this).closest('td').find('table').toggle();
                    return false;
                });
                $('body, #retrieve_sales_popup').unblock();
            }
        }
    });
}
if (typeof Stripe == 'function' && typeof wc_stripe_params != 'undefined') { 
  Stripe.setPublishableKey( wc_stripe_params.key );
}

$('#order_payment_popup input.select_payment_method').attr('disabled', 'disabled');

runTipTip();
POScalculateTotal();

$('#add_wc_pos_registers').submit(function(){
    var err = 0;
    $('.form-field').removeClass('form-invalid')
    if($('#_register_name').val() == ''){
        $('#_register_name').parents('.form-field').addClass('form-invalid');
        err++;
    }
    if($('#_register_grid_template').val() == '' || $('#_register_grid_template').val() == null){
        $('#_register_grid_template').parents('.form-field').addClass('form-invalid');                
        err++;
    }
    if($('#_register_receipt_template').val() == '' || $('#_register_receipt_template').val() == null){
        $('#_register_receipt_template').parents('.form-field').addClass('form-invalid');                
        err++;
    }
    if($('#_register_outlet').val() == '' || $('#_register_outlet').val() == null){
        $('#_register_outlet').parents('.form-field').addClass('form-invalid');                
        err++;
    }
    if(err){
        $(window).scrollTop(0);
        return false;
    }
    
});

char0 = new Array("§", "32");
char1 = new Array("˜", "732");
$('#wc-pos-customer-data').on('click', '.remove_customer_row', function() {
    $(this).closest('#customer_items_list').html(default_guest);
    $('#customer_items_list .tips').tipTip({
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        });
    $('#add_wc_pos_customer')[0].reset();
    
    $('tr.shipping_methods_register th, tr.shipping_methods_register td').html('');
    $('tr.shipping_methods_register').hide();
    
    POScalculateTotal();
    return false;
});

/****/

$('#clear_order_discount').on('click', function(){
    if($('#tr_order_discount').length > 0 ){
        $('#tr_order_discount').remove();
    }
    $('#order_discount_prev').val('');
    
    POScalculateTotal();
    $('#overlay_order_discount').hide();
});
$('#poststuff').on('click', '#span_clear_order_discount', function(){
    if($('#tr_order_discount').length > 0 ){
        $('#tr_order_discount').remove();
    }
    $('#order_discount_prev').val('');
    
    POScalculateTotal();
    $('#overlay_order_discount').hide();
});
$('#save_order_discount').on('click', function() {
    POScalculateTotal();
    $('#overlay_order_discount').hide();
});
$('#wc-pos-register-buttons').on('click', '.wc_pos_register_discount', function() {
    $('#overlay_order_discount').show();
});
function update_customer_popup(){
    if( $('#pos_c_user_id').length ){

        var id = $('#pos_c_user_id').val();
        var b  = $.parseJSON( $('#pos_c_billing_addr').val() );
        var s  = $.parseJSON( $('#pos_c_shipping_addr').val() );

        $('#customer_details_id').val(id);
        $('#save_customer').val('Update Customer');
        $.each(b, function(index, val) {
             $('#billing_'+index).val(val);
        });
        $.each(s, function(index, val) {
             $('#shipping_'+index).val(val);
        });
        
        $('select#billing_country, select#shipping_country').trigger("chosen:updated").trigger('change');

        $('#ship-to-different-address-checkbox').attr('checked', false).trigger('change');
        $('#customer_details .create-account').hide();

        $('#overlay_order_customer').show();
        $('#overlay_order_customer').css('visibility', 'visible');
    }else{
        $('#customer_details_id').val('');
        $('#save_customer').val('Save Customer');
        $('#add_wc_pos_customer')[0].reset();
        $('select#billing_country, select#shipping_country').trigger("chosen:updated");
    }
    return false;
}

$('body').on('click', 'a.show_customer_popup', function(event) {
    return update_customer_popup();
});
$('#wc-pos-customer-data').on('change', '.ajax_chosen_select_customer', function() {
    var _this = $(this);

    if(_this.val() == '')
      return false;

    ids_users = $('#customer_user').val();
    
        $('#wc-pos-customer-data').block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6
          }
        });

        var data = {
            action: 'wc_pos_add_customers_to_register',
            user_to_add: ids_users,
            security: wc_pos_params.add_customers_to_register,
            register_id: $('#id_register').val(),
        };

        $.post(wc_pos_params.ajax_url, data, function(response) {

            $('#wc-pos-customer-data #customer_items_list').html('').append(response);
                $('select#customer_user, #customer_user_chosen .chosen-choices').css('border-color', '').val('');
                $('select#customer_user').trigger("chosen:updated");

                update_customer_popup();
                
                $('#wc-pos-customer-data .new_row').removeClass('new_row');
                $('#wc-pos-customer-data').unblock();
                
        });
        if (ids_users) {

            if($( '.product_item_id' ).length > 0){
                var products_ids = $( '.product_item_id' ).serialize();
                var products_qt = $( '.quantity' ).serialize();
                var data2 = {
                    action: 'wc_pos_check_shipping',
                    user_to_add: ids_users,
                    security: wc_pos_params.check_shipping,
                    products_ids: products_ids,
                    products_qt: products_qt,
                    register_id: $('#id_register').val(),
                };

                $.post(wc_pos_params.ajax_url, data2, function(response) {
                    $( "tr.shipping_methods_register" ).replaceWith( response );
                    $( "tr.shipping_methods_register" ).show();
                    
                    
                    POScalculateTotal();
                });
            }
           

        }else{
            $( "tr.shipping_methods_register" ).replaceWith( '<tr class="shipping_methods_register"><th></th><td></td></tr>' );
            
            
            POScalculateTotal();
        }
    if( $().select2 != 'undefined')
      _this.select2('val', '');

    return false;
});



$('#wc-pos-register-data').on('change', '#shipping_method_0', function() {
    
    POScalculateTotal();
});

$('#wc-pos-register-data').on('change', '.check-column input', function() {
    
    POScalculateTotal();
});



/* Add Customer Popup open */
$('#add_customer_to_register').on('click', function() {
    $('#customer_details_id').val('');
    $('#customer_details .create-account').show();
    $('#save_customer').val('Save Customer');
    $('#overlay_order_customer').show();
    $('#overlay_order_customer').css('visibility', 'visible');
});

$('#retrieve_sales').on('click', function() {
    $('body, #retrieve_sales_popup').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
    $('#retrieve_sales_popup_content').html('');
    
    retrieve_sales('all');
    return false;
});
$('#overlay_retrieve_sales').on('click', '.load_order_data', function() {
    $('#retrieve_sales_popup').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
    var load_order_id = $(this).attr('href');
        load_order_id = load_order_id.replace("#", "");
    var data = {
        action: 'wc_pos_load_order_data',
        security      : wc_pos_params.load_order_data,
        order_id      : $('#order_id').val(),
        load_order_id : load_order_id,
        register_id   : $('#id_register').val(),
    };
    $.ajax({
        type: 'POST',
        url: wc_pos_params.ajax_url,
        data: data,
        success: function(response) {
          var j_data;
          try{
            // Get the valid JSON only from the returned string
            if ( response.indexOf( '<!--WC_POS_START-->' ) >= 0 )
                response = response.split( '<!--WC_POS_START-->' )[1]; // Strip off before after WC_POS_START

            if ( response.indexOf( '<!--WC_POS_END-->' ) >= 0 )
                response = response.split( '<!--WC_POS_END-->' )[0]; // Strip off anything after WC_POS_START

            // Parse
            j_data = JSON.parse(response);
            
            if( typeof j_data == 'object'){

              $.each(j_data.order_items, function(index, o_item ) {
                
                var item    = o_item.item;
                var option = {};
                var parent_id    = parseInt(item.product_id);
                var product_id   = parent_id;
                var variation_id = parseInt(item.variation_id);

                if(variation_id > 0)
                  product_id = variation_id;

                var row_class    = 'product_id_' + product_id;
                if(o_item.product === false){
                  var option = {};

                  var price  = parseFloat(item.line_subtotal) / parseInt(item.qty);

                  if ( wc_pos_params.wc.prices_include_tax == 'yes' ){
                    var tax  = parseFloat(item.line_subtotal_tax) / parseInt(item.qty);
                    price += tax;
                  }

                  option.title    = item.name;
                  option.price    = price;
                  option.quantity = parseInt(item.qty);

                  option.attributes = Array();

                  var attr_index = 0;
                  $.each(item.item_meta, function(attr_label, attr_value) {
                    if( typeof wc_pos_params.hidden_order_itemmeta[attr_label] == 'undefined' ){
                      option.attributes[attr_index] = {};
                      option.attributes[attr_index]['label']     = attr_label;
                      option.attributes[attr_index]['attribute'] = attr_value;
                      option.attributes[attr_index]['taxonomy']  = attr_label.split(' ').join('_');
                      row_class += '_' + attr_value;
                      attr_index++;
                    }
                  });
                  option.row_class = row_class;
                  POSaddProduct(option, true, false);
                }else{
                  
                  var price        = parseFloat(item.line_total) / parseInt(item.qty);
                  if ( wc_pos_params.wc.prices_include_tax == 'yes' ){
                    var tax  = parseFloat(item.line_tax) / parseInt(item.qty);
                    price += tax;
                  }

                  var attr = {};  

                  $.each(item.item_meta, function(attr_label, attr_value) {
                    if( typeof wc_pos_params.hidden_order_itemmeta[attr_label] == 'undefined' ){
                      var label = attr_label;

                      if( typeof wc_pos_params.attr_tax_names[attr_label] != 'undefined'){
                        label = wc_pos_params.attr_tax_names[attr_label];
                      }

                      attr[index] = {};
                      attr[index]['label']     = label;
                      attr[index]['attribute'] = attr_value;
                      attr[index]['taxonomy']  = attr_label;
                      row_class += '_' + attr[index]['attribute'];
                    }
                  });

                  POSaddProduct({
                    img        : item.featured_src,
                    id         : product_id,
                    href       : wc_pos_params.admin_url+'/post.php?post='+parent_id+'&amp;action=edit',
                    barcode    : item.barcode,
                    title      : item.name,
                    attributes : attr,
                    parent_id  : parent_id,
                    cat_ids    : item.categories_ids,
                    price      : price,
                    quantity   : parseInt(item.qty),
                    taxable    : item.taxable,
                    taxclass   : item.tax_class,
                    row_class  : row_class
                  }, false, false);
                }                 
              }); // end $.each(j_data.order_items

            
            $('#customer_user').val(j_data.customer).trigger('change');
            if(j_data.customer_info != '' ){
                var guest = j_data.customer_info;
                $.each(j_data.customer_info, function(index, val) {
                  $('#add_wc_pos_customer #'+index).val(val);   
                });
                $('#save_customer').trigger('click');
            }
            
            if(j_data.coupons && j_data.coupons != ''){
              $.each(j_data.coupons, function(index, val) {
                if(index == 'POS Discount'){
                  $('#order_discount_prev').val(val);
                  $('#order_discount_symbol').val('currency_symbol');
                  POScalculateTotal();
                }
                else
                  applyCoupon(index, true);
              });                  
            }
            
            $('input#order_id').val(load_order_id);
            $('#order_comments').val(j_data.notes);
            $('.overlay_order_popup').hide();
            $('#retrieve_sales_popup_content').html('');
            $('#retrieve_sales_popup').removeAttr('style');
            $('#retrieve_sales_popup').removeAttr('style');
          }

        }catch(e){
          console.log(e);
        }
        $('#retrieve_sales_popup').unblock();
      }
    });

    return false;
});
if($('#ship-to-different-address-checkbox').length > 0){
    $('#ship-to-different-address-checkbox').change(function () {
        if($(this).is(':checked')){
            $('.woocommerce-shipping-fields .shipping_address').show();
        }else{
            $('.woocommerce-shipping-fields .shipping_address').hide();
        }
    }).change();

    $('#billing-same-as-shipping').click(function () {
      var ar = ['country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode'];
      $.each(ar, function(index, val) {
         if( $('#billing_'+val).length && $('#shipping_'+val)){
          var v = $('#billing_'+val).val();
          $('#shipping_'+val).val(v);
         }
      });
      $('#shipping_country').trigger("chosen:updated").trigger('change');
    });
}
/* For saving Customer Data */
$('#save_customer').on('click', function() {
    $('#error_in_customer p').html('');
    $('#error_in_customer').hide();
    $('.form-invalid').removeClass('form-invalid');
    var err = 0;
    $('.woocommerce-billing-fields .validate-required').each(function() {
        $(this).find('input:not(".select2-offscreen, .select2-input, .input-checkbox"), select, textarea').each(function(index, el) {
            if($(el).val() == ''){
                $(el).closest('p').addClass('form-invalid');
                err++;
            }                
        });
    });
    $('.woocommerce-billing-fields .validate-email').each(function() {
        var f = $(this).find('input');
        if ( !checkEmail( f.val() ) ) {
            f.closest('p').addClass('form-invalid');
            err++;
        }
    });
    $('.woocommerce-billing-fields .validate-phone').each(function() {
        var f = $(this).find('input');
        if ( !checkPhone( f.val() ) ) {
            f.closest('p').addClass('form-invalid');
            err++;
        }
    });

    if($('#ship-to-different-address-checkbox').is(':checked')){
        $('.woocommerce-shipping-fields .validate-required').each(function() {
            $(this).find('input:not(".select2-offscreen, .select2-input, .input-checkbox"), select, textarea').each(function(index, el) {
                if($(el).val() == ''){
                    $(el).closest('p').addClass('form-invalid');
                    err++;
                }                
            });
        });
        $('.woocommerce-shipping-fields .validate-email').each(function() {
            var f = $(this).find('input');
            if ( !checkEmail( f.val() ) ) {
                f.closest('p').addClass('form-invalid');
                err++;
            }
        });
        $('.woocommerce-shipping-fields .validate-phone').each(function() {
            var f = $(this).find('input');
            if ( !checkPhone( f.val() ) ) {
                f.closest('p').addClass('form-invalid');
                err++;
            }
        });
    }
    if( $('.woocommerce-additional-fields').length ){
        $('.woocommerce-additional-fields .validate-required').each(function() {
            $(this).find('input:not(".select2-offscreen, .select2-input"), select, textarea').each(function(index, el) {
                if($(el).val() == ''){
                    $(el).closest('p').addClass('form-invalid');
                    err++;
                }                
            });
        });
        $('.woocommerce-additional-fields .validate-email').each(function() {
            var f = $(this).find('input');
            if ( !checkEmail( f.val() ) ) {
                f.closest('p').addClass('form-invalid');
                err++;
            }
        });
        $('.woocommerce-additional-fields .validate-phone').each(function() {
            var f = $(this).find('input');
            if ( !checkPhone( f.val() ) ) {
                f.closest('p').addClass('form-invalid');
                err++;
            }
        });
    }
    

    if (err) {
        window.scrollTo(0, parseInt($('.form-invalid').first().offset().top) - 100);
        return false;
    }

    if( $('#createaccount').is(':checked') || $('#customer_details_id').val() != ''){
        var data = {
            action: 'wc_pos_add_customer',
            form_data: $('#add_wc_pos_customer').serialize()
        };


        xhr = $.ajax({
            type: 'POST',
            url: wc_pos_params.ajax_url,
            data: data,
            beforeSend: function(xhr) {
                $('#order_customer_popup').block({
                  message: null,
                  overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                  }
                });
            },
            complete: function(xhr) {
                $('#order_customer_popup').unblock();
            },
            success: function(response) {
                var j_data;
                try{
                    // Get the valid JSON only from the returned string
                    if ( response.indexOf( '<!--WC_POS_START-->' ) >= 0 )
                        response = response.split( '<!--WC_POS_START-->' )[1]; // Strip off before after WC_POS_START

                    if ( response.indexOf( '<!--WC_POS_END-->' ) >= 0 )
                        response = response.split( '<!--WC_POS_END-->' )[0]; // Strip off anything after WC_POS_START

                    // Parse
                    j_data = JSON.parse(response);
                    console.log(j_data);
                    if (j_data.success == false) {
                        $('#error_in_customer').html('<p>'+j_data.message+'</p>');
                        $('#error_in_customer').show();
                        $('#customer_details').scrollTop(0);
                    } else {
                        $('#overlay_order_customer').hide();
                        $('#wc-pos-customer-data #customer_items_list').html(j_data.html);
                        $('#add_wc_pos_customer')[0].reset();
                        $('select#billing_country, select#shipping_country').trigger("chosen:updated");
                        $('#customer_details_id').val('');
                    }
                    
                        

                }catch(e){
                    console.log(e);
                }
                                
            }
        });
    }else{
        $('#overlay_order_customer').hide('slow');
        var name = $('#billing_first_name').val() + ' ' + $('#billing_last_name').val();
        var email = $('#billing_email').val();
        if( $('.customer-loaded-name').length ){
          $('.customer-loaded-name').html(name);
          $('.customer-loaded-email').html(email);
        }else{
          var customer = '<tr class="item" data-customer_id="0"><td class="avatar">'+wc_pos_params.avatar+'</td><td class="name">'+name+' (Guest) - '+email+'</td><td class="remove_customer"><a data-tip="Remove" class="remove_customer_row tips" href="#"></a> <input type="hidden" name="customer_details" value="'+$('#add_wc_pos_customer').serialize()+'" /></td> </tr>';
          $('#wc-pos-customer-data #customer_items_list').html(customer);
        }
        
    }
    

});
/* for calculate change amount*/

 $('.close_popup, .back_to_sale').on('click', function() {
    submit_f = false;
    notes = false;
    $('.overlay_order_popup').hide();
    $('#order_payment_popup input.select_payment_method').attr('disabled', 'disabled');

    if($('#add_wc_pos_customer').length > 0){
        $('#add_wc_pos_customer')[0].reset();
        $('select#billing_country, select#shipping_countrym select#shipping_state, select#billing_state').trigger("chosen:updated");
    }

    $('#error_in_customer').html('');
    $('#retrieve_sales_popup_content').html('');

    $('#coupon_tab div.messages').html('');
});

if($('.previous-next-toggles').length > 0 ){
    if($('#grid_layout_cycle > div').length <= 1 ){
        $('.previous-next-toggles').hide();
    }
    $('#grid_layout_cycle').cycle({
        speed:  'fast',
        timeout : 0,
        pager   : '.previous-next-toggles #nav_layout_cycle',
        next    : '.previous-next-toggles .next-grid-layout',
        prev    : '.previous-next-toggles .previous-grid-layout',
        before  : function(currSlideElement, nextSlideElement, options, forwardFlag) {
            var table = $(nextSlideElement).find('table');
            if(typeof table.data('title') != undefined ){
                var title = table.data('title');
                $('#wc-pos-register-grids-title').html(title);
            }
        }
    });
}
if($('#grid_category_cycle').length){
    $('#grid_category_cycle').category_cycle({
      count           : 25,
      hierarchy       : wc_pos_params.term_relationships.hierarchy,
      relationships   : wc_pos_params.term_relationships.relationships,
      parents         : wc_pos_params.term_relationships.parents,
      archive_display : wc_pos_params.category_archive_display,
      breadcrumbs     : $('#wc-pos-register-grids .hndle'),
      breadcrumbs_h   : $('#wc-pos-register-grids-title'),
    });
}

$('#order_payment_popup').on('click', 'input.go_payment', function() {
  var selected_payment_method = $('.select_payment_method:checked').attr('id');
  if(selected_payment_method == '' || selected_payment_method == undefined){
      toastr.error('Please select Payment method.');
      return false;
  }
  err = 0;
  if ($('#order_items_list tr').length == 0) {
      toastr.error('Please add products');
      err++;
  }
  if ($('#customer_items_list tr').length == 0) {
      toastr.error('Please add customer');
      err++;
  }

  if (err > 0)
      return false;

  /* check the amount pay and total paybale amount*/
  var total_amount = parseFloat($("#show_total_amt_inp").val());
  var $form        = $("#overlay_order_payment .overlay_order_popup_wrapper");
  var amount_pay   = $('#amount_pay_cod').val();
  
  if (amount_pay < total_amount && selected_payment_method == 'payment_method_cod') {
      $('.error_amount').html('Please enter correct amount.');
      toastr.error('Please enter correct amount.');
      return false;
  }else if (selected_payment_method == 'payment_method_stripe') {
      
      if ( $( 'input.stripe_token' ).size() == 0 ) {

              var card    = jQuery('#stripe-card-number').val();
              var cvc     = jQuery('#stripe-card-cvc').val();
              var expires = jQuery('#stripe-card-expiry').payment( 'cardExpiryVal' );
              if( typeof expires != 'object' || typeof expires.month == 'undefined'){

                var expr = jQuery('#stripe-card-expiry').val();
                expr = expr.split('/');
                expires = {};
                expires.month = expr[0];
                expires.year  = '';
                if( typeof expr[1] != 'undefined'){
                  expires.year = expr[1];
                }
              }

              $form.block({
                message: null,
                overlayCSS: {
                  background: '#fff',
                  opacity: 0.6
                }
              });

              var data = {
                  number:    card,
                  cvc:       cvc,
                  exp_month: parseInt( expires['month'] ) || 0,
                  exp_year:  parseInt( expires['year'] ) || 0
              };

              if($('#customer_items_list input[name="user_id"]').length > 0 && $('#customer_items_list input[name="user_id"]').val() != ''){
                  var request = {
                      action : 'wc_pos_stripe_get_user',
                      user_id: $('#customer_items_list input[name="user_id"]').val()
                  };
                  $.post(wc_pos_params.ajax_url, request, function(response) {
                      try {
                          var d = JSON.parse(response);
                          data.name = d.first_name + ' ' + d.last_name;
                          data.address_line1   = d.billing_address_1;
                          data.address_line2   = d.billing_address_2;
                          data.address_state   = d.billing_state;
                          data.address_city    = d.billing_city;
                          data.address_zip     = d.billing_postcode;
                          data.address_country = d.billing_country;
                      }
                      catch( err ) {
                      }
                      $form.unblock();
                  });                        
              }else {
                  if($('#billing_first_name').val() != '' && $('#billing_last_name').val() != ''){
                      data.name = $('#billing_first_name').val() + ' ' + $('#billing_last_name').val();
                      data.address_line1   = $('#billing_address_1').val();
                      data.address_line2   = $('#billing_address_2').val();
                      data.address_state   = $('#billing_state').val();
                      data.address_city    = $('#billing_city').val();
                      data.address_zip     = $('#billing_postcode').val();
                      data.address_country = $('#billing_country').val();
                  }else{
                      var request = {
                          action : 'wc_pos_stripe_get_outlet_address',
                          outlet_id: $('#outlet_ID').val()
                      };
                      $.post(wc_pos_params.ajax_url, request, function(response) {
                          var d = JSON.parse(response);
                          var contact = d.contact;
                          
                          data.name = 'Outlet "' + d.name + '"';
                          data.address_line1   = contact.address_1;
                          data.address_line2   = contact.address_2;
                          data.address_state   = contact.state;
                          data.address_city    = contact.city;
                          data.address_zip     = contact.postcode;
                          data.address_country = contact.country;
                          $form.unblock();
                      });    
                  }
              }
              Stripe.createToken( data, stripePOSResponseHandler );
              // Prevent form submitting
              return false;
      }
          $form.unblock();
  } else {
      $('.error_amount').html('');
  }
  $form.unblock();
  $('#edit_wc_pos_registers').submit();
});

 
});


/** functions **/
function stripePOSResponseHandler( status, response ) {
    
  var $form = jQuery("#order_payment_popup");
  if ( response.error ) {
      // show the errors on the form
      jQuery('.stripe_token').remove();
      jQuery('#stripe-card-number').closest('p').before( '<ul class="woocommerce_error woocommerce-error"><li>' + response.error.message + '</li></ul>' );
      toastr.error(response.error.message);
      $form.unblock();

  } else {
      // token contains id, last4, and card type
      var token = response['id'];

      // insert the token into the form so it gets submitted to the server
      $form.append("<input type='hidden' class='stripe_token' name='stripe_token' value='" + token + "'/>");
      jQuery('#edit_wc_pos_registers').submit();
  }
}
function calculateAmountTendered(){
  var total_amount = jQuery("#show_total_amt_inp").val();
    if(total_amount == '') total_amount = 0.00;
    else total_amount = parseFloat(total_amount).toFixed(2);

  amount_tendered = {
    'tendered_1' : '0.00',
    'tendered_2' : '',
    'tendered_3' : '',
    'tendered_4' : '',
  };
  tendered_num = {
    'tendered_1' : '0.00',
    'tendered_2' : '',
    'tendered_3' : '',
    'tendered_4' : '',
  };     

  tendered_num.tendered_1 = total_amount;
      amount_tendered.tendered_1 = accountingPOS( total_amount, 'formatNumber');
  
  var t_2 = 0;

  var decimal_p = parseFloat( ( total_amount - parseInt(total_amount) ).toFixed(2) );
  if(decimal_p > 0){
  t_2 = Math.ceil(total_amount);
  }else{
  t_2 = total_amount+1;
  }

  var t2_cel = Math.ceil(t_2);
  if( t2_cel == parseInt(t_2) )
  t2_cel = t2_cel+1;

  var t_3 = Math.ceil( t2_cel / 5 ) * 5;

  var t_4 = Math.ceil( t_3 / 10 ) * 10;
  if(t_4 == t_3){
  t_4 = Math.ceil( (t_3+1) / 10 ) * 10;
  }
  var i = 2;

  if( t_2 != total_amount && t_2 > total_amount ){
  tendered_num['tendered_'+i]    = parseFloat(t_2).toFixed(2);
      amount_tendered['tendered_'+i] = accountingPOS( t_2, 'formatNumber');
  i++;
  }
  if( t_3 != t_2 && t_3 > t_2){
  tendered_num['tendered_'+i]    = parseFloat(t_3).toFixed(2);
      amount_tendered['tendered_'+i] = accountingPOS( t_3, 'formatNumber');
  i++;
  }
  if( t_4 != t_3 && t_4 > t_3){
  tendered_num['tendered_'+i]    = parseFloat(t_4).toFixed(2);
      amount_tendered['tendered_'+i] = accountingPOS( t_4, 'formatNumber');
  i++;
  }


  for(var key in amount_tendered) {

   if (amount_tendered.hasOwnProperty(key)) {

      var value = amount_tendered[key];

      if(value){
        jQuery('.amount_pay_keypad .keypad-'+key).text(value).show();
      }else{
        jQuery('.amount_pay_keypad .keypad-'+key).text('').hide();
      }
   }

  }

  if(!amount_tendered.tendered_1)
  jQuery('.amount_pay_keypad .keypad-tendered_1').text('0.00').show();
}
function POSaddProduct(opt, custom, notification){

    var options = jQuery.extend({      
      img        : wc_pos_params.def_img,
      id         : wc_pos_params.custom_pr_id,
      href       : '',
      barcode    : '',
      title      : 'No_name',
      attributes : '',
      parent_id  : '',
      cat_ids    : '',
      price      : 0.00,
      quantity   : 1,
      taxable    : true,
      taxclass   : '',
      row_class  : '',
      stock_qty  : false,
    }, opt );

    var row_class    = 'product_id_' + options.id;
    var item_id      = options.id;
    var attributes   = '';
    var price_html   = accountingPOS(options.price*options.quantity, 'formatMoney');
    var title        = options.title;

    if(options.href != '' && wc_pos_params.user_can_edit_product == '1'){
      title = '<a href="'+options.href+'" target="_blank" >'+options.title+'</a>'

    }
    if(options.barcode != '')
      title = options.barcode + " &ndash; " + title;
    if(options.row_class != ''){
      item_id = row_class = hex_md5(options.row_class);
    }
    
    if(typeof options.attributes == "object"){

        var attr    = "";
        var a_class = "";
        if(custom)
          a_class = "pos_pr_variations";
        
        jQuery.each(options.attributes, function(index, val) {

            var hidden = '';
            
            hidden += '<input type="hidden" class="'+a_class+'" name="variations['+item_id+']['+val.taxonomy+']" value="'+val.attribute+'" data-attrlabel="'+val.label+'" />';

            if(options.row_class == ''){
              row_class += '_'+val.attribute;
            }
            attr += "<li class='"+a_class+"'><span class='meta_label'>"+val.label+"</span><span class='meta_value'><p>"+val.attribute+"</p>"+hidden+"</span></li>";
        });

        attributes = attr;
    }else{
      attributes = options.attributes;
    }
    

    var custom_product = '';
    var tip            = '';
    if(custom === true){
      custom_product = '<input type="hidden" name="custom_product_name['+item_id+']" value="'+options.title+'">';
      tip            = 'Custom product';
    }else{
      tip = '<strong>Product ID:</strong>'+options.id+'<br>';
        if(options.barcode != '')
          tip += '<strong>Product SKU:</strong>'+options.barcode+'<br>';
    }
    var stock_qty = '';
    if(wc_pos_params.show_stock == 'yes' && options.stock_qty !== false){
      stock_qty = '<span class="register_stock_indicator"><b>'+options.stock_qty+'</b> In Stock </span>';
    }

    var sale = '';
    if(  typeof options.sale_price != 'undefined' && typeof options.regular_price != 'undefined' && options.sale_price != null && options.sale_price != options.regular_price ){
      var regular_price = accountingPOS(options.regular_price, 'formatMoney');
      sale = '<span class="register_sale_indicator"><del>'+regular_price+'</del> </span>';
    }
    if(options.taxable === false){
      row_class += ' no_taxable';
    }
    
    var row = '\
        <tr class="item '+row_class+' new_row" id="'+row_class+'">\
          <td class="thumb"><img width="90" height="90" src="'+options.img+'" class="attachment-shop_thumbnail wp-post-image tips" data-tip="'+tip+'"></td>\
          <td class="name">\
            <span>'+title+'</span>\
            <br>\
            <input type="hidden" class="product_item_id" name="product_item_id['+item_id+']" value="'+options.id+'">\
            <input type="hidden" class="product_parent_id" value="'+options.parent_id+'">\
            <input type="hidden" class="product_cat_ids" value="'+options.cat_ids+'">\
            '+custom_product+'\
            <div class="view"><ul class="display_meta">\
            '+attributes+'\
            '+stock_qty+'\
            '+sale+'\
            </ul></div>\
          </td>\
          <td class="edit_link">\
              <a href="#" class="add_custom_meta button tips" data-tip="Edit Product"></a>\
          </td>\
          <td width="1%" class="line_cost">\
            <div class="view">\
              <input type="text" class="product_price" value="'+options.price+'" name="order_item_price['+item_id+']" data-original="'+options.price+'" data-istaxable="'+options.taxable+'" data-taxclass="'+options.taxclass+'" data-discountsymbol="currency_symbol" data-percent="0" data-modprice="'+options.price+'" >\
              <input type="hidden" value="0" class="pr_coupon_discount" />\
              <input type="hidden" value="'+options.price+'" class="final_total_amount" />\
            </div>\
          </td>\
          <td width="1%" class="quantity">\
            <div class="edit">\
            <input type="text" min="0" autocomplete="off" name="order_item_qty['+item_id+']" placeholder="0" value="'+options.quantity+'" class="quantity">\
          </div>\
          </td>\
          <td width="1%" class="line_cost_total">\
            <div class="view">\
            <span class="amount"><span class="amount">'+price_html+'</span></span>\
          </div>\
          </td>\
          <td class="remove_item">\
            <a href="#" class="remove_order_item tips" data-tip="'+wc_pos_params.remove_button+'"></a>\
          </td>\
        </tr>';
      jQuery('#wc-pos-register-data #order_items_list').append(row);

      POScalculateTotal();
      reloadKeypad();
      runTipTip();
      if(notification !== false){
        toastr.info('Product added successfully');
        ion.sound.play("basket_addition");
      }
}
function calculateLinePrice($elem){

    var symbol     = $elem.data('discountsymbol');
    var real_price = $elem.data('original');
        real_price = parseFloat(real_price);
    var modprice   = $elem.data('modprice');
        modprice   = parseFloat(modprice);
    var price      = $elem.val();
    var percent    = 0;
    var discount   = 0;

    if(symbol == 'percent_symbol'){
        percent = price;
        percent = parseInt(percent);
        percent = !isNaN(percent) ? percent : 0;
        $elem.val(percent);

        price = (real_price - (real_price*percent/100) )
        if(price < 0){
          price = 0;
          percent = 100;
        }
        price   = !isNaN(price) ? price : 0;        

        $elem.data('percent', percent);
        $elem.data('modprice', price);

        price = accountingPOS(price, 'formatMoney');

        jQuery('.price_keypad .keypad-price_val1').text( price );
        jQuery('.price_keypad .keypad-price_val2').text( percent );

    }else{
      price = checkFormatPricePOS(price);
      $elem.val(price);
      price = Number(price);
      var r_percent = price*100/real_price;

      percent = (100-r_percent);
      percent = parseInt(percent);
      percent = !isNaN(percent) ? percent : 0;

      $elem.data('percent', percent);
      $elem.data('modprice', price);
      jQuery('.price_keypad .keypad-price_val1').text( (percent) +'% off ');
      jQuery('.price_keypad .keypad-price_val2').text( price );
  }

}
function runTipTip() {
    // remove any lingering tooltips
    jQuery('#tiptip_holder').removeAttr('style');
    jQuery('#tiptip_arrow').removeAttr('style');
    if(isTouchDevice() === false){
        // init tiptip
        jQuery('.tips').tipTip({
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        });
    }
}
function calculateDiscount(total){
    var discount_prev = jQuery('#order_discount_prev').val();
    if(discount_prev == '')
      discount_prev = 0;

    if( typeof total == 'undefined')
      total = 0;

    var discount_val  = 0;
    var percent       = 0;
    var symbol        = jQuery('#order_discount_symbol').val();
    if (discount_prev && total) {
        discount_prev = parseFloat( discount_prev.replace("/\%/g", "") );

        if(symbol == 'percent_symbol'){
            percent      = parseInt(discount_prev);
            jQuery('#order_discount_prev').val(percent);
            discount_val = accountingPOS( total*percent/100, 'formatNumber');
            jQuery('.discount_keypad .keypad-discount_val1').text( discount_val );
            jQuery('.discount_keypad .keypad-discount_val2').text( percent );
        }else{
            var price = jQuery('#order_discount_prev').val();
            if(price != '' && price != parseInt(price) ){                    
                var num_after_dot = price.length - price.indexOf(".")-1;
                if(num_after_dot > wc_pos_params.currency_format_num_decimals){
                  price = accountingPOS( price, 'formatNumber');
                  discount_prev =  price;
                  jQuery('#order_discount_prev').val(discount_prev);                        
                }
            }
            percent      = (discount_prev*100/total).toFixed(2);;
            discount_val = discount_prev;
            jQuery('.discount_keypad .keypad-discount_val1').text( percent +'% off ');
            jQuery('.discount_keypad .keypad-discount_val2').text( discount_val );
        }
        var formatted_discount = accountingPOS( total*percent/100, 'formatMoney');
        if(percent){
            formatted_discount = percent+'% - '+ formatted_discount;
        }
        if (jQuery('#tr_order_discount').length > 0) {
            jQuery('#formatted_order_discount').text(formatted_discount);
            jQuery('#order_discount').val(discount_val);
            jQuery('#order_discount_percent').val(percent);
        } else {
            var html = '<tr id="tr_order_discount">\
                    <th class="total_label"><span id="span_clear_order_discount">Remove</span>Order Discount</th>\
                    <td class="total_amount">\
                    <input type="hidden" value="'+discount_val+'" id="order_discount" name="order_discount">\
                    <input type="hidden" value="'+percent+'" id="order_discount_percent" name="order_discount_percent">\
                    <strong id="formatted_order_discount" >' + formatted_discount + '</strong>\
                    </td>\
                  </tr>';
            
                if(jQuery('tr.tr_order_coupon').length)
                    jQuery('table.woocommerce_order_items tr.tr_order_coupon').last().after(html);
                else
                    jQuery('table.woocommerce_order_items #tr_order_subtotal_label').after(html);
            
        }
        
        getCouponCartPercentAmount(percent, total);
    }else{
        if(symbol == 'percent_symbol'){
            jQuery('.discount_keypad .keypad-discount_val1').text( '0.00' );
            jQuery('.discount_keypad .keypad-discount_val2').text( '0' );
        }else{
            jQuery('.discount_keypad .keypad-discount_val1').text( '0% off ' );
            jQuery('.discount_keypad .keypad-discount_val2').text( '0.00' );                    
        }
        jQuery('#tr_order_discount').remove();
    }
}
function setTotalPriceProd() {
  jQuery('#order_items_list tr.item').each(function(index, el) {

    var final_price = jQuery(el).find('.final_total_amount').val();
        final_price = parseFloat( final_price );
        final_price = accountingPOS(final_price, 'formatMoney');

    var discount = jQuery(el).find('.pr_coupon_discount').val();
        discount = parseFloat( discount );
    
    var modprice = jQuery(el).find('.product_price').data('modprice');
        modprice = parseFloat( modprice );

    var original = jQuery(el).find('.product_price').data('original');
        original = parseFloat( original );

    if(discount > 0 || original != modprice){

      var qt = jQuery(el).find('input.quantity').val();
          qt = parseFloat( qt );

      var default_price = original*qt;
          default_price = accountingPOS(default_price, 'formatMoney');
      jQuery(el).find('.line_cost_total .view').html('<del>'+default_price+'</del><span class="amount">'+final_price+'</span>');
    }else{
      jQuery(el).find('.line_cost_total .view').html('<span class="amount">'+final_price+'</span>');
    }

  });
}
function getCartTotal (sub_total) {
  if(typeof sub_total == 'undefined')
    sub_total = 0;
  jQuery('.tr_order_coupon .cart_coupon_amount').each(function(index, el) {
    sub_total = sub_total - parseFloat( jQuery(this).val() );
  });
  if( jQuery('#order_discount').length && jQuery('#order_discount').val() != ''){
    sub_total = sub_total - parseFloat(jQuery('#order_discount').val());
  }
  if(jQuery('select#shipping_method_0').length > 0){
    if(jQuery('select#shipping_method_0 option:selected').length)
      sub_total += parseFloat(jQuery('select#shipping_method_0 option:selected').attr('data-cost') );
    else
      sub_total += parseFloat( jQuery('input#shipping_method_0').attr('data-cost') );
  }
  if(jQuery('input.shipping_price').length > 0){
    sub_total += parseFloat(jQuery('input.shipping_price').val() );
  }
  return sub_total;
}

function getSubTotal () {
  var subtotal = 0;
  jQuery('#order_items_list tr.item').each(function(index, el) {
    var price = parseFloat( jQuery(el).find('input.product_price').data('modprice') );
    var qt    = parseInt( jQuery(el).find('input.quantity').val() );
    jQuery(el).find('input.pr_coupon_discount').val(0);
    jQuery(el).find('input.final_total_amount').val(price*qt);
    subtotal += price*qt;
  });  

  return subtotal;
}
function getTotalTaxNumber(total) {
    var number_tax   = 0;
    if(typeof total == 'undefined')
      return 0;
    var tax    = calc_tax(total);        
    number_tax = round(tax, wc_pos_params.currency_format_num_decimals, wc_pos_params.tax_rounding_mode);

    return number_tax;
}

function POScalculateTotal(el) { 

  if( typeof el != 'undefined')
    calculateLinePrice(el);

    var sub_total = getSubTotal();
        discounted_total = calcProductCoupons(sub_total);
        
    calculateDiscount(discounted_total);
    setTotalPriceProd();
    
    var total = getCartTotal(sub_total);
    
    if(wc_pos_params.pos_calc_taxes == 'enabled'){
        var tax = calc_tax();
        var wc = wc_pos_params.wc;
        if(wc.prices_include_tax != 'yes'){
          total += tax;
        }
        if(wc.prices_include_tax == 'yes'){
          sub_total -= tax;          
        }
    }

    /*************************************/

    var formatted_sub_total = accountingPOS(sub_total, 'formatMoney');
    jQuery('#inp_subtotal_amount').val(sub_total);
    jQuery('#subtotal_amount').html(formatted_sub_total);

    /*************************************/

    var formatted_total = accountingPOS( total, 'formatMoney');
    var _total = accountingPOS( total, 'defaultNum');

    jQuery('#show_total_amt_inp').val( _total  );
    jQuery('#show_total_amt').html(formatted_total);
    jQuery('#total_amount').html(formatted_total);

    
  calculateAmountTendered();
}

function inputQuantityVal(val) {    
  val = parseInt(val);
  if(isNaN(val)){
      val = '';
  }
  return val;
}


function checkEmail(e)
{
    ok = "1234567890qwertyuiop[]asdfghjklzxcvbnm.@-_QWERTYUIOPASDFGHJKLZXCVBNM+";

    for (i = 0; i < e.length; i++)
        if (ok.indexOf(e.charAt(i)) < 0)
            return (false);

    if (document.images)
    {
        re = /(@.*@)|(\.\.)|(^\.)|(^@)|(@$)|(\.$)|(@\.)/;
        re_two = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        if (!e.match(re) && e.match(re_two))
            return true;
        else
            return false;
    }
    return true;

}

function checkPhone(e)
{  
    var number_count = 0;
    for (i = 0; i < e.length; i++)
        if ((e.charAt(i) >= '0') && (e.charAt(i) <= 9))
            number_count++;

    if (number_count == 11 || number_count <= 12)
        return true;

    return false;
}
function delete_cookie (name) {
    document.cookie = name + '=;Path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
function isTouchDevice() {
  //return true;
   var el = document.createElement('div');
   el.setAttribute('ontouchstart', 'return;');

   if(typeof el.ontouchstart == "function"){
      return true;
   }else {
      return false;
   }
}

function checkFormatPricePOS (price) {
  if( typeof price == 'number')
    price = price.toString();

  var pos_dot       = price.indexOf(".");
  var num_after_dot = price.length - pos_dot-1;  
  
  if(pos_dot >= 0 && num_after_dot > 2){
      price = accountingPOS(price, 'defaultNum');
  }  
  return !isNaN(price) ? price : 0;
}

function accountingPOS (price, format) {
  if( typeof price == 'undefined')
    price = 0;

  switch(format){
    case 'formatMoney':
      price = accounting.formatMoney(price, {
          symbol   : wc_pos_params.currency_format_symbol,
          decimal  : wc_pos_params.currency_format_decimal_sep,
          thousand : wc_pos_params.currency_format_thousand_sep,
          precision: wc_pos_params.currency_format_num_decimals,
          format   : wc_pos_params.currency_format
      });
      break;
    case 'unformat':
      price = accounting.unformat(price, wc_pos_params.mon_decimal_point);
      break;
    case 'defaultNum':
      price = accounting.formatNumber( price, 2, '', '.');      
      break;
    case 'formatNumber':
      price = accounting.formatNumber( price, wc_pos_params.currency_format_num_decimals, wc_pos_params.currency_format_thousand_sep, wc_pos_params.currency_format_decimal_sep);
      break;
  }
  return price;
}
Array.prototype.in_array = function(p_val) {
    for(var i = 0, l = this.length; i < l; i++) {
        if(this[i] == p_val) {
            return true;
        }
    }
    return false;
}
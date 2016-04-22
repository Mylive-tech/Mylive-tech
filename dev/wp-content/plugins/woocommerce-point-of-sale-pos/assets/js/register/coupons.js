var wc_applied_coupons = {};
var wc_coupons = {};
var clearCoupons,
applyCoupon,
calcProductCoupons;

jQuery(document).ready(function($) {
$.when(get_coupons()).then(function(response) {
    if (response) {
        $.each(response.coupons, function(index, val) {
           wc_coupons[val.code] = val;
        });
    }
}); 
function get_coupons() {
    return $.getJSON(wc_pos_params.wc_api_url+'coupons/');
}

applyCoupon = function(code, loaded){
  if(typeof code == 'undefined'){
    code = $('#coupon_code').val();
  }
  
  $('#coupon_code').val('');
  if(code != ''){
    if(typeof wc_coupons[code] != 'undefined'){
      if(typeof wc_applied_coupons[code] != 'undefined'){
        showCouponMessage(wc_pos_params.i18n_c_already_applied, 'error');
        return false;
      }else{
        var coupon = wc_coupons[code];           

        if( !validateCouponCart(coupon))
            return false;           

        if(coupon.individual_use === true){
            clearCoupons();
        }
        
        wc_applied_coupons[code] = coupon;
        
        if(typeof loaded == 'undefined')
          POScalculateTotal();

        showCouponMessage(wc_pos_params.i18n_c_applied, 'updated');
      }
    }else{
        showCouponMessage(wc_pos_params.i18n_c_not_exist, 'error');
        return false;
    }
  }      
}

calcProductCoupons = function (sub_total) {
	if( typeof sub_total == 'undefined')
		sub_total = 0;

    $.each(wc_applied_coupons, function(index, coupon) {
        if(!validateCouponCart(coupon, false)){
            var msg = wc_pos_params.i18n_c_invalid.replace('%s', coupon.code );
            toastr.error(msg);
            ion.sound.play("error");
            clearCoupons(coupon.code);
        }else{

            if(coupon.limit_usage_to_x_items == '' || coupon.limit_usage_to_x_items == 0){
                coupon.limit_usage_to_x_items == -1;
            }

            var need_update = false;
            var coupon_amount = 0;
            var formatted_coupon = accountingPOS(0, 'formatMoney');;
            if(coupon.type == 'fixed_product'){
            	coupon_amount = getCouponFixedProductAmount(coupon);                   
                sub_total = sub_total - coupon_amount;
                formatted_coupon = accountingPOS(coupon_amount, 'formatMoney');
            }
            else if (coupon.type == 'percent_product'){
            	coupon_amount = getCouponPercentProductAmount(coupon);
                sub_total = sub_total - coupon_amount;
                formatted_coupon = accountingPOS(coupon_amount, 'formatMoney');
            }
            else if(coupon.type == 'fixed_cart'){
                coupon_amount = coupon.amount;
                setCouponCartFixedAmount(coupon.amount, sub_total);
                sub_total = sub_total - parseFloat(coupon_amount);
                formatted_coupon = accountingPOS(coupon.amount, 'formatMoney');
            }
            else if( coupon.type == 'percent' ){
                need_update = true;
                coupon_amount = getCouponCartPercentAmount(coupon.amount, sub_total);

                sub_total = sub_total - parseFloat(coupon_amount);

                formatted_coupon = accountingPOS(coupon_amount, 'formatMoney');
                formatted_coupon = parseFloat(coupon.amount) + '% &ndash; ' + formatted_coupon;
            }

            if(formatted_coupon != '')
                formatted_coupon = '&ndash; '+ formatted_coupon;

            var code = coupon.code;
            var $tr_order_coupon = $('.tr_order_coupon[data-coupon="'+code+'"]');
            if( $tr_order_coupon.length ) {
                $tr_order_coupon.find('span.formatted_coupon').html(formatted_coupon);
                $tr_order_coupon.find('input.cart_coupon_amount').val(coupon_amount);
            }else{
                var coupon_html = '\
                <tr class="tr_order_coupon" data-coupon="'+code+'">\
                    <th class="coupon_label"><span class="span_clear_order_coupon">Remove</span>Coupon</th>\
                    <td class="coupon_amount">\
                        <input type="hidden" name="order_coupon[]" class="order_coupon_amount" value="'+code+'">\
                        <input type="hidden" class="cart_coupon_amount" value="'+coupon_amount+'">\
                        <strong><span class="coupon_code">'+code+'</span> <span class="formatted_coupon">'+formatted_coupon+'</span></strong>\
                    </td>\
                </tr>';
                
                if( $('.tr_order_coupon').length )
                    $('.tr_order_coupon').last().after(coupon_html);
                else
                    $('table.woocommerce_order_items #tr_order_subtotal_label').after(coupon_html);
                 
            }          
        }
    });
	return sub_total;
}
function setCouponCartFixedAmount(amount, subtotal){
    
    $('#order_items_list tr.item').each(function() {
        var pr = $(this);
        var d  = parseFloat(pr.find('input.pr_coupon_discount').val());
        var f  = parseFloat(pr.find('input.final_total_amount').val());
        
        var qt       = pr.find('input.quantity').val();

        var discount_percent = f/subtotal;
        var discount         = amount * discount_percent;

        var total = accounting.unformat( discount, wc_pos_params.mon_decimal_point);        

        var final_total_amount = f - total;
        pr.find('input.pr_coupon_discount').val( d + total );
        pr.find('input.final_total_amount').val( final_total_amount < 0 ? 0 : final_total_amount );

    });
}
getCouponCartPercentAmount = function (amount, subtotal){
    if(typeof subtotal == 'undefined')
        subtotal = 0;

    if(typeof amount == 'undefined')
        return 0;

    $('#order_items_list tr.item').each(function() {
        var pr = $(this);
        var d  = parseFloat(pr.find('input.pr_coupon_discount').val());
        var f  = parseFloat(pr.find('input.final_total_amount').val());
        
        var qt       = pr.find('input.quantity').val();
        var percent     = ( parseFloat(amount) / 100 ) * f;

        var total = accounting.unformat( percent, wc_pos_params.mon_decimal_point);        

        var final_total_amount = f - total;
        pr.find('input.pr_coupon_discount').val( d + total );
        pr.find('input.final_total_amount').val( final_total_amount < 0 ? 0 : final_total_amount );

    });

    return ( parseFloat(amount) / 100 ) * subtotal;
}
getCouponFixedProductAmount = function(_coupon) {
	var total_d = 0;
    var limit = _coupon.limit_usage_to_x_items;

    $('#order_items_list tr.item').each(function() {
        var pr = $(this);
        
        if(isValidProductForCoupon(pr, _coupon)){
            var d   = parseFloat(pr.find('input.pr_coupon_discount').val());
            var f   = parseFloat(pr.find('input.final_total_amount').val());
            var qt  = parseInt(pr.find('input.quantity').val());
            
            var total = 0;
            if( limit < 0 ){
                total = accounting.unformat( _coupon.amount * qt, wc_pos_params.mon_decimal_point);
            }else if(limit > 0) {
                var limit_usage_qty = Math.min( limit, qt );
                limit               = Math.max( 0, limit - limit_usage_qty );
                total = accounting.unformat( _coupon.amount * parseInt(limit_usage_qty), wc_pos_params.mon_decimal_point);
            }

            
            total_d += total;

            var final_total_amount = f - total;
            pr.find('input.pr_coupon_discount').val( d + total );
            pr.find('input.final_total_amount').val( final_total_amount < 0 ? 0 : final_total_amount );
        }

    });
    return total_d;
}
function getCouponPercentProductAmount (_coupon) {
    var total_d = 0;    
    var limit = _coupon.limit_usage_to_x_items;

    $('#order_items_list tr.item').each(function() {

    	var total = 0;
        var pr = $(this);
        if( isValidProductForCoupon(pr, _coupon) ){
            var d   = parseFloat(pr.find('input.pr_coupon_discount').val());
            var f   = parseFloat(pr.find('input.final_total_amount').val());
            var qt  = parseInt(pr.find('input.quantity').val());
            var percent = 0;
            
            if( limit < 0 ){
                percent = ( parseFloat(_coupon.amount) / 100 ) * f;
            }else if(limit > 0) {
                var limit_usage_qty              = Math.min( limit, qt );
                limit    = Math.max( 0, limit - limit_usage_qty );
                var pr_price = f/qt;
            
                var _final  = pr_price * limit_usage_qty;
                
                percent = ( parseFloat(_coupon.amount) / 100 ) * _final;
            }
            total   = accounting.unformat( percent, wc_pos_params.mon_decimal_point);

            total_d += total;

            var final_total_amount = f - total;
            pr.find('input.pr_coupon_discount').val( d + total );
            pr.find('input.final_total_amount').val( final_total_amount < 0 ? 0 : final_total_amount );
        }

    });
    return total_d;
}

function isValidProductForCoupon(pr, coupon){
    var valid_for_cart = true;

    if( pr.length ){

        var vr_id   = pr.find('td.name .product_item_id').val();
        var pr_id   = pr.find('td.name .product_parent_id').val();
        var cat_ids = pr.find('td.name .product_cat_ids').val();


        if( coupon.product_ids.length > 0 && !coupon.product_ids.in_array(pr_id) && !coupon.product_ids.in_array(vr_id) ){
            valid_for_cart = false;
        }else if( coupon.exclude_product_ids.length > 0 && ( coupon.exclude_product_ids.in_array(pr_id) || coupon.exclude_product_ids.in_array(vr_id) ) ){
            valid_for_cart = false;
        }else if(cat_ids != '' && typeof cat_ids != 'undefined'){
            cat_ids = JSON.parse(cat_ids);
            
            if(coupon.product_category_ids.length > 0 && $.arrayIntersect(coupon.product_category_ids, cat_ids).length == 0){
                valid_for_cart = false;
            }
            if(coupon.exclude_product_category_ids.length > 0 && $.arrayIntersect(coupon.exclude_product_category_ids, cat_ids).length ){
                valid_for_cart = false;
            }                
        }
        if( coupon.exclude_sale_items === true && ( wc_pos_params.product_ids_on_sale.in_array(pr_id) || wc_pos_params.product_ids_on_sale.in_array(vr_id) ) ){
            valid_for_cart = false;
        }
    }else{
        valid_for_cart = false;
    }
    return valid_for_cart;
}
function showCouponMessage (msg, type) {
    var html = '<div class="'+type+'"><p>'+msg+'</p></div>';
    $('#coupon_tab .messages ').html(html);
}
clearCoupons = function(code){
    if(typeof code == 'undefined'){
        wc_applied_coupons = {};
        $('tr.tr_order_coupon').remove();
    }else if(typeof wc_applied_coupons[code] != 'undefined'){
        delete wc_applied_coupons[code];
        $('.order_coupon_amount[value="'+code+'"]').closest('tr').remove();
    }
}
$.arrayIntersect = function(a, b)
{
    return $.grep(a, function(i)
    {
        return $.inArray(i, b) > -1;
    });
};

function validate_sale_items (coupon) {
    
    if( coupon.exclude_sale_items === true && coupon.type != 'fixed_product' && coupon.type != 'percent_product'){
        var valid_for_cart = true;

        if( $('#order_items_list tr').length > 0 ){
            $('#order_items_list tr').each(function(index, el) {
                if( $(el).find('td.name .product_parent_id').length ){
                    var vr_id = $(el).find('td.name .product_item_id').val();
                    var pr_id = $(el).find('td.name .product_parent_id').val();
                    if( wc_pos_params.product_ids_on_sale.in_array(pr_id) || wc_pos_params.product_ids_on_sale.in_array(vr_id) ){
                        valid_for_cart = false;
                    }
                }

            });
        }
        return valid_for_cart;
    }
    return true;
}
function validateCouponCart (coupon, ind) {
    var user = $('#pos_c_user_id').length ? $('#pos_c_user_id').val() : 0;
    if(ind !== false){
        var c_name = validate_individual_coupons();
        if( c_name != '' ){
            var msg = wc_pos_params.i18n_c_individual_error.replace('%s', c_name);
            showCouponMessage(msg, 'error');
            return false;
        }
    }
    if(coupon.usage_count == coupon.usage_limit){
        showCouponMessage(wc_pos_params.i18n_c_usage_limit, 'error');
        return false;
    }
    if( coupon.usage_limit_per_user != null && user > 0 && coupon.used_by != null && coupon.used_by.in_array(user) ){
        showCouponMessage(wc_pos_params.i18n_c_usage_limit, 'error');
        return false;
    }
    
    if( coupon.expiry_date != null && coupon.expiry_date){
        var expiry_date = new Date(coupon.expiry_date).getTime();
        var now         = new Date().getTime();
        if(now > expiry_date){
            showCouponMessage(wc_pos_params.i18n_c_expired, 'error');
            return false;
        }
    }
    if( !validate_minimum_amount(coupon) ){
        var msg = wc_pos_params.i18n_c_minimum_spend.replace('%s', coupon.minimum_amount);
        showCouponMessage(msg, 'error');
        return false;
    }
    if( !validate_maximum_amount(coupon) ){
        var msg = wc_pos_params.i18n_c_maximum_spend.replace('%s', coupon.maximum_amount);
        showCouponMessage(msg, 'error');
        return false;
    }
    if( !validate_product_ids(coupon) ){
        showCouponMessage(wc_pos_params.i18n_c_not_applicable, 'error');
        return false;
    }
    if( !validate_product_categories(coupon) ){
        showCouponMessage(wc_pos_params.i18n_c_not_applicable, 'error');
        return false;
    }

    if( !validate_cart_excluded_product_categories(coupon) ){
        showCouponMessage(wc_pos_params.i18n_c_not_applicable, 'error');
        return false;
    }

    if( !validate_sale_items(coupon) ){
        showCouponMessage(wc_pos_params.i18n_c_sale_items, 'error');
        return false;
    }
    var pr = validate_cart_excluded_product_ids(coupon);
    if( pr.length ){
        var msg = wc_pos_params.i18n_c_not_applicable_pr.replace('%s', pr.join(', '));
        showCouponMessage(msg, 'error');
        return false;
    }
    return true;
}

function validate_individual_coupons () {
    var c_name = '';
    if( $(wc_applied_coupons).length ){
        var valid_for_cart = true;
        $.each(wc_applied_coupons, function(index, coupon) {
            
            if(coupon.individual_use === true)
                c_name = coupon.code;
        });
    }
    return c_name;
}

function validate_minimum_amount (coupon) {
    var subtotal = parseFloat( $('#inp_subtotal_amount').val() );
    var min      = parseFloat(coupon.minimum_amount);
    if(isNaN(subtotal))
        subtotal = 0;
    
    if(min > 0 && min > subtotal){
        return false;
    }
    return true;
}
function validate_maximum_amount (coupon) {
    if( typeof coupon.maximum_amount == 'undefined' )
        return true;
            
    var subtotal = parseFloat( $('#inp_subtotal_amount').val() );
    var max      = parseFloat(coupon.maximum_amount);
    if(isNaN(subtotal))
        subtotal = 0;
    
    if(max > 0 && subtotal > max ){
        return false;
    }
    return true;
}

function validate_product_ids (coupon) {
    if( coupon.product_ids.length > 0 && coupon.type != 'fixed_product' && coupon.type != 'percent_product'){
        var valid_for_cart = false;
        if( $('#order_items_list tr').length > 0 ){
            $('#order_items_list tr').each(function(index, el) {

                if( $(el).find('td.name .product_parent_id').length ){
                    var vr_id = $(el).find('td.name .product_item_id').val();
                    var pr_id = $(el).find('td.name .product_parent_id').val();
                    if( coupon.product_ids.in_array(pr_id) || coupon.product_ids.in_array(vr_id) ){
                        valid_for_cart = true;
                    }
                }

            });
        }
        return valid_for_cart;
    }
    return true;
}

function validate_cart_excluded_product_ids (coupon) {
    var pr = [];
    if( coupon.exclude_product_ids.length > 0 && coupon.type != 'fixed_product' && coupon.type != 'percent_product'){
        var i  = 0;
        if( $('#order_items_list tr').length > 0 ){
            $('#order_items_list tr').each(function(index, el) {

                if( $(el).find('td.name .product_parent_id').length ){
                    var vr_id = $(el).find('td.name .product_item_id').val();
                    var pr_id = $(el).find('td.name .product_parent_id').val();
                    if( coupon.exclude_product_ids.in_array(pr_id) || coupon.exclude_product_ids.in_array(vr_id) ){
                        var name  = $(el).find('td.name > a').text();
                        pr[i] = name;
                        i++;
                    }
                }

            });
        }
    }
    return pr;
}

function validate_product_categories (coupon) {
    if( coupon.product_category_ids.length > 0 && coupon.type != 'fixed_product' && coupon.type != 'percent_product'){
        var valid_for_cart = false;
        if( $('#order_items_list tr').length > 0 ){
            $('#order_items_list tr').each(function(index, el) {

                if( $(el).find('td.name .product_cat_ids').length ){
                    var cat_ids = $(el).find('td.name .product_cat_ids').val();
                    if(cat_ids != ''){
                        cat_ids = JSON.parse(cat_ids);
                        
                        if( $.arrayIntersect(coupon.product_category_ids, cat_ids).length ){
                             valid_for_cart = true;
                        }                                
                    }
                }

            });
        }
        return valid_for_cart;
    }
    return true;
}
function validate_cart_excluded_product_categories (coupon) {
    if( coupon.exclude_product_category_ids.length > 0 && coupon.type != 'fixed_product' && coupon.type != 'percent_product'){
        var valid_for_cart = true;

        if( $('#order_items_list tr').length > 0 ){
            $('#order_items_list tr').each(function(index, el) {

                if( $(el).find('td.name .product_cat_ids').length ){
                    var cat_ids = $(el).find('td.name .product_cat_ids').val();
                    if(cat_ids != ''){
                        cat_ids = JSON.parse(cat_ids);
                        
                        if( $.arrayIntersect(coupon.exclude_product_category_ids, cat_ids).length ){
                             valid_for_cart = false;
                        }                                
                    }
                }

            });
        }
        return valid_for_cart;
    }
    return true;
}

/****/
});
/** tax **/
var calc_tax,
calc_exclusive_tax,
pos_tax_rates,
pos_tax_base_country;

var taxclass = {};
jQuery(document).ready(function($) {
pos_tax_base_country = wc_pos_params.wc.base_country;
pos_tax_rates = wc_pos_params.wc.all_rates;

calc_tax = function(){
    var price    = 0;
    var tax      = 0;
    taxclass = {};    
    $('.wc_pos_register_subtotals table.woocommerce_order_items .tax_row td table').html('');
    $('#order_items_list tr.item').not('.no_taxable').each(function(index, el) {
        var _price    = parseFloat( $(el).find('input.final_total_amount').val() );
        var _taxclass = $(el).find('input.product_price').data('taxclass');
        
        rates = findItemRate(_taxclass);
        
        var tax_price = get_tax(_price, rates);        
        tax += tax_price;

    });
    if( $('select.shipping_method').length ){
        var shipping_price  = parseFloat( $('select.shipping_method option:selected').data('cost') );

        if( shipping_price > 0){
            var matched_tax_rates = [];
            var rates = findItemRate('', true);
            $.each(rates, function(key, rate) {
                 if ( typeof rate['shipping'] != 'undefined' && 'yes' === rate['shipping'] ) {
                    matched_tax_rates[ key ] = rate;
                }
            });
            var shipping_tax = calc_shipping_tax( shipping_price, matched_tax_rates );
            tax += shipping_tax;
        }
    }
    
    if( wc_pos_params.wc.tax_total_display != 'itemized'){
        var formatted_tax = accountingPOS( tax, 'formatMoney');
        var html = '<tr><th class="tax_label">'+wc_pos_params.i18n_tax+'</th><td class="tax_amount"><strong class="tax_amount">'+formatted_tax+'</strong></td></tr>';
        $('.wc_pos_register_subtotals table.woocommerce_order_items .tax_row td table').append(html);
    }else{
        $.each(taxclass, function(label, amount) {
            amount     = remove_precision( amount );
            var formatted_tax = accountingPOS( amount, 'formatMoney');
            var html = '<tr><th class="tax_label">'+label+'</th><td class="tax_amount"><strong class="tax_amount">'+formatted_tax+'</strong></td></tr>';
            $('.wc_pos_register_subtotals table.woocommerce_order_items .tax_row td table').append(html);
        });
    }
    return tax;
}
/**
 * Calculate the shipping tax using a passed array of rates.
 *
 * @param   float       Price
 * @param   array       Taxation Rate
 * @return  array
 */
function calc_shipping_tax( price, rates ) {
    var taxes = new Array();
    var wc    = wc_pos_params.wc;
    if(wc.calc_taxes == 'yes'){
        price = precision( price );
        taxes = calc_exclusive_tax( price, rates );
        // Round to precision
        if ( !wc.tax_round_at_subtotal ) {
          taxes = array_map( 'round', taxes ); // Round to precision
        }
    }
    // Remove precision
    taxes     = array_map( 'remove_precision', taxes );

    return Math.max( 0, array_sum( taxes ) );
}
function get_tax (price, rates) {
    if( typeof price == 'undefined')
        price = 0;
    var tax = 0;
    if(price > 0){
        var taxes = new Array();
        var wc    = wc_pos_params.wc;

        if(wc.calc_taxes == 'yes'){
            // Work in pence to X precision
            price = precision( price );  

            if ( wc.prices_include_tax == 'yes' )
              taxes = calc_inclusive_tax( price, rates );
            else
              taxes = calc_exclusive_tax( price, rates );

            // Round to precision
            if ( !wc.tax_round_at_subtotal ) {
              taxes = array_map( 'round', taxes ); // Round to precision
            }
            // Remove precision
            price     = remove_precision( price );
            taxes     = array_map( 'remove_precision', taxes );
        }
        tax   = Math.max( 0, array_sum( taxes ) );
    }    
    return tax;
}
function findItemRate(_taxclass, shipping){
    if(typeof _taxclass == 'undefined')
        _taxclass = '';

    var new_rates = {}; 
    var location  = {}
    switch(wc_pos_params.wc.pos_tax_based_on){
        case 'billing':
            if( $('#pos_c_user_id').length != 0 && $('#pos_c_billing_addr').length )
                location = JSON.parse($('#pos_c_billing_addr').val());
            break;
        case 'shipping':
            if( $('#pos_c_user_id').length != 0 && $('#pos_c_shipping_addr').length )
                location = JSON.parse($('#pos_c_shipping_addr').val());
            break;
        case 'base':
            location = wc_pos_params.wc.shop_location;
            break;
        default:
            location = wc_pos_params.wc.outlet_location;
            break;
    }
    var found_priority = {};
    $.each(wc_pos_params.wc.all_rates, function(index, rate) {
        if(rate.taxclass == _taxclass || shipping === true ){

            var cities    = rate.city.split(";");
            var postcodes = rate.postcode.split(";");

            if( (rate.country == location.country || rate.country == '')
            && (rate.state == location.state || rate.state == '')
            && (rate.city == '' || cities.in_array(location.city) )
            && (rate.postcode == '' || postcodes.in_array(location.postcode) ) ){

                if( typeof found_priority[rate.priority] == 'undefined'){
                    new_rates[index] = rate;
                    found_priority[rate.priority] = '1';
                }
            }

        }
    });

    return new_rates;
}

precision = function(price){
    return price * ( Math.pow( 10, wc_pos_params.wc.precision ) );
}
remove_precision = function(price){
    return price / ( Math.pow( 10, wc_pos_params.wc.precision ) );
}
calc_inclusive_tax = function(price, rates){
    var taxes = [];

    var regular_tax_rates  = 0;
    var compound_tax_rates = 0;

    $.each(rates, function(key, rate) {
        if(rate.compound == 'yes'){
            compound_tax_rates = compound_tax_rates + parseFloat(rate.rate);
        }else{
            regular_tax_rates = regular_tax_rates + parseFloat(rate.rate);
        }
    });

    var regular_tax_rate   = 1 + ( regular_tax_rates / 100 );
    var compound_tax_rate  = 1 + ( compound_tax_rates / 100 );
    var non_compound_price = price / compound_tax_rate;

    $.each(rates, function(key, rate) {
        if ( typeof taxes[key] == 'undefined' ){
            taxes[ key ] = 0;
        }
        var the_rate      = parseFloat(rate.rate) / 100;
        if ( rate.compound == 'yes' ) {
            var the_price = price;
            the_rate  = the_rate / compound_tax_rate;
        } else {
            var the_price = non_compound_price;
            the_rate      = the_rate / regular_tax_rate;
        }

        var net_price       = price - ( the_rate * the_price );
        var tax_amount      = price - net_price;
        taxes[ key ]       += tax_amount;

        if( typeof taxclass[rate.label] == 'undefined')
            taxclass[rate.label] = tax_amount;
        else
            taxclass[rate.label] += tax_amount;

    });

    return taxes;
}
calc_exclusive_tax = function(price, rates){
    var taxes = new Array();
    // Multiple taxes
    $.each(rates, function(key, rate) {
        if(rate.compound != 'yes'){
            var tax_amount = price * ( parseFloat(rate.rate) / 100 );
            if ( typeof taxes[key] == 'undefined' ){
                taxes[ key ] = tax_amount;
            }
            else{
                taxes[ key ] += tax_amount;
            }

            if( typeof taxclass[rate.label] == 'undefined')
                taxclass[rate.label] = tax_amount;
            else
                taxclass[rate.label] += tax_amount;
        }
    });
    
    var pre_compound_total = array_sum( taxes );
    
    // Compound taxes
    if ( rates ) {
        $.each(rates, function(key, rate) {
            if(rate.compound != 'no'){
               var the_price_inc_tax = price + ( pre_compound_total );
               var tax_amount = the_price_inc_tax * ( parseFloat(rate.rate) / 100 );

               // Add rate
                if ( typeof taxes[key] == 'undefined' )
                    taxes[ key ] = tax_amount;
                else
                    taxes[ key ] += tax_amount;

                if( typeof taxclass[rate.label] == 'undefined')
                    taxclass[rate.label] = tax_amount;
                else
                    taxclass[rate.label] += tax_amount;
            }
        });
    }
    return taxes;
}
function array_map(callback) {

      var argc = arguments.length,
        argv = arguments,
        glbl = this.window,
        obj = null,
        cb = callback,
        j = argv[1].length,
        i = 0,
        k = 1,
        m = 0,
        tmp = [],
        tmp_ar = [];

      while (i < j) {
        while (k < argc) {
          tmp[m++] = argv[k++][i];
        }

        m = 0;
        k = 1;

        if (callback) {
          if (typeof callback === 'string') {
            cb = glbl[callback];
          } else if (typeof callback === 'object' && callback.length) {
            obj = typeof callback[0] === 'string' ? glbl[callback[0]] : callback[0];
            if (typeof obj === 'undefined') {
              throw 'Object not found: ' + callback[0];
            }
            cb = typeof callback[1] === 'string' ? obj[callback[1]] : callback[1];
          }
          tmp_ar[i++] = cb.apply(obj, tmp);
        } else {
          tmp_ar[i++] = tmp;
        }

        tmp = [];
      }

      return tmp_ar;
    }
array_sum = function(arr){
    var total = 0;
    for(var i=0, len=arr.length; i<len; i++){
        if(typeof arr[i] != 'undefined' && isNaN(arr[i]) === false)
            total += arr[i];
    }
    return total;
}
function isInteger(nVal) {
return typeof nVal === 'number'
  && isFinite(nVal)
  && nVal > -9007199254740992
  && nVal < 9007199254740992
  && Math.floor(nVal) === nVal;
};

function ex_tax_or_vat(){
    var european_union_countries = wc_pos_params.wc.european_union_countries;
    return european_union_countries.in_array(pos_tax_base_country) ? '(ex. VAT)' : '(ex. tax)';
}
});
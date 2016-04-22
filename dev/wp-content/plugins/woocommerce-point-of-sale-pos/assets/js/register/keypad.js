var calculateDiscountKeyPad,
    calculateQuantity;
	
var amount_tendered = {
    'tendered_1' : '0.00',
    'tendered_2' : '',
    'tendered_3' : '',
    'tendered_4' : '',
  };
var tendered_num = {
    'tendered_1' : '0.00',
    'tendered_2' : '',
    'tendered_3' : '',
    'tendered_4' : '',
  };
jQuery(document).ready(function($) {
    reloadKeypad = function() {
            $('#wc-pos-register-data tr.new_row input.quantity').keypad({
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
                    POScalculateTotal($(inst.elem));
                    $('.quantity_keypad .keypad-inpval').text(val);
                    $(inst.elem).val(val);
                },
                beforeShow : function(div, inst) { 
                    var val = $(inst.elem).val();
                    $('.quantity_keypad .keypad-inpval').text(val);
                }
            }).click(function(){
                this.select();
            }).keyup(function(ev){              
                POScalculateTotal($(this));
            }).keydown(function(ev){
                $('.keypad-popup').hide();
                if ( ev.which == 13 ) {
                    ev.preventDefault();
                }
                var value = $(this).val();
                var val = inputQuantityVal(value);
                $(this).val(val);
            });

            $('#wc-pos-register-data tr.new_row td.line_cost input.product_price').keypad({
                keypadOnly: false,
                separator  : '|', 
                layout     : [ 
                    $.keypad.PRICE_VAL1+'|'+ $.keypad.PRICE_CURRENCY_SYMBOL+'|'+$.keypad.PRICE_VAL2+'|'+$.keypad.PRICE_PERCENT_SYMBOL,
                    '1|2|3|' + $.keypad.CLEAR,
                    '4|5|6|' + $.keypad.BACK,
                    '7|8|9|' + $.keypad.CLOSE,
                    '0|.|00'],
                keypadClass : 'price_keypad',
                closeText  : '',
                price_val1Text    : '0% off',
                price_currency_symbolText  : wc_pos_params.currency_format_symbol,
                price_val2Text    : '0',
                price_percent_symbolText   : '%',
                beforeShow : function(div, inst) {
                    var price   = $(inst.elem).val();
                    var percent = $(inst.elem).data('percent');
                    $(inst.elem).data('discountsymbol', 'currency_symbol');
                    $('.price_keypad .keypad-price_currency_symbol, .price_keypad .keypad-price_percent_symbol').removeClass('active');
                    $('.price_keypad .keypad-price_currency_symbol').addClass('active');

                    $('.price_keypad .keypad-price_val1').text( (percent) +'% off ');
                    $('.price_keypad .keypad-price_val2').text( price );
                },
                onClose: function (div, inst){
                    var modprice   = $(inst.elem).data('modprice');
                        modprice   = checkFormatPricePOS(modprice);
                        $(inst.elem).val(modprice);
                },
                onKeypress : function(key, value, inst) {
                    POScalculateTotal($(inst.elem));
                }
            }).click(function(){
                this.select();
            }).keyup(function(ev){
                POScalculateTotal($(this));
                return this;
            }).keydown(function(ev){
                $('.keypad-popup').hide();
                if ( ev.which == 13 ) {
                    ev.preventDefault();
                }
            }).change(function(event) {             
                POScalculateTotal($(this));            
            });        
        if(isTouchDevice()){
            $('#wc-pos-register-data tr.new_row input.quantity').attr('readonly', 'readonly');
            $('#wc-pos-register-data tr.new_row input.product_price').attr('readonly', 'readonly');
        }
    }
	
    calculateQuantity = function(input, action){
        var val = $(input).val();
        if(val == '') val = 1;
        val = parseInt(val);
        if(action == 'plus'){
            val = val +1;
        }
        if(action == 'minus'){
            val = val-1;
            if(val < 1 ) val = 1;
        }
        
        $('.quantity_keypad .keypad-inpval').text(val);
        if($('#var_tip input.quantity').length)
            $('#var_tip input.quantity').val(val);

        $(input).val(val);
    }
    calculateDiscountKeyPad = function(){

        var $keyentry     = $('#inline_order_discount .keypad-keyentry');
        var discount_prev = $keyentry.val();
        
        var discount_val  = 0;
        var percent       = 0;
        var total         = $("#inp_subtotal_amount").val();

        if(total == '') total = 0.00;
        else total = parseFloat(total).toFixed(2);

        var symbol        = $('#order_discount_symbol').val();

        $('#order_discount_prev').val(discount_prev);
        
        if (discount_prev && total) {
            discount_prev = parseFloat( discount_prev.replace("/\%/g", "") );

            if(symbol == 'percent_symbol'){
                percent      = parseInt(discount_prev);
                discount_val = (total*percent/100);
                discount_val = accountingPOS( discount_val, 'formatNumber');
                
                $('#order_discount_prev').val(percent);
                $keyentry.val(percent);
                $('.discount_keypad .keypad-discount_val1').text( discount_val );
                $('.discount_keypad .keypad-discount_val2').text( percent );
            }else{
                var price = $('#order_discount_prev').val();
                if(price != '' && price != parseInt(price) ){                    
                    var num_after_dot = price.length - price.indexOf(".")-1;
                    if(num_after_dot > wc_pos_params.currency_format_num_decimals){
                        discount_val = accountingPOS( price, 'formatNumber');
                        discount_prev =  price;
                        $('#order_discount_prev').val(discount_prev);                        
                        $keyentry.val(discount_prev);                        
                    }
                }

                percent      = (discount_prev*100/total).toFixed(2);
                discount_val = discount_prev;
                $('.discount_keypad .keypad-discount_val1').text( percent +'% off ');
                $('.discount_keypad .keypad-discount_val2').text( discount_val );
            }
        }else{
            if(symbol == 'percent_symbol'){
                discount_prev      = parseInt(discount_prev);
                if( isNaN(discount_prev) )
                    discount_prev = 0;

                $('#order_discount_prev').val(discount_prev);
                $keyentry.val(discount_prev);
                
                $('.discount_keypad .keypad-discount_val1').text( '0.00' );
                $('.discount_keypad .keypad-discount_val2').text( discount_prev );
            }else{
                var price = $('#order_discount_prev').val();
                if(price != '' && price != parseInt(price) ){                    
                    var num_after_dot = price.length - price.indexOf(".")-1;
                    if(num_after_dot > 2){
                        price = parseFloat(price);
                        price = Math.floor(100 * price) / 100;
                        discount_prev =  price;
                        $('#order_discount_prev').val(discount_prev);
                        $keyentry.val(discount_prev);
                    }
                }
                $('.discount_keypad .keypad-discount_val1').text( '0% off ' );
                $('.discount_keypad .keypad-discount_val2').text( discount_prev );                    
            }
        }
    }

	$.keypad.setDefaults({
        showAnim : 'slideDown',
        duration : 'fast'
    });

    $.keypad.addKeyDef('PLUS', 'plus', function(inst) {

        calculateQuantity(this, 'plus');
        POScalculateTotal($(inst.elem));
        this.focus();
    }); 
    $.keypad.addKeyDef('MINUS', 'minus', function(inst) {
        calculateQuantity(this, 'minus');
        POScalculateTotal($(inst.elem));
        this.focus();
    });

    $.keypad.addKeyDef('INPVAL', 'inpval', function(inst) {
        if($('.inline_quantity').length)
            $('.inline_quantity .keypad-row').show();
        else
            this.focus();
    });

    $.keypad.addKeyDef('FIVEPERCENT', 'fivepercent', function(inst) {
        $(this).val(wc_pos_params.discount_presets[0]);
        $('#order_discount_symbol').val('percent_symbol');

        $('.keypad-currency_symbol').removeClass('active');
        $('.keypad-percent_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    }); 

    $.keypad.addKeyDef('TENPERCENT', 'tenpercent', function(inst) {
        $(this).val(wc_pos_params.discount_presets[1]);
        $('#order_discount_symbol').val('percent_symbol');

        $('.keypad-currency_symbol').removeClass('active');
        $('.keypad-percent_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    });

    $.keypad.addKeyDef('FIFTEENPERCENT', 'fifteenpercent', function(inst) {
        $(this).val(wc_pos_params.discount_presets[2]);
        $('#order_discount_symbol').val('percent_symbol');

        $('.keypad-currency_symbol').removeClass('active');
        $('.keypad-percent_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    });

    $.keypad.addKeyDef('TWENTYPERCENT', 'twentypercent', function(inst) {
        $(this).val(wc_pos_params.discount_presets[3]);
        $('#order_discount_symbol').val('percent_symbol');

        $('.keypad-currency_symbol').removeClass('active');
        $('.keypad-percent_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    });

    $.keypad.addKeyDef('CURRENCY_SYMBOL', 'currency_symbol', function(inst) { 
        $('#order_discount_symbol').val('currency_symbol');

        $('.keypad-percent_symbol').removeClass('active');
        $('.keypad-currency_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    });

    $.keypad.addKeyDef('PERCENT_SYMBOL', 'percent_symbol', function(inst) { 
        $('#order_discount_symbol').val('percent_symbol');
        
        $('.keypad-currency_symbol').removeClass('active');
        $('.keypad-percent_symbol').addClass('active');
        calculateDiscountKeyPad();
        this.focus();
    });

    $.keypad.addKeyDef('PRICE_CURRENCY_SYMBOL', 'price_currency_symbol', function(inst) {
        this.select();
        if( $('.keypad-price_currency_symbol').hasClass('active') ) return; 

        var percent   = $(inst.elem).data('percent');
        var modprice  = $(inst.elem).data('modprice');

        $(inst.elem).val(modprice);

        $('.price_keypad .keypad-price_val1').text( (percent) +'% off ' );
        $('.price_keypad .keypad-price_val2').text( modprice ); 

        $(inst.elem).data('discountsymbol', 'currency_symbol');
        $('.keypad-price_percent_symbol').removeClass('active');
        $('.keypad-price_currency_symbol').addClass('active');

        $(inst.elem).focus();
    });

    $.keypad.addKeyDef('PRICE_PERCENT_SYMBOL', 'price_percent_symbol', function(inst) {
        this.select();
        if( $('.keypad-price_percent_symbol').hasClass('active') ) return;

        var percent   = $(inst.elem).data('percent');
        var modprice  = $(inst.elem).data('modprice');

        $(inst.elem).val(percent);
        modprice  = accountingPOS(modprice, 'formatMoney');

        $('.price_keypad .keypad-price_val1').text( modprice );
        $('.price_keypad .keypad-price_val2').text( percent );            
        $(inst.elem).data('discountsymbol', 'percent_symbol');
        $('.keypad-price_currency_symbol').removeClass('active');
        $('.keypad-price_percent_symbol').addClass('active');

        $(inst.elem).focus();
    });

    $.keypad.addKeyDef('DISCOUNT_VAL1', 'discount_val1', function(inst) {this.focus();});

    $.keypad.addKeyDef('DISCOUNT_VAL2', 'discount_val2', function(inst) {this.focus();});

    $.keypad.addKeyDef('PRICE_VAL1', 'price_val1', function(inst) {this.focus();});

    $.keypad.addKeyDef('PRICE_VAL2', 'price_val2', function(inst) {this.focus();});

    $.keypad.addKeyDef('TENDERED_1', 'tendered_1', function(inst) {
        $(this).val(tendered_num.tendered_1);
        $("#amount_pay_cod").val(tendered_num.tendered_1).change();
        $("#amount_pay_cod").focus();
    });
    $.keypad.addKeyDef('TENDERED_2', 'tendered_2', function(inst) {
        $(this).val(tendered_num.tendered_2).change();
        $("#amount_pay_cod").val(tendered_num.tendered_2).change();
        $("#amount_pay_cod").focus();
    });
    $.keypad.addKeyDef('TENDERED_3', 'tendered_3', function(inst) {
        $(this).val(tendered_num.tendered_3);
        $("#amount_pay_cod").val(tendered_num.tendered_3).change();
        $("#amount_pay_cod").focus();
    });
    $.keypad.addKeyDef('TENDERED_4', 'tendered_4', function(inst) {
        $(this).val(tendered_num.tendered_4);
        $("#amount_pay_cod").val(tendered_num.tendered_4).change();
        $("#amount_pay_cod").focus();
    });
	
	if ($('#custom_product_price').length > 0 ){
		custom_product();
	}
	if ($('#inline_amount_tendered').length > 0 ){
		amount_tendered();
	}
	if ($('#order_discount_prev').length > 0 ){
		order_discount();
	}
	$('#amount_pay_cod').on('change', function() {
	    updateChangeCOD();
	});

    if(isTouchDevice()){
        $('#custom_product_price, #custom_product_quantity, #amount_pay_cod, #custom_shipping_price').attr('readonly', 'readonly');
    }

	function order_discount () {
		$('#inline_order_discount').keypad({
            keypadOnly: false,
            beforeShow : function(div, inst) { 
                var order_discount_symbol  = $('#order_discount_symbol').val();
                $('.discount_keypad .keypad-currency_symbol, .discount_keypad .keypad-percent_symbol').removeClass('active');
                $('.discount_keypad .keypad-'+order_discount_symbol).addClass('active');
                calculateDiscountKeyPad();
            },
            onKeypress : calculateDiscountKeyPad,

            keypadClass : 'discount_keypad',
            separator: '|',
            layout: [
                $.keypad.CURRENCY_SYMBOL+'|'+$.keypad.DISCOUNT_VAL2+'|'+$.keypad.PERCENT_SYMBOL+'|'+ $.keypad.DISCOUNT_VAL1,
                '1|2|3|' + $.keypad.CLEAR +'|' + $.keypad.FIVEPERCENT,
                '4|5|6|' + $.keypad.BACK+'|' + $.keypad.TENPERCENT,
                '7|8|9|' + $.keypad.CLOSE + '|' + $.keypad.FIFTEENPERCENT,
                '0|.|00|' + $.keypad.TWENTYPERCENT
                ],
            closeText  : '',

            discount_val1Text    : '0% off',
            currency_symbolText  : wc_pos_params.currency_format_symbol,
            discount_val2Text    : '0',
            percent_symbolText   : '%',

            discount_val1Status   : '',
            currency_symbolStatus : '',
            discount_val2Status   : '',
            percent_symbolStatus  : '',

            fivepercentText    : wc_pos_params.discount_presets[0]+'%',
            tenpercentText     : wc_pos_params.discount_presets[1]+'%',
            fifteenpercentText : wc_pos_params.discount_presets[2]+'%',
            twentypercentText  : wc_pos_params.discount_presets[3]+'%',

            fivepercentStatus    : '',
            tenpercentStatus     : '',
            fifteenpercentStatus : '',
            twentypercentStatus  : '',
        });
	}

	function custom_product () {
		$('#custom_product_quantity').keypad({
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
                $(inst.elem).val(val);
                $('.quantity_keypad .keypad-inpval').text(val);
            },
            beforeShow : function(div, inst) {
                var val = $(inst.elem).val();
                $('.quantity_keypad .keypad-inpval').text(val);
            }
        }).click(function(){
            this.select();
        }).keydown(function(ev){
            $('.keypad-popup').hide();
            if ( ev.which == 13 ) {
                ev.preventDefault();
            }
            var val = inputQuantityVal(this.value);
            $(this).val(val);
        });
        $('#custom_product_price, #custom_shipping_price').keypad({
            keypadOnly: false,
            separator  : '|', 
            layout     : [ '1|2|3|' + $.keypad.CLEAR, '4|5|6|' + $.keypad.BACK, '7|8|9|' + $.keypad.CLOSE, '0|.|00'],
            closeText  : '',
            onKeypress : function(key, value, inst) { 
                var price = checkFormatPricePOS(value);
                $(inst._input).val(price);
            }
        }).click(function(){
            this.select();
        }).keyup(function(ev){
            var price = $(this).val();
        		price = checkFormatPricePOS(price);
            this.value =  price;
            return this;
        }).keydown(function(ev){
            $('.keypad-popup').hide();
            if ( ev.which == 13 ) {
                ev.preventDefault();
            }
        });
	}

    function amount_tendered () {
    	$('#amount_pay_cod').click(function(){
	        this.select();
	    }).keyup(function(ev){
	        var price      = $(this).val();
	    		price      = checkFormatPricePOS(price);
                this.value =  price;
                updateChangeCOD();
	    });
	    $('#inline_amount_tendered').keypad({
            onKeypress : function(key, value, inst) { 
                var price = checkFormatPricePOS(value);
                $(inst._input).val(price);
                $('#amount_pay_cod').val(price);
                updateChangeCOD();
            },
            keypadOnly: false,
            keypadClass : 'amount_pay_keypad',
            separator: '|',
            layout: [
              '1|2|3|' + $.keypad.CLEAR +'|' + $.keypad.TENDERED_1,
              '4|5|6|' + $.keypad.BACK +'|' + $.keypad.TENDERED_2,
              '7|8|9|' + $.keypad.CLOSE +'|' + $.keypad.TENDERED_3,
              '0|.|00|' + $.keypad.TENDERED_4
              ],
            closeText      : '',
            tendered_1Text : '',
            tendered_2Text : '',
            tendered_3Text : '',
            tendered_4Text : '',

            tendered_1Status : '',
            tendered_2Status : '',
            tendered_3Status : '',
            tendered_4Status : '',
        });
    }

	function updateChangeCOD(){
	   var total_amount = parseFloat($("#show_total_amt_inp").val());

	    var amount_pay = $('#amount_pay_cod').val();
	    var change = amount_pay - total_amount;
	    var change = change.toFixed(wc_pos_params.currency_format_num_decimals);
	    if (amount_pay != '') {
	        $('#amount_change_cod').val(0);
	    }
	    if (amount_pay > total_amount) {
	        $('#amount_change_cod').val(change);
	    } else {
	        $('#amount_change_cod').val(0);
	    } 
	}
});
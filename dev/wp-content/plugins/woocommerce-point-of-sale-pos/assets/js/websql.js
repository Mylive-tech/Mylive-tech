    const DB_NAME          = 'wc_point_of_sale';
    const DB_DISPLAY_NAME  = 'Woocommerce Point Of Sale';
    
    const DB_STORE_NAME    = 'pos_products';
    const DB_STORE_VERSION = '2.0';
    const MAX_SIZE         = 10485760;
    var db;


function openDb() {
    pos_debug( "Loading products" );

    try {
        if (!window.openDatabase) {
            alert('not supported');
        } else {
            var data = {
                action      : 'wc_pos_json_search_products_all',
                version     : 'websql'
            };
            var productsData;
            jQuery.ajax({
              type: 'GET',
              async: true,
              url: wc_pos_params.wc_api_url+'products/',
              data: data,
              timeout: 120000,
              success: function(response) {
                  if (response) {
                    pos_debug( "Open Database. Version 2.0" );
                    db = window.openDatabase(DB_NAME, '2.0', DB_DISPLAY_NAME, MAX_SIZE);

                   /* try {
                        db.changeVersion(db.version, '2.0', function(t){
                          t.executeSql("ALTER TABLE "+DB_STORE_NAME+" ADD COLUMN categories_ids");
                        });
                    } catch(e) {
                        alert('changeversion 1.0 -> 2.0 failed');
                        alert('DB Version: '+db.version);
                    }    */
                    createTables(db, response.products);
                  }
              },
              error: function(response) {
                var responseText = JSON.parse(response.responseText);
                    if(typeof responseText.errors != undefined){
                        errors_message = responseText.errors.message;
                    }else{
                        errors_message = response.responseText;
                    }
                    pos_debug( errors_message );
                    displayLocked(errors_message);
              }
            });
            
            // You should have a database instance in db.
        }
    } catch(e) {
        // Error handling code goes here.
        if (e == 2) {
            // Version number mismatch.
            alert("Invalid database version.");
        } else {
            alert("Unknown error "+e+".");
        }
        return;
    }
    

     
}
function pos_debug( msg, type ) {
        if ( window.console && window.console.log && wc_pos_params.wp_debug == true ) {
            window.console.log( msg );
        }
        if( jQuery('#process_loding').length ){
            if( typeof msg == 'string' && msg != ''){
                if( type == false)
                    jQuery('#process_loding').append('<p>'+msg+'</p>');
                else
                    jQuery('#process_loding').append('<p>'+msg+'<span class="dot one">.</span><span class="dot two">.</span><span class="dot three">.</span>​</p>');
            }
            jQuery('#process_loding').scrollTop( jQuery('#process_loding')[0].scrollHeight);
        }
}
function createTables(db, productsData)
{   
    db.transaction(
        function (transaction) {
                
            /* The first query causes the transaction to (intentionally) fail if the table exists. */
            transaction.executeSql('CREATE TABLE IF NOT EXISTS '+DB_STORE_NAME+'\
                (_id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,\
                id INTEGER NOT NULL DEFAULT 0,\
                parent_id INTEGER NOT NULL DEFAULT 0,\
                stock_quantity INTEGER NOT NULL  DEFAULT 0,\
                pr_excl_tax FLOAT NOT NULL DEFAULT 0.00,\
                pr_inc_tax FLOAT NOT NULL DEFAULT 0.00,\
                attributes TEXT NOT NULL DEFAULT "",\
                parent_attr TEXT NOT NULL DEFAULT "",\
                title TEXT NOT NULL DEFAULT "",\
                barcode TEXT NOT NULL DEFAULT "",\
                f_title TEXT NOT NULL DEFAULT "",\
                featured_src TEXT NOT NULL DEFAULT "",\
                price TEXT NOT NULL DEFAULT "",\
                price_html TEXT NOT NULL DEFAULT "",\
                in_stock BOOLEAN NULL DEFAULT false,\
                categories_ids TEXT NOT NULL DEFAULT "",\
                taxable BOOLEAN NULL DEFAULT false,\
                sale_price TEXT NOT NULL DEFAULT "",\
                regular_price TEXT NOT NULL DEFAULT ""\
                );', [], nullDataHandler, errorHandler);
            /* These insertions will be skipped if the table already exists. */
            
            transaction.executeSql('DELETE FROM '+DB_STORE_NAME+';');

            pos_debug( "Adding products to database" );
            var ProductsCount = productsData.length;
            jQuery.each(productsData, function(i, data){
                pos_debug( 'Loading '+ i +' of '+ ProductsCount + ' products' );
                if(typeof data.id != 'undefined' && typeof data.f_title != 'undefined'){

                    if(typeof data.variations != 'undefined' && data.type == 'variable'){
                        jQuery.each(data.variations, function(j, var_data){
                            
                            var_data.attributes     = JSON.stringify(var_data.attributes);
                            var_data.parent_attr    = JSON.stringify(var_data.parent_attr);
                            var_data.categories_ids = JSON.stringify(var_data.categories_ids);

                            if(typeof var_data.parent_id == 'undefined')
                                var_data.parent_id = 0;

                            if( var_data.regular_price == 'null' || var_data.regular_price == null)
                                var_data.regular_price = '';
                            
                            if( var_data.sale_price == 'null' || var_data.sale_price == null)
                                var_data.sale_price = var_data.regular_price;


                            var ar_data = new Array( var_data.id, var_data.parent_id, var_data.stock_quantity, var_data.pr_excl_tax, var_data.pr_inc_tax, var_data.attributes, var_data.parent_attr, var_data.title, var_data.barcode, var_data.f_title, var_data.featured_src, var_data.price, var_data.price_html, var_data.in_stock, var_data.categories_ids, var_data.taxable, var_data.sale_price, var_data.regular_price);

                            transaction.executeSql('INSERT INTO '+DB_STORE_NAME+' (id, parent_id, stock_quantity, pr_excl_tax, pr_inc_tax, attributes, parent_attr, title, barcode, f_title, featured_src, price, price_html, in_stock, categories_ids, taxable, sale_price, regular_price ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );', ar_data, nullDataHandler, errorHandler);

                        });
                    }
                    else
                    {
                        data.attributes     = JSON.stringify(data.attributes);
                        data.parent_attr    = JSON.stringify(data.parent_attr);
                        data.categories_ids = JSON.stringify(data.categories_ids);
                        
                        if(typeof data.parent_id == 'undefined')
                            data.parent_id = 0;
                        
                        if(typeof data.barcode == 'undefined') data.barcode = ''; 

                        var title = data.title;
                        if(typeof title == 'undefined')
                            title = '';

                        if( data.regular_price == 'null' || data.regular_price == null)
                            data.regular_price = '';

                        if( data.sale_price == 'null' || data.sale_price == null)
                            data.sale_price = data.regular_price;

                        var ar_data = new Array( data.id, data.parent_id, data.stock_quantity, data.pr_excl_tax, data.pr_inc_tax, data.attributes, data.parent_attr, data.title, data.barcode, data.f_title, data.featured_src, data.price, data.price_html, data.in_stock, data.categories_ids, data.taxable, data.sale_price, data.regular_price);

                        transaction.executeSql('INSERT INTO '+DB_STORE_NAME+' (id, parent_id, stock_quantity, pr_excl_tax, pr_inc_tax, attributes, parent_attr, title, barcode, f_title, featured_src, price, price_html, in_stock, categories_ids, taxable, sale_price, regular_price ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );', ar_data, nullDataHandler, errorHandler);

                    }
                }else{
                    console.log(data);
                }
            });

            console.log("createTables END");
            displayUnLocked();
            addEventListeners_webSql();            
        }
    ); 
}
function displayActionSuccess(msg) {
    msg = typeof msg != 'undefined' ? "Success: " + msg : "Success";
    $('#msg').html('<span class="action-success">' + msg + '</span>');
}


function displayLocked(msg) {
    // If some other tab is loaded with the database, then it needs to be closed
    // before we can proceed.
    var msg = msg;
    if(msg == '') msg = wc_pos_params.open_another_tab;
    jQuery('document').ready(function($) {
        $('#post-lock-dialog .post-taken-over').html("<p>"+msg+"</p>");
        $('#post-lock-dialog').show();
    });
    console.log("Please close all other tabs with this site open!");
}
function displayUnLocked() {
    console.log("displayUnLocked");
    jQuery('document').ready(function($) {
        if( jQuery('.post-locked-message.not_close').length == 0 ){
            jQuery('#post-lock-dialog').hide();            
        }
    });
}
function displayActionFailure(msg) {
    msg = typeof msg != 'undefined' ? "Failure: " + msg : "Failure";
    $('#msg').html('<span class="action-failure">' + msg + '</span>');
}
function resetActionStatus() {
    console.log("resetActionStatus ...");
    $('#msg').empty();
    console.log("resetActionStatus DONE");
}
function addEventListeners_webSql() {
    console.log("addEventListeners_webSql");
    jQuery('document').ready(function($) {
        $('#wc-pos-register-grids').on('click', '.add_grid_tile', function() {
            var pid = parseInt($(this).find('a').attr('data-id'));
            addProduct({pid: pid, element: $(this)});

            return false;
        });
        
        $('body').on('click', '#add_pr_variantion', function() {
            var all_variations = $('#var_tip').data( 'product_variations' ),
                exit    = false,
                showing_variation = false,
                current_settings = {},
                humaniz_settings = {},
                exclude = '',
                quantity          = $('#var_tip input.quantity' ).length ? parseInt($('#var_tip input.quantity' ).val()) : 1;

            if(!quantity || quantity == '')
                quantity = 1;

            if( $('#var_tip select' ).length ){

                $('#var_tip select' ).each( function() {

                    if ( $( this ).val().length === 0 ) {
                        exit = true;
                    }

                    // Encode entities
                    value = $( this ).val();
                    // Add to settings array
                    current_settings[ 'attribute_'+$( this ).closest('div').data( 'taxonomy' ) ] = value;
                    var label   = $( this ).closest('div').data( 'label' );
                    var slug    = $( this ).closest('div').data( 'slug' );
                    var v_label = $( this ).find('option[value="'+value+'"]').text();
                    humaniz_settings[ slug ] = {};
                    humaniz_settings[ slug ]['label']    = label;
                    humaniz_settings[ slug ]['value']    = value;
                    humaniz_settings[ slug ]['v_label']  = v_label;
                    humaniz_settings[ slug ]['taxonomy'] = 'attribute_'+$( this ).closest('div').data( 'taxonomy' );

                });
                if(exit) return false;
                var matching_variations = find_matching_variations( all_variations, current_settings );
                var variation = matching_variations.shift();
            }else{
                var id = $('#var_tip').data( 'id' );
                
                addProduct({pid: id, element: 'no', quantity: quantity});
                if(wc_pos_params.instant_quantity == 'yes' || $('#var_tip select').length ){
                    $('#var_tip').hide();
                }
                return false;
            }
            if ( variation ) {

                addProduct({pid: variation.variation_id, element:'no', humaniz_settings: humaniz_settings, quantity: quantity});

            }else{
                toastr.error(wc_pos_params.cannot_be_purchased);
            }
            if(wc_pos_params.instant_quantity == 'yes' || $('#var_tip select').length ){
                $('#var_tip').hide();
            }
            return false;
        });
        $('body').on('click', '#add_pr_variantion_popup', function() {

            var product_id     = parseInt( $('#popup_choose_attributes_inner').data('id') )
                humaniz_settings = {};

            $('#popup_choose_attributes_inner select' ).each( function() {

                // Encode entities
                value = $( this ).val();
                // Add to settings array
                var label   = $( this ).data( 'label' );
                var v_label = $( this ).find('option[value="'+value+'"]').text();
                humaniz_settings[ label ] = {};
                humaniz_settings[ label ]['label']    = label;
                humaniz_settings[ label ]['value']    = value;
                humaniz_settings[ label ]['v_label']  = v_label;
                humaniz_settings[ label ]['taxonomy'] = 'attribute_'+$( this ).data( 'taxonomy' );
            });

            addProduct({pid: product_id, element:'no', humaniz_settings: humaniz_settings});
            $('#popup_choose_attributes').hide();
            return false;
        });
        $('#wc-pos-register-data').on('click', '.remove_order_item', function() {
            var $el = $(this).closest('tr');
            $el.remove();
            $('#tiptip_holder').hide().css( {margin: '-100px 0 0 -100px'});
            POScalculateTotal();
            return false;
        });
        $('#wc-pos-register-data').on('change', '#add_product_id', function() {
            var ids_products = $('#add_product_id').val();
            $('#add_product_id').select2('val', '', false);
            if (ids_products && ids_products != '') {                
                addProduct({pid: ids_products, element: 'no'});
            }
        });
        
        if( wc_pos_params.ready_to_scan == 'yes') {
            $(document).anysearch({
                searchSlider: false,
                isBarcode: function(barcode) {
                    searchProduct(barcode);
                },
                searchFunc: function(search) {
                    searchProduct(search);
                },
            });
        }
        function searchProduct (code) {
            code = code.trim();
            if (code != ''){
                db.transaction(function(tx) {
                    tx.executeSql("SELECT * FROM "+DB_STORE_NAME +" WHERE barcode = '"+code+"'", [], function(tx, result){
                        if(typeof result == 'undefined'){
                                toastr.error(wc_pos_params.cannot_be_purchased);
                                return;
                            }
                            var item = result.rows.item(0);
                            if(typeof item == 'undefined' || parseFloat(item.price) < 0){
                                toastr.error(wc_pos_params.cannot_be_purchased);
                                return;
                            }
                            
                            addProduct({pid: item.id, element: 'no'});
                    }, function(tx, error){});
                });
            }else{
                toastr.error(wc_pos_params.cannot_be_purchased);
                return;
            }
        }

        $('#wc-pos-register-data').on('change', 'input.quantity', function() {
            var pid = $(this).parents('tr.item').find('input.product_item_id').val();
            POScalculateTotal();
        });

    });

}
function addProduct(option) {
    
    if( (wc_pos_params.instant_quantity == 'yes' && option.element != 'no') || (option.element != 'no' && option.element.find('.hidden').length > 0 )  ){
            runVariantion(option.element);
            return;
    }

    db.transaction(function(tx) {
        
        tx.executeSql("SELECT * FROM "+DB_STORE_NAME +" WHERE id = "+option.pid, [], function(tx, result) {
        
            for(var i = 0; i < result.rows.length; i++) {
                var item = result.rows.item(i);
                if(typeof result.rows == 'undefined'){
                    toastr.error(wc_pos_params.cannot_be_purchased);
                    return;
                }

                var attr       = {};
                var row_class  = 'product_id_' + item.id;
                var exit = false;
                var need_attributes = {};

                if(item.attributes != ""){
                    var v_data = JSON.parse(item.attributes);
                    
                    jQuery.each(v_data, function(index, val) {
                        var hidden = '';
                        if(val["option"] == ''){
                            if(typeof option.humaniz_settings != 'undefined' ){
                                if( option.humaniz_settings[val['name']] ){

                                    attr[index] = {};
                                    attr[index]['label']     = option.humaniz_settings[ val['name'] ].label;
                                    attr[index]['attribute'] = option.humaniz_settings[ val['name'] ].value;
                                    attr[index]['taxonomy']  = option.humaniz_settings[ val['name'] ].taxonomy; 

                                }
                            }else{
                                need_attributes[ val['name'] ] = true;
                                exit = true;
                                return
                            }
                        }else{
                            var v_label = val["option"];
                            if(typeof item.parent_attr == 'string'){
                                var product_attributes = jQuery.parseJSON(item.parent_attr);
                            }else{
                                var product_attributes = item.parent_attr;
                            }
                            for(var att in product_attributes){

                                if (product_attributes.hasOwnProperty(att) ){
                                    var attribute = product_attributes[att];
                                    

                                    var a = attribute.name.toLowerCase();
                                    var b = val['name'].toLowerCase();
                                    var c = attribute.title.toLowerCase();
                                    var d = val['title'].toLowerCase();

                                    if( a == b || c == d ){

                                        attr[index] = {};
                                        attr[index]['label']     = attribute.title;
                                        attr[index]['attribute'] = val["option"];
                                        attr[index]['taxonomy']  = 'attribute_'+attribute.taxonomy;

                                    }

                                }

                            }
                        }

                        row_class += '_' + attr[index]['attribute'];
                    }); 
                }
            if(exit){
                popupChooseAttributes(item, need_attributes);
                return;
            }

            var new_item = jQuery('#wc-pos-register-data #order_items_list .' + hex_md5(row_class));

            var stock = item.stock_quantity;
            if (new_item.length > 0) {

                var el = new_item.find('input.quantity');
                var qt = parseInt(el.val());
                if(el.val() == '') qt = 0;
                qt = qt+1;

                if(typeof(option.quantity) != 'undefined')
                    qt = qt+option.quantity;
                else
                    qt = qt+1;

                if(item.in_stock === 'false'){
                    toastr.error(wc_pos_params.out_of_stock);
                    ion.sound.play("error");
                    return;
                }else if( stock < qt && stock != '' ){
                    var txt = wc_pos_params.cannot_add_product.replace('%NAME%', item.title);
                        txt = txt.replace('%COUNT%', stock);
                     toastr.error(txt);

                     return;
                }
                el.val(qt);
                POScalculateTotal();
            }else{
                var qt = 1;
                if(typeof(option.quantity) != 'undefined')
                    qt = option.quantity;
                if(item.in_stock === 'false'){
                    toastr.error(wc_pos_params.out_of_stock);
                    ion.sound.play("error");
                    return;
                }else if( stock < 1 && stock != '' ){
                    var txt = wc_pos_params.cannot_add_product.replace('%NAME%', item.title);
                        txt = txt.replace('%COUNT%', stock);
                     toastr.error(txt);
                     return;
                }
                var variation = '';

                var tip = '<strong>Product ID:</strong>'+item.id+'<br>';
                var title = '';
                if(item.barcode != ''){
                    title += item.barcode + " &ndash; ";
                    tip += '<strong>Product SKU:</strong>'+item.barcode+'<br>';
                }
                title += '<a href="'+wc_pos_params.admin_url+'/post.php?post='+item.id+'&amp;action=edit" >'+item.title+'</a>';

                var parent_id = ( typeof item.parent_id != 'undefined' && item.parent_id > 0 ? item.parent_id : item.id);
                var cat_ids = ( typeof item.categories_ids != 'undefined' ? item.categories_ids : '');
                var stock_qty = false;
                if( typeof item.managing_stock  != 'undefined'){
                    stock_qty = item.managing_stock === true ? item.stock_quantity : false;
                }else if(typeof item.in_stock  != 'undefined' && item.in_stock === true && item.stock_quantity > 0){
                    stock_qty = item.in_stock === true ? item.stock_quantity : false;                    
                }
                
                POSaddProduct({
                    img        : item.featured_src,
                    id         : item.id,
                    href       : wc_pos_params.admin_url+'/post.php?post='+parent_id+'&amp;action=edit',
                    barcode    : item.barcode,
                    title      : item.title,
                    attributes : attr,
                    parent_id  : parent_id,
                    cat_ids    : cat_ids,
                    price      : item.price,
                    quantity   : qt,
                    taxable    : item.taxable,
                    taxclass   : item.tax_class,
                    stock_qty  : stock_qty,
                    row_class  : row_class,
                    sale_price : item.sale_price,
                    regular_price : item.regular_price,
                });
                
            }
        }
            
        }, null)
    });    
}

function errorHandler(transaction, error)
{
    // error.message is a human-readable string.
    // error.code is a numeric error code
    
    alert('Oops.  Error was '+error.message+' (Code '+error.code+')');
 
    // Handle errors here
    var we_think_this_error_is_fatal = true;
    if (we_think_this_error_is_fatal) return true;
    return false;
}
 
function dataHandler(transaction, results)
{
    // Handle the results
    var string = "Green shirt list contains the following people:\n\n";
    for (var i=0; i<results.rows.length; i++) {
        // Each row is a standard JavaScript array indexed by
        // column names.
        var row = results.rows.item(i);
        string = string + row['title'] + " (ID "+row['id']+")\n";
    }
    alert(string);
}
function nullDataHandler(transaction, results) { }
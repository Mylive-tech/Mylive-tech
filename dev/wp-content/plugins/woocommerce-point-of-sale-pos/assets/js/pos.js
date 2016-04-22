window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction;
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;

var pos_db;
var ll;
var lastUpdate = checkCookie();
var lastOffset = checkCookieOffset();
(function ( $ ) {
    var settings = {
                    db_old        : 'WC-Point-Of-Sale_',
                    db_name       : 'WC-Point-Of-Sale_v3',
                    db_version    : 2,
                    db_store_name : 'products',
                    errors_message: "Couldn't open database"
                };
 
    $.pointofsale = function( method ) {
        Ladda.bind( '#sync_data', {
        callback: function( instance ) {
        var progress = 0;
        var interval = setInterval( function() {
            progress = Math.min( progress + Math.random() * 0.1, 1 );
            instance.setProgress( progress );
            if( progress === 1 ) {
            instance.stop();
            clearInterval( interval );
            }
            }, 200 );
            }
        } ); 
      

        if( jQuery('.post-locked-message.not_close').length > 0 || $('#edit_wc_pos_registers').length == 0)
            return;
        
        if (!window.indexedDB){
            window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
            return;
        }

        if ( methods[method] ) {
          return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
          return methods.init.apply( this, arguments );
        } else {
          $.error( method + ' does not exist' );
        }

    };
    var onupgradeneeded = false;
    var posready = false;
    var methods = {
        init : function( options ) { 
            settings    = $.extend(settings, options );
            methods.openDb();
        },
        openDb: function() {
            var _DB_NAME       = 'WC_POS_check_duplicate';
            var _DB_VERSION    = '1';
            var _DB_STORE_NAME = 'check_duplicate';            
            
            // Let us open our database
            var del = window.indexedDB.deleteDatabase(settings.db_old);
            var del = window.indexedDB.deleteDatabase(_DB_NAME);
            var blocked = false;
            del.onblocked = function(event) {
              blocked = true;
              locked('');
            };
            del.onsuccess = function (event) {
                var req_check = indexedDB.open(_DB_NAME);
                req_check.onupgradeneeded = function (evt) {
                    var db = this.result;
                    var store = db.createObjectStore( _DB_STORE_NAME, { keyPath: "id" });
                    var myVar = setInterval(function(){ _timer() }, 1000);
                    var i = 0;
                    function _timer() {
                        var productsData = {'id': i, 'test': 10+i, 'test2': 11+i};
                        i++;
                        var tx = db.transaction(_DB_STORE_NAME, 'readwrite');
                        store =  tx.objectStore(_DB_STORE_NAME);
                        store.add(productsData);
                        if(i == 2000)
                            clearInterval(myVar);
                    }
                }
                if(!blocked){
                    //var req = indexedDB.deleteDatabase(settings.db_name);
                    var request = window.indexedDB.open(settings.db_name, settings.db_version);
                    request.onsuccess = function (evt) {
                        
                        debug( "Open Database. Version "+settings.db_version );
                        pos_db = evt.target.result;  
                        
                        var curentVersion = parseInt(evt.target.result.version) || 0;
                        if(curentVersion === settings.db_version && !onupgradeneeded){
                            
                            debug( ['onupgradeneeded', onupgradeneeded] );

                            methods.ready();
                            methods.update();
                        }
                    }
                    request.onerror = function (evt) {
                        locked(settings.errors_message);
                        console.error("openDb:", evt.target.errorCode);
                    }
                    request.onupgradeneeded = function(event) {

                        debug( "Create Database");
                        lastUpdate = 'null';
                        onupgradeneeded = true;
                        pos_db = event.target.result;
                        var newVersion = event.newVersion,
                            oldVersion = event.oldVersion;
                        settings.oldVersion = oldVersion;

                        if(oldVersion > 99999999999)
                            oldVersion = 0;

                        if(oldVersion < 2 && oldVersion != 0 ){
                            lastUpdate = null;
                            methods.update();
                        }else{
                            methods.create();
                        }
                    };
                }
            }

            
            
        },
        getServerProductIds: function() {
            var e = $.getJSON(wc_pos_params.ajax_url, {
                action: "wc_pos_get_server_product_ids"
            });
            return e;
        },
        getServerProducts :function(limit, offset){
            var filter = "?filter[limit]=" + limit + "&filter[offset]=" + offset + "&filter[updated_at_min]="+lastUpdate;
            var e = $.getJSON(wc_pos_params.wc_api_url+'products'+filter, {
                action : "wc_pos_json_search_products_all",
            });
            return e;
        },
        getServerProductsCount :function(){            
            var filter = "?filter[updated_at_min]="+lastUpdate;
            var e = $.getJSON(wc_pos_params.wc_api_url+'products/count'+filter);
            return e;
        },
        ready: function() {
            var store     = methods.getObjectStore('readonly');
            var cursorReq = store.openCursor();
                store_data = [];
            cursorReq.onsuccess = function(evt){
                var cursor = evt.target.result;
                if (cursor) {
                    var test = methods.getObj_products(cursor.value);
                    if(test){
                        if( typeof test == 'array'){
                           $.each(test, function(index, val) {
                                store_data.push( val );
                            }); 
                        }else if(typeof test == 'object'){
                            store_data.push( test );        
                        }
                    }
                    cursor.continue();
                } else {
                    if(!posready){
                        posready = true;
                        displayUnLocked();
                        addEventListeners_indexedDB();
                    }
                }
            }            
        },
        update: function(){
            debug( 'Checking new products' );
            $.when(methods.getServerProductsCount()).then(function(ProductsCount) {
              if(parseInt(ProductsCount.count) > 0){
                debug( ProductsCount.count + ' new products', false );
                methods.insertUpdate(40, lastOffset, ProductsCount.count);
              }else{
                debug('No new products', false);
                lastUpdate = _formatLastUpdateFilter();
                lastOffset = 0;
                setCookie('pos_LastOffset',lastOffset,5);
                methods.hideBlockWin();
              }
              methods.canHide();
            }); 
        },
        getObjectStore: function(mode) {
            var tx = pos_db.transaction(settings.db_store_name, mode);
            return tx.objectStore(settings.db_store_name);
        }, 
        create: function(){
            var objectStore = pos_db.createObjectStore( settings.db_store_name, { keyPath: "id" });            
            debug( 'Loading products count' );
            objectStore.createIndex("title", "title", { unique: false });
            objectStore.createIndex("barcode", "barcode", { unique: false });
            $.when(methods.getServerProductsCount()).then(function(ProductsCount) {
                //$('#wc_pos_loading_products_lock').text('(0/'+ProductsCount.count+')');
                debug( 'The store has '+ ProductsCount.count + ' products.', false );
                methods.insert(40, 0, ProductsCount.count);
            });            
        },
        insertUpdate: function(limit, offset, ProductsCount){
            var d = limit + offset;
            if(ProductsCount < d)
                d = ProductsCount;

            debug( 'Loading '+ d +' of '+ ProductsCount + ' products' );

            $.when(methods.getServerProducts(limit, offset)).then(function(productsData) {
                if(productsData.products.length > 0){
                  var objectStore   = methods.getObjectStore('readwrite');
                    
                    $.each(productsData.products, function(i, data){
                        var html_products = '';
                        
                        if(typeof data.id != 'undefined'){

                            if(typeof data.f_title != 'undefined')
                                data.f_title = data.title;
                                if(typeof data.variations != 'undefined' && data.type == 'variable'){                                  
                                    jQuery.each(data.variations, function(j, var_data){

                                      var_data.attributes = JSON.stringify(var_data.attributes); 

                                      var dd = objectStore.get( var_data.id );
                                      dd.onsuccess = function(event) {
                                        if(typeof event.target.result == 'undefined' ) 
                                            objectStore.add(var_data);
                                        else
                                            objectStore.put(var_data);          

                                        var result = $.grep(store_data, function(e, i){
                                            if( parseInt(e.id) == parseInt(var_data.id) ){
                                                var title = $('<div></div>').html(var_data.f_title).text();
                                                store_data[i] = { id: var_data.id, text: title };
                                                return true;
                                            }
                                            return false;
                                        });

                                        if(result.length == 0){
                                            var title = $('<div></div>').html(var_data.f_title).text();
                                            store_data.push( { id: var_data.id, text: title } );
                                        }

                                      }
                                    });
                                }
                                else
                                {
                                  var dd = objectStore.get( data.id );
                                  dd.onsuccess = function(event) {
                                    data.attributes = JSON.stringify(data.attributes);                
                                    
                                    if(typeof event.target.result == 'undefined' ) 
                                        objectStore.add(data);
                                    else
                                        objectStore.put(data);                                    
                                    
                                     var result = $.grep(store_data, function(e, i){
                                        if( parseInt(e.id) == parseInt(data.id) ){
                                            var title = $('<div></div>').html(data.f_title).text();
                                            store_data[i] = { id: data.id, text: title };
                                            return true;
                                        }
                                        return false;
                                    });
                                    
                                    if(result.length == 0){
                                        var title = $('<div></div>').html(data.f_title).text();
                                        store_data.push( { id: data.id, text: title } );
                                    }
                                  }
                                }
                        }else{
                            debug(['-------', data]);
                            debug( 'Error inset product to data base :'+ JSON.stringify(data) );
                        }
                    });
                    
                }
                lastOffset = offset;
                setCookie('pos_LastOffset',lastOffset,5);

                if(ProductsCount >= limit+offset){
                    methods.insertUpdate(limit, limit+offset, ProductsCount);
                }
                else{
                    lastUpdate = _formatLastUpdateFilter();
                    methods.hideBlockWin();
                }
            });
        },
        insert: function(limit, offset, ProductsCount){
            var d = limit + offset;
            if(ProductsCount < d)
                d = ProductsCount;

            debug( 'Loading '+ d +' of '+ ProductsCount + ' products' );
            $.when(methods.getServerProducts(limit, offset)).then(function(productsData) {                
                var objectStore = methods.getObjectStore('readwrite');
                
                $.each(productsData.products, function(i, data){
                    var html_products = '';
                    if(typeof data.id != 'undefined' && typeof data.f_title != 'undefined'){
                        //objectStore.delete(data.id);
                            if(typeof data.variations != 'undefined' && data.type == 'variable'){
                                jQuery.each(data.variations, function(j, var_data){
                                    var_data.attributes = JSON.stringify(var_data.attributes); 
                                    objectStore.add(var_data);

                                    store_data.push({ id: var_data.id, text: var_data.f_title });
                                });
                            }
                            else
                            {
                                data.attributes = JSON.stringify(data.attributes);                
                                
                                objectStore.add(data);
                                store_data.push({ id: data.id, text: data.f_title });
                            }

                    }else{
                        debug(['-------', data]);
                        debug( 'Error inset product to data base :'+ JSON.stringify(data) );
                    }
                });

                if(ProductsCount > offset){
                    lastOffset = limit+offset;
                    setCookie('pos_LastOffset',lastOffset,5);
                    methods.insert(limit, limit+offset, ProductsCount);
                }
                else{
                    lastUpdate = _formatLastUpdateFilter();
                    methods.hideBlockWin();
                }
            });
        },
        canHide: function(){
            if($('#close_post-lock-dialog').length == 0){
                $('#post-lock-dialog .post-taken-over').append('<span id="close_post-lock-dialog" class="close_popup"></span>');
                $('#close_post-lock-dialog').click(function() {
                    methods.hideBlockWin();
                });
            }
        },
        hideBlockWin: function(){
            $('#post-lock-dialog').remove();
            methods.ready();
        },
        getHtml_products : function(data){
            var html_products = '';
            if(typeof data.id != 'undefined' && typeof data.f_title != 'undefined'){
                if(typeof data.variations != 'undefined' && data.type == 'variable'){
                    jQuery.each(data.variations, function(j, var_data){
                        
                        var title = var_data.f_title;
                        if ( var_data.sku != '' && title.indexOf( var_data.sku+' &ndash;' ) == -1 )
                            title = var_data.sku+' &ndash; '+title;
                        html_products += '<option value="' + var_data.id +'">'+title+'</option>';    
                    });
                }
                else
                {
                    var title = data.f_title;
                    if ( data.sku != '' && title.indexOf( data.sku+' &ndash;' ) == -1 )
                        title = data.sku+' &ndash; '+title;
                    html_products += '<option value="' + data.id +'">'+title+'</option>';    
                }
            }else{
                debug(['-------', data]);
                debug( 'Error inset product to data base :'+ JSON.stringify(data) );
            }
            return html_products;
        },
        getObj_products : function(data){
            var data_pr = false;
            if(typeof data.id != 'undefined' && typeof data.f_title != 'undefined'){
                if(typeof data.variations != 'undefined' && data.type == 'variable'){
                    var var_d = [];
                    var i = 0;
                    jQuery.each(data.variations, function(j, var_data){
                        
                        var title = var_data.f_title;
                        if ( var_data.sku != '' && title.indexOf( var_data.sku+' &ndash;' ) == -1 )
                            title = var_data.sku+' &ndash; '+title;

                        title = $('<div></div>').html(title).text();
                        var_d[i] = { id: var_data.id, text: title };   
                        i++;
                    });
                    data_pr = var_d;
                }
                else
                {
                    var title = data.f_title;
                    if ( data.sku != '' && title.indexOf( data.sku+' &ndash;' ) == -1 )
                        title = data.sku+' &ndash; '+title;

                    title = $('<div></div>').html(title).text();
                    data_pr = { id: data.id, text: title };
                }
            }else{
                debug(['-------', data]);
                debug( 'Error inset product to data base :'+ JSON.stringify(data) );
            }
            return data_pr;
        }
    };

    


    _formatLastUpdateFilter = function() {
                    var t = new Date();
                    if (t.getTime() > 0) {
                        var r = t.getUTCFullYear(),
                            i = t.getUTCMonth() + 1,
                            s = t.getUTCDate(),
                            o = t.getUTCHours(),
                            u = t.getUTCMinutes(),
                            a = t.getUTCSeconds();
                            var dd = r + "-" + i + "-" + s + "T" + o + ":" + u + ":" + a + "Z";
                            setCookie('pos_lastUpdate',dd,5);
                        return dd;
                    }
                    $('#last_sync_time').html('');
                    return null
                }


    function debug( msg, type ) {
        if ( window.console && window.console.log && wc_pos_params.wp_debug == true ) {
            window.console.log( msg );
        }
        if($('#process_loding').length){
            if( typeof msg == 'string' && msg != ''){
                if( type == false)
                    $('#process_loding').append('<p>'+msg+'</p>');
                else
                    $('#process_loding').append('<p>'+msg+'<span class="dot one">.</span><span class="dot two">.</span><span class="dot three">.</span>​</p>');
            }
            $('#process_loding').scrollTop($('#process_loding')[0].scrollHeight);
        }
    };
    function locked(msg) {
        // If some other tab is loaded with the database, then it needs to be closed
        // before we can proceed.
        var msg = msg;
        if(msg == '') msg = wc_pos_params.open_another_tab;
        debug( ['locked', msg] );
        $('#post-lock-dialog .post-taken-over').html("<p>"+msg+"</p>");
        $('#post-lock-dialog').show();
    }
    function unLocked() {
        if( jQuery('.post-locked-message.not_close').length == 0 ){
            //jQuery('#post-lock-dialog').hide();            
        }
    }
 
}( jQuery ));
function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    var LU = getCookie("pos_lastUpdate");
    if (LU != "") {
        return LU;
    }
    return 'null';
}
function checkCookieOffset() {
    var LO = getCookie("pos_LastOffset");
    if (LO != "") {
        return parseInt(LO);
    }
    return 0;
}
function openDb(){
   jQuery.pointofsale('init');
}

function addEventListeners_indexedDB() {
    
    jQuery('document').ready(function($) {

        $('#sync_data').click(function(){
          var attr = $(this).attr('disabled');
          
          if (typeof attr === typeof undefined)
            $.pointofsale('update');
          return false;
        });
        if(lastUpdate != 'null')
                jQuery('#last_sync_time').attr('title', lastUpdate).timeago();
        setInterval(function(){
            if(lastUpdate != 'null'){
                jQuery('#last_sync_time').attr('title', lastUpdate).timeago('updateFromDOM');
            }
        }, 1000);

        $('#wc-pos-register-grids').on('click', '.add_grid_tile', function() {
            var pid = $(this).find('a').attr('data-id');
            
            addProduct({pid: pid, element: $(this)});
            return false;
        });
        
        $('body').on('click', '#add_pr_variantion', function() {

            var all_variations = $('#var_tip').data( 'product_variations' ),
                exit              = false,
                showing_variation = false,
                current_settings  = {},
                humaniz_settings  = {},
                exclude           = '',
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
                    current_settings[ 'attribute_'+ $( this ).closest('div').data( 'taxonomy' ) ] = value;
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

                addProduct({pid: variation.variation_id, element: 'no', humaniz_settings: humaniz_settings, quantity: quantity});

            }else{
                toastr.error(wc_pos_params.cannot_be_purchased);
                ion.sound.play("error");
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

            addProduct({pid: product_id, element: 'no', humaniz_settings: humaniz_settings});
            $('#popup_choose_attributes').hide();
            return false;
        });
        $('#wc-pos-register-data').on('click', '.remove_order_item', function() {
            var $el = $(this).closest('tr');
            $el.remove();
            
            POScalculateTotal();
            $('#tiptip_holder').hide().css( {margin: '-100px 0 0 -100px'});
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
            if (code.trim() != ''){
                
                var store = jQuery.pointofsale('getObjectStore', 'readonly');
                var index = store.index("barcode");
                index.get(code).onsuccess = function(evt) {
                    var value = evt.target.result;
                    if(typeof value != 'undefined'){
                        var id = value.id;
                        addProduct({pid: id, element: 'no'});
                    }else{
                        toastr.error(wc_pos_params.cannot_be_purchased);
                        ion.sound.play("error");
                        return;
                    }
                };
            }else{
                toastr.error(wc_pos_params.cannot_be_purchased);
                ion.sound.play("error");
                return;
            }
        }

        $('#wc-pos-register-data').on('change', 'input.quantity', function() {
            var pid = $(this).parents('tr.item').find('input.product_item_id').val();
            
            POScalculateTotal();
        });

    });

}

function addProduct( option ) {
    if( (wc_pos_params.instant_quantity == 'yes' && option.element != 'no') || (option.element != 'no' && option.element.find('.hidden').length > 0 )  ){
            runVariantion(option.element);
            return;
    }

    var store = jQuery.pointofsale('getObjectStore', 'readonly');
    req = store.get(parseInt(option.pid));
    req.onsuccess = function (evt) {
        var value = evt.target.result;
        if(typeof value == 'undefined' || parseFloat(value.price) < 0){
            toastr.error(wc_pos_params.cannot_be_purchased);
            ion.sound.play("error");
            return;
        }

        var row_class  = 'product_id_' + value.id;
        var exit = false;
        var need_attributes = {};
        var attr = {};
        if(value.attributes != ""){
            
            var v_data = JSON.parse(value.attributes);

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
                    if(typeof value.parent_attr == 'string'){
                        var parent_attributes = jQuery.parseJSON(value.parent_attr);
                    }else{
                        var parent_attributes = value.parent_attr;
                    }
                    
                    for(var att in parent_attributes){

                        if (parent_attributes.hasOwnProperty(att) ){
                            var attribute = parent_attributes[att];

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
            popupChooseAttributes(value, need_attributes);
            return;
        }

        var new_item = jQuery('#wc-pos-register-data #order_items_list .' + hex_md5(row_class));


        var stock = value.stock_quantity;
        if (new_item.length > 0) {

            var $qt = new_item.find('input.quantity');
            var qt = parseInt($qt.val());
            if($qt.val() == '') qt = 0;
            if(typeof(option.quantity) != 'undefined')
                qt = qt+option.quantity;
            else
                qt = qt+1;

            if(!value.in_stock){
                toastr.error(wc_pos_params.out_of_stock);
                ion.sound.play("error");
                return;
            }else if( stock < qt && stock != '' ){
                var txt = wc_pos_params.cannot_add_product.replace('%NAME%', value.title);
                    txt = txt.replace('%COUNT%', stock);
                 toastr.error(txt);
                 ion.sound.play("error");
                 return;
            }
            $qt.val(qt);
            POScalculateTotal();
        }else{
            var qt = 1;
            if(typeof(option.quantity) != 'undefined')
                qt = option.quantity;

            if(!value.in_stock){
                toastr.error(wc_pos_params.out_of_stock);
                ion.sound.play("error");
                return;
            }else if(!value.in_stock || ( stock < qt && stock != '')){
                var txt = wc_pos_params.cannot_add_product.replace('%NAME%', value.title);
                    txt = txt.replace('%COUNT%', stock);
                 toastr.error(txt);
                 ion.sound.play("error");
                 return;
            }
            var parent_id = ( typeof value.parent_id != 'undefined' && value.parent_id > 0 ? value.parent_id : value.id);
            var cat_ids = ( typeof value.categories_ids != 'undefined' ? JSON.stringify(value.categories_ids) : '');
            var stock_qty = false;

            if( typeof value.managing_stock  != 'undefined'){
                stock_qty = value.managing_stock === true ? value.stock_quantity : false;
            }else if(typeof value.in_stock  != 'undefined' && value.in_stock === true && value.stock_quantity > 0){
                stock_qty = value.stock_quantity;                    
            }
            POSaddProduct({
                img        : value.featured_src,
                id         : value.id,
                href       : wc_pos_params.admin_url+'/post.php?post='+parent_id+'&amp;action=edit',
                barcode    : value.barcode,
                title      : value.title,
                attributes : attr,
                parent_id  : parent_id,
                cat_ids    : cat_ids,
                price      : value.price,
                quantity   : qt,
                taxable    : value.taxable,
                taxclass   : value.tax_class,
                stock_qty  : stock_qty,
                row_class  : row_class,
                sale_price : value.sale_price,
                regular_price : value.regular_price,
            });
        }
    };
}
function displayUnLocked() {
    jQuery('document').ready(function($) {
        if( jQuery('.post-locked-message.not_close').length == 0 ){
            //jQuery('#post-lock-dialog').hide();            
        }
    });
}
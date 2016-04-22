/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {/**
	 * pick POS off the window
	 */
	var app = global['POS'];
	var debug = app.debug('pro');

	app.on('before:start', function(){
	  debug('starting WooCommerce POS Pro admin app');
	});

	/**
	 * Services and SubApps
	 */
	__webpack_require__(1);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Customers = __webpack_require__(2);
	var License = __webpack_require__(5);

	// patch general view
	__webpack_require__(7);

	var Router = {

	  showCustomers: function(){
	    var model = app.settingsApp.collection.get('customers');
	    app.settingsApp.showFooter({model: model});
	    return new Customers({
	      container : app.settingsApp.layout.getRegion('settings'),
	      model: model
	    });
	  },

	  showLicense: function(){
	    var model = app.settingsApp.collection.get('license');

	    app.settingsApp.showFooter({
	      model: model,
	      buttons: [
	        {
	          action    : 'save',
	          label     : app.polyglot.t('buttons.activate'),
	          className : 'button-primary',
	          icon      : 'append'
	        },{
	          action    : 'restore',
	          label     : app.polyglot.t('buttons.deactivate'),
	          className : 'button-secondary',
	          icon      : 'append'
	        },{
	          type: 'message'
	        }
	      ]
	    });

	    var buttons = app.settingsApp.layout.getRegion('footer').currentView,
	        activateBtn = buttons.$('[data-action="save"]'),
	        deactivateBtn = buttons.$('[data-action="restore"]');

	    activateBtn.toggle(!model.get('activated'));
	    deactivateBtn.toggle(model.get('activated'));

	    model.on('change:activated', function(model, activated){
	      activateBtn.trigger('state', 'reset').toggle(!activated);
	      deactivateBtn.trigger('state', 'reset').toggle(activated);
	    });

	    return new License({
	      container : app.settingsApp.layout.getRegion('settings'),
	      model: model
	    });
	  }
	};

	app.settingsApp.processAppRoutes(Router, {
	  'customers' : 'showCustomers',
	  'license'   : 'showLicense'
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var View = __webpack_require__(3);

	module.exports = app.Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  fetch: function() {
	    if(this.model.isNew()){
	      return this.model.fetch();
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.container.show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var $ = __webpack_require__(4);

	module.exports = app.FormView.extend({

	  template: 'customers',

	  attributes: {
	    id: 'wc-pos-settings-customers'
	  },

	  modelEvents: {
	    'change:id': 'render'
	  },

	  onRender: function(){
	    var self = this;

	    // bind ordinary elements
	    this.$('input').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 4 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var View = __webpack_require__(6);

	module.exports = app.Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  fetch: function() {
	    if(this.model.isNew()){
	      return this.model.fetch();
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.container.show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var $ = __webpack_require__(4);

	module.exports = app.FormView.extend({

	  template: 'license',

	  attributes: {
	    id: 'wc-pos-settings-license'
	  },

	  modelEvents: {
	    'change:id change:activated': 'render'
	  },

	  onRender: function(){
	    var self = this;

	    // bind ordinary elements
	    this.$('input').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var $ = __webpack_require__(4);
	var _ = __webpack_require__(8);
	var Radio = __webpack_require__(9);

	// the general view prototype
	var prototype = app.SettingsApp.General.View.prototype;

	/**
	 *
	 */
	var getFields = function(){
	  var ajaxurl = Radio.request( 'entities', 'get', {
	    type: 'option',
	    name: 'ajaxurl'
	  });
	  var nonce = Radio.request( 'entities', 'get', {
	    type: 'option',
	    name: 'nonce'
	  });
	  return $.getJSON( ajaxurl, {
	    action: 'wc_pos_pro_barcode_fields',
	    security: nonce
	  });
	};

	/**
	 * Hook into the onRender function
	 */
	var onRender = prototype.onRender;

	prototype.onRender = function(){
	  var barcode_select = this.$('[name="barcode_field"]');
	  var model = this.model;

	  // init the barcode_field
	  var selected = this.model.get('barcode_field');
	  barcode_select.html( $('<option />').val(selected).text(selected) );

	  // populate on first open
	  barcode_select.one('select2:open', function(){

	    // append loading option
	    var loading = $('<option />')
	      .text( app.polyglot.t('messages.loading') )
	      .prop('disabled', true);
	    barcode_select.append( loading );

	    // fetch barcode fields
	    getFields().done(function(fields){
	      barcode_select.html( _.map( fields, function( field ){

	        // construct each option
	        return $('<option />')
	          .val(field)
	          .text(field)
	          .prop('selected', (field === selected) );
	      }));

	      // hack .. open and close to refresh
	      // todo: there must be a cleaner way?
	      barcode_select.select2('close').select2('open');
	    });
	  });

	  // bump IDB version if barcode is changed
	  barcode_select.one('select2:select', function(){
	    model.set({ bump_idb_version: true });
	  });

	  // run the parent onRender
	  return onRender.apply(this, arguments);
	};

	// events
	//prototype.behaviors.Select2 = prototype.behaviors.Select2 || {};
	//_.extend( prototype.behaviors.Select2, {
	//  'barcode_field' : {
	//    minimumInputLength: 2
	//  }
	//});

	//
	//// ui
	//View.ui = View.ui || {};
	//_.extend( View.ui, {
	//  barcodeFieldSelect : '[name="barcode_field"]'
	//});
	//
	//// events
	//View.events = View.events || {};
	//_.extend( View.events, {
	//  //'change @ui.barcodeFieldSelect' : function(){
	//  //  console.log(arguments);
	//  //}
	//});
	//

	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 8 */
/***/ function(module, exports) {

	module.exports = _;

/***/ },
/* 9 */
/***/ function(module, exports) {

	module.exports = Backbone.Radio;

/***/ }
/******/ ]);
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
	    debug('starting WooCommerce POS Pro app');
	});

	/**
	 * Config
	 */
	__webpack_require__(10);

	/**
	 * Helpers
	 */
	__webpack_require__(11);

	/**
	 * Services and SubApps
	 */
	__webpack_require__(13);
	__webpack_require__(19);
	__webpack_require__(22);

	var ProductsRouter  = __webpack_require__(24);
	var OrdersRouter    = __webpack_require__(33);
	var CustomersRouter = __webpack_require__(41);
	var CouponsRouter   = __webpack_require__(46);

	app.productsApp     = new ProductsRouter();
	app.ordersApp       = new OrdersRouter();
	app.customersApp    = new CustomersRouter();
	app.couponsApp      = new CouponsRouter();
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ },
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */
/***/ function(module, exports) {

	module.exports = _;

/***/ },
/* 9 */
/***/ function(module, exports) {

	module.exports = Backbone.Radio;

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(9);

	var prepare = app.ReceiptView.prototype.prepare;

	app.ReceiptView.prototype.prepare = function(order){
	  var tax = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax'
	    }) || {};
	  order.store = Radio.request('entities', 'get', {
	    type: 'option',
	    name: 'store'
	  });
	  return prepare(order, tax);
	};
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	//var Utils = require('./utils');
	var bb = __webpack_require__(12);
	var _ = __webpack_require__(8);

	/**
	 * Custom Stickit handler for nested customer address
	 */
	bb.Stickit.addHandler({
	  selector: 'input[data-handler="address"]',
	  onGet: function( value, options ){
	    var key = options.selector.match(/\w+\[(\w+)\]/)[1];
	    if( _(value).has(key) ){
	      return value[key];
	    } else {
	      return '';
	    }

	  },
	  onSet: function( value, options ){
	    var key = options.selector.match(/\w+\[(\w+)\]/)[1];
	    var address = options.view.model.get( options.observe );
	    address = address || {};
	    address[key] = value;
	    return address;
	  }
	});

/***/ },
/* 12 */
/***/ function(module, exports) {

	module.exports = Backbone;

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	// overwrite collections
	var collections = app.Entities.Service.prototype.collections;
	collections.customers = __webpack_require__(14);
	collections.coupons = __webpack_require__(16);
	__webpack_require__(18);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Model = __webpack_require__(15);
	var Radio = __webpack_require__(9);

	module.exports = app.DualCollection.extend({
	  model: Model,
	  name: 'customers',
	  indexes: [
	    {name: 'local_id', keyPath: 'local_id', unique: true},
	    {name: 'id', keyPath: 'id', unique: true},
	    {name: 'status', keyPath: 'status', unique: false},
	    {name: 'email', keyPath: 'email', unique: true},
	    {name: 'username', keyPath: 'username', unique: true}
	  ],

	  initialize: function(){
	    var settings = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'customers'
	    });
	    if(settings){
	      this._guest = settings.guest;
	      this._default = settings['default'] || settings.guest;
	    }
	  },

	  getGuestCustomer: function(){
	    return this._guest;
	  },

	  getDefaultCustomer: function(){
	    return this._default;
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 15 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.DualModel.extend({
	  name: 'customer',

	  // this is an array of fields used by FilterCollection.matchmaker()
	  fields: ['email', 'username', 'first_name', 'last_name'],

	  validation: function(){
	    // email is required
	    var rules = {
	      email: {
	        required: true,
	        pattern: 'email'
	      }
	    };

	    // if creating new customer & auto generating, username is required
	    if( !app.options.generate_username ){
	      rules.username = {
	        required: true
	      };
	    }
	    return rules;
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Model = __webpack_require__(17);

	module.exports = app.DualCollection.extend({
	  model: Model,
	  name: 'coupons'
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 17 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.DualModel.extend({
	  name: 'coupon'
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	//var Radio = require('backbone.radio');
	var _ = __webpack_require__(8);
	var $ = __webpack_require__(4);

	// this should match WC_POS_Pro_Gateways->stripe_gateways
	var stripeGateways = [
	  'stripe',
	  'Striper',
	  's4wc'
	];

	/**
	 * Extend the Cart Route
	 */
	module.exports = _.extend( app.Entities.Order.Model.prototype, {

	  processGateway: function(){
	    var data = this.gateways.findWhere({ active: true }).toJSON();
	    if(window.Stripe && _.contains(stripeGateways, data.method_id)){
	      return this.processStripeGateway(data);
	    }
	    this.set({
	      payment_details: data
	    });
	  },

	  processStripeGateway: function(data){
	    var deferred = new $.Deferred();
	    var self = this;

	    /**
	     * Parse data for credit card details
	     */
	    /* jshint -W071, -W074  */
	    var cc = _.reduce(data, function(result, value, key) {
	      if(key === 'number' || /-card-number$/.test(key)){
	        result['number'] = value;
	      }
	      if(key === 'cvc' || /-card-cvc/.test(key)){
	        result['cvc'] = value;
	      }
	      if(/-card-expiry/.test(key)){
	        var date = value.split('/');
	        result['exp_month'] = date[0];
	        result['exp_year'] = date[1];
	      }
	      if(key === 'exp-month'){
	        result['exp_month'] = value;
	      }
	      if(key === 'exp-year'){
	        result['exp_year'] = value;
	      }
	      return result;
	    }, {});
	    /* jshint +W071, +W074 */

	    window.Stripe.card.createToken(cc, function(status, response){
	      if( response.error ){
	        data.message = response.error.message;
	        data.paid = false;
	        self.set({ payment_details: data });
	        deferred.reject();
	      }
	      data.stripe_token = response.id;
	      self.set({ payment_details: data });
	      deferred.resolve();
	    });

	    return deferred;
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * POSApp module has already been created
	 * this is just a shell to bootstrap changes
	 */
	__webpack_require__(20);

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(9);
	var _ = __webpack_require__(8);

	// extend CustomerView
	__webpack_require__(21);

	/**
	 * Extend the Cart Route
	 */
	module.exports = _.extend( app.POSApp.Cart.Route.prototype, {

	  onEnter: function(){
	    this.openOrders = this.collection.openOrders();
	    if(this.openOrders.length > 0){
	      this.showTabs();
	    }
	  },

	  ///**
	  // * hi-jack customer panel
	  // */
	  //showCustomer: function(){
	  //  var view = new CustomerView({
	  //    model: this.order
	  //  });
	  //  this.layout.getRegion('customer').show( view );
	  //},

	  showTabs: function(){
	    var view = Radio.request('tabs', 'view', {
	      tabs : this.tabsArray()
	    });

	    this.listenTo( this.collection, 'change:total', function(model){
	      var tab = view.collection.get(model.id);
	      if(tab){
	        tab.set({label : this.tabLabel(model)});
	      }
	    });

	    this.listenTo( view.collection, 'active:tab', function(model){
	      this.navigate('cart/' + model.id, {trigger: true});
	    });

	    this.layout.getRegion('footer').show(view);
	    this.layout.getRegion('footer').$el.addClass('tabs infinite-tabs');

	  },

	  tabsArray: function(){
	    var active = this.collection.active;

	    var tabs = _.map(this.openOrders, function(order){
	      var tab     = {};
	      tab.id      = order.id;
	      tab.label   = this.tabLabel(order);
	      tab.active  = order === active;
	      return tab;
	    }, this);

	    tabs.push({
	      id    : 'new',
	      label : '<i class="icon-plus"></i>',
	      active: !active
	    });

	    return tabs;
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(9);
	var CustomerView = app.POSApp.Cart.Views.Customer.prototype;
	var _ = __webpack_require__(8);

	_.extend( CustomerView.events, {
	  'click [data-action="add"]': function(){
	    Radio.request('router', 'add:customer');
	  },
	  'click [data-action="edit"]': function(){
	    Radio.request('router', 'edit:customer', {
	      remote_id: this.model.get('customer.id')
	    });
	  }
	});

	_.extend( CustomerView, {
	  onModalSave: function(model){
	    this.saveCustomer( model.toJSON() );
	  }
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * SupportApp module has already been created
	 * this is just a shell to bootstrap changes
	 */
	__webpack_require__(23);

/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var _ = __webpack_require__(8);
	//var $ = require('jquery');
	//var Radio = require('backbone.radio');

	/**
	 * Extend the Cart Route
	 */
	module.exports = _.extend( app.SupportApp.Status.Route.prototype, {

	  databases: ['products', 'orders', 'cart', 'customers', 'coupons']

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var ListRoute = __webpack_require__(25);

	module.exports = app.Router.extend({

	  routes: {
	    'products' : 'list'
	  },

	  list: function(){
	    return new ListRoute();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 25 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Layout = __webpack_require__(26);
	var Actions = __webpack_require__(27);
	var Products = __webpack_require__(28);
	var Radio = __webpack_require__(9);
	var Pagination = app.Components.Pagination.View;

	module.exports = app.Route.extend({

	  initialize: function() {
	    this.container  = app.layout.getRegion('main');
	    this.filtered   = Radio.request('entities', 'get', {
	      type    : 'filtered',
	      name    : 'products',
	      perPage : 10
	    });
	  },

	  fetch: function() {
	    var collection = this.filtered.superset();
	    if( collection.isNew() ){
	      return collection.fetch()
	        .then(function(){
	          collection.fullSync();
	        });
	    } else {
	      this.filtered
	        .resetFilters()
	        .removeSort()
	        .setPage(0);
	    }
	  },

	  render: function() {
	    this.layout = new Layout();

	    this.listenTo(this.layout, 'show', function () {
	      this.showActions();
	      this.showProducts();
	      this.showPagination();
	    });

	    this.container.show( this.layout );
	  },

	  showActions: function(){
	    var view = new Actions({
	      collection: this.filtered
	    });

	    // show
	    this.layout.getRegion('actions').show( view );
	  },

	  showProducts: function() {

	    // pass filtered collection to view
	    var view = new Products({
	      collection: this.filtered
	    });

	    // show in products region
	    this.layout.getRegion('list').show( view );
	  },

	  showPagination: function(){
	    var view = new Pagination({
	      collection: this.filtered
	    });
	    this.layout.getRegion('footer').show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 26 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.LayoutView.extend({

	  template: 'products.panel',

	  tagName: 'section',

	  regions: {
	    actions : '.products-actions',
	    list    : '.products-list',
	    footer  : '.products-footer'
	  },

	  attributes: {
	    'class': 'panel products products-page'
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 27 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.ItemView.extend({

	  template: 'products.actions',

	  behaviors: {
	    Filter: {
	      behaviorClass: app.Behaviors.Filter
	    }
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 28 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Item = __webpack_require__(29);

	var Empty = app.ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return app.polyglot.t('messages.no-products');
	  }
	});

	module.exports = app.InfiniteListView.extend({
	  childView: Item,
	  emptyView: Empty,
	  childViewContainer: 'ul'
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 29 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Product = __webpack_require__(30);
	var Variations = __webpack_require__(31);

	module.exports = app.LayoutView.extend({

	  tagName: 'li',

	  className: function () {
	    return this.model.get('type');
	  },

	  template: function () {
	    return '' +
	      '<div class="item"></div>' +
	      '<div class="list-drawer"></div>';
	  },

	  regions: {
	    item    : '.item',
	    drawer  : '.list-drawer'
	  },

	  onRender: function(){
	    var view = new Product({
	      model: this.model
	    });

	    this.listenTo( view, 'open:drawer', this.openDrawer );
	    this.listenTo( view, 'close:drawer', this.closeDrawer );
	    this.listenTo( view, 'toggle:drawer', this.toggleDrawer );

	    this.getRegion('item').show(view);
	  },

	  openDrawer: function(options){
	    options = options || {};
	    options.model = this.model;
	    options.className = 'variations';
	    var view = new Variations(options);
	    this.getRegion('drawer').show(view);
	    this.$el.addClass('drawer-open');
	  },

	  closeDrawer: function(){
	    var drawer = this.getRegion('drawer');

	    drawer.$el.slideUp( 250, function(){
	      drawer.empty();
	      drawer.$el.show();
	    });

	    this.$el.removeClass('drawer-open');
	  },

	  toggleDrawer: function(options){
	    var drawer = this.getRegion('drawer'),
	        open = drawer.hasView();

	    if(open && options.filter){
	      return drawer.currentView.filterVariations(options.filter);
	    }

	    if(open){
	      this.closeDrawer();
	    } else {
	      this.openDrawer(options);
	    }
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var $ = __webpack_require__(4);
	var _ = __webpack_require__(8);

	module.exports = app.FormView.extend({

	  template: 'products.list-item',

	  className: 'list-row',

	  templateHelpers: function(){
	    var data = this.model.get( 'type' ) === 'variable' ? {
	      product_variations: this.model.getVariationOptions()
	    } : {} ;
	    data.product_attributes = this.model.productAttributes();
	    return data;
	  },

	  ui: {
	    variations: '.title dl dd a',
	    title: '.title'
	  },

	  events: {
	    'click @ui.variations': 'toggleVariations',
	    'click @ui.title': 'focusTitle'
	  },

	  behaviors: {
	    Numpad: {
	      behaviorClass: app.Behaviors.Numpad
	    },
	    AutoGrow: {
	      behaviorClass: app.Behaviors.AutoGrow
	    },
	    Tooltip: {
	      behaviorClass: app.Behaviors.Tooltip,
	      html: true
	    }
	  },

	  bindings: {
	    '.title strong': {
	      observe: 'title',
	      events: ['blur']
	    },
	    'input[name="stock_quantity"]': {
	      observe: 'stock_quantity',
	      onGet: function(value) {
	        return app.Utils.formatNumber(value, 'auto');
	      },
	      onSet: app.Utils.unformat,
	      attributes: [{
	        name: 'disabled',
	        observe: 'managing_stock',
	        onGet: function(val) {
	          return !val;
	        }
	      }]
	    },
	    'input[name="managing_stock"]': 'managing_stock',
	    'input[name="regular_price"]': {
	      observe: 'regular_price',
	      onGet: app.Utils.formatNumber,
	      onSet: app.Utils.unformat
	    },
	    'input[name="taxable"]': 'taxable',
	    'input[name="sale_price"]': {
	      observe: 'sale_price',
	      onGet: app.Utils.formatNumber,
	      onSet: app.Utils.unformat,
	      attributes: [{
	        name: 'disabled',
	        observe: 'on_sale',
	        onGet: function(val) {
	          return !val;
	        }
	      }]
	    },
	    'input[name="on_sale"]': 'on_sale',
	    'span[data-name="regular_price"]': {
	      observe: 'variations',
	      updateMethod: 'html',
	      onGet: function(){
	        var r = this.model.getVariations().superset().range('regular_price');
	        return _.map(r, app.Utils.formatMoney).join(' - ');
	      }
	    },
	    'span[data-name="sale_price"]': {
	      observe: 'variations',
	      updateMethod: 'html',
	      onGet: function(){
	        var r = this.model.getVariations().superset().range('sale_price');
	        return _.map(r, app.Utils.formatMoney).join(' - ');
	      }
	    }
	  },

	  modelEvents: {
	    'change:title'          : 'save',
	    'change:stock_quantity' : 'save',
	    'change:regular_price'  : 'save',
	    'change:managing_stock' : 'save',
	    'change:taxable'        : 'save',
	    'change:sale_price'     : 'save',
	    'change:on_sale'        : 'save'
	  },

	  /**
	  * Bit of a hack here
	  * - we only want to remote save on user input
	  * - option.stickitChange will be true for input
	  * - however, numpad sets the model directly
	  * - so I've added options.numpadChange
	  */
	  save: function( model, value, options ){
	    options = options || value;
	    if( options.stickitChange || options.numpadChange ){
	      this.model.save( model.changed, {remote: true, patch: true});
	    }
	  },

	  toggleVariations: function(e){
	    e.preventDefault();
	    var options = {};

	    var name = $(e.target).data('name');
	    if(name){
	      options.filter = {
	        name: name,
	        option: $(e.target).text()
	      };
	    }

	    this.trigger('toggle:drawer', options);
	  },

	  focusTitle: function(){
	    this.ui.title.find('strong').focus();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 31 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Variation = __webpack_require__(32);
	var _ = __webpack_require__(8);

	var Empty = app.ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: 'products.empty'
	});

	module.exports = app.CollectionView.extend({
	  childView: Variation,
	  emptyView: Empty,
	  childViewContainer: 'ul',

	  initialize: function(options){
	    options = options || {};
	    this.collection = options.model.getVariations();
	    this.collection.resetFilters();
	    this.filterVariations(options.filter);
	  },

	  onShow: function() {
	    this.$el.hide().slideDown(250);
	  },

	  filterVariations: function(filter){
	    if(filter){
	      filter = filter || {};
	      var matchMaker = function(model){
	        var attributes = model.get('attributes');
	        return _.any(attributes, function(attribute){
	          return attribute.name === filter.name &&
	            attribute.option === filter.option;
	        });

	      };
	      this.collection.filterBy('variation', matchMaker);
	    }
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 32 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.FormView.extend({

	  template: 'products.list-item',

	  className: 'list-row',

	  behaviors: {
	    Numpad: {
	      behaviorClass: app.Behaviors.Numpad
	    },
	    AutoGrow: {
	      behaviorClass: app.Behaviors.AutoGrow
	    },
	    Tooltip: {
	      behaviorClass: app.Behaviors.Tooltip,
	      html: true
	    }
	  },

	  ui: {
	    title: '.title strong'
	  },

	  initialize: function(options){
	    this.listenTo(options.model.parent, {
	      'change:title': function(parent, title){
	        this.ui.title.text(title);
	      }
	    });
	  },

	  templateHelpers: function(){
	    return {
	      variation: true,
	      title: this.options.model.parent.get('title')
	    };
	  },

	  bindings: {
	    'input[name="stock_quantity"]'  : {
	      observe: 'stock_quantity',
	      onGet: function(value) {
	        return app.Utils.formatNumber(value, 'auto');
	      },
	      onSet: app.Utils.unformat,
	      attributes: [{
	        name: 'disabled',
	        observe: 'managing_stock',
	        onGet: function(val) {
	          return !val;
	        }
	      }]
	    },
	    'input[name="managing_stock"]'  : 'managing_stock',
	    'input[name="regular_price"]'   : {
	      observe: 'regular_price',
	      onGet: app.Utils.formatNumber,
	      onSet: app.Utils.unformat
	    },
	    'input[name="taxable"]'         : 'taxable',
	    'input[name="sale_price"]'      : {
	      observe: 'sale_price',
	      onGet: app.Utils.formatNumber,
	      onSet: app.Utils.unformat,
	      attributes: [{
	        name: 'disabled',
	        observe: 'on_sale',
	        onGet: function(val) {
	          return !val;
	        }
	      }]
	    },
	    'input[name="on_sale"]'         : 'on_sale'
	  },

	  modelEvents: {
	    'change:stock_quantity' : 'save',
	    'change:regular_price'  : 'save',
	    'change:managing_stock' : 'save',
	    'change:taxable'        : 'save',
	    'change:sale_price'     : 'save',
	    'change:on_sale'        : 'save'
	  },

	  /**
	   * Bit of a hack here
	   * - we only want to remote save on user input
	   * - option.stickitChange will be true for input
	   * - however, numpad sets the model directly
	   * - so I've added options.numpadChange
	   */
	  save: function( model, value, options ){
	    options = options || value;
	    if( options.stickitChange || options.numpadChange ){
	      this.model.save( model.changed, {remote: true, patch: true});
	    }
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 33 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var ListRoute = __webpack_require__(34);
	var ShowRoute = __webpack_require__(39);

	module.exports = app.Router.extend({

	  initialize: function(){

	    this.channel.reply({
	      'show:order': function(options){
	        this.execute(this.show, options);
	      }
	    }, this);

	  },

	  routes: {
	    'orders' : 'list'
	  },

	  list: function(){
	    return new ListRoute();
	  },

	  /**
	   * shows modal
	   */
	  show: function(){
	    return new ShowRoute();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(12).Radio;
	var Layout = __webpack_require__(35);
	var Actions = __webpack_require__(36);
	var Orders = __webpack_require__(37);
	var Pagination = app.Components.Pagination.View;

	module.exports = app.Route.extend({

	  initialize: function() {
	    this.container  = app.layout.getRegion('main');
	    this.filtered = Radio.request('entities', 'get', {
	      type    : 'filtered',
	      name    : 'orders',
	      perPage : 10
	    });
	  },

	  fetch: function() {
	    var collection = this.filtered.superset();
	    if( collection.isNew() ){
	      return collection.fetch()
	        .then(function(){
	          collection.fullSync();
	        });
	    }
	  },

	  onFetch: function(){
	    this.filtered
	      .resetFilters()
	      .setSort('id', 'desc')
	      .setPage(0);
	  },

	  render: function() {
	    this.layout = new Layout();

	    this.listenTo(this.layout, 'show', function () {
	      this.showActions();
	      this.showOrders();
	      this.showPagination();
	    });

	    this.container.show( this.layout );
	  },

	  /**
	   * Actions
	   */
	  showActions: function(){
	    var view = new Actions({
	      collection: this.filtered
	    });

	    // open new order
	    this.listenTo( view, {
	      'new:order': function(){
	        this.navigate('cart/new', {trigger: true});
	      }
	    });

	    // show
	    this.layout.getRegion('actions').show( view );
	  },

	  showOrders: function() {
	    var view = new Orders({
	      collection: this.filtered
	    });

	    this.listenTo(view, {
	      'childview:show:order': function(childview, args) {
	        if( args.model.hasRemoteId() ){
	          Radio.request('router', 'show:order', {local_id: args.model.id});
	        } else {
	          this.navigate('cart/' + args.model.id, {trigger: true});
	        }
	      },
	      'childview:show:customer': function(childview, args){
	        var customer_id = args.model.get('customer_id');
	        Radio.request('router', 'edit:customer', {remote_id: customer_id});
	      }
	    });

	    // show
	    this.layout.getRegion('list').show( view );
	  },

	  showPagination: function(){
	    var view = new Pagination({
	      collection: this.filtered
	    });
	    this.layout.getRegion('footer').show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 35 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.LayoutView.extend({

	  template: 'orders.panel',

	  tagName: 'section',

	  regions: {
	    actions : '.list-actions',
	    list    : '.list',
	    footer  : '.list-footer'
	  },

	  attributes: {
	    'class': 'panel orders-page'
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 36 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.ItemView.extend({

	  template: 'orders.actions',

	  className: 'form-inline',

	  behaviors: {
	    Filter: {
	      behaviorClass: app.Behaviors.Filter
	    }
	  },

	  ui: {
	    'order': '*[data-action="new-order"]',
	    'search': 'input[type="search"]'
	  },

	  triggers: {
	    'click @ui.order': 'new:order'
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Order = __webpack_require__(38);

	var NoOrder = app.ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return app.polyglot.t('messages.no-orders');
	  }
	});

	module.exports = app.InfiniteListView.extend({
	  childView: Order,
	  emptyView: NoOrder,
	  childViewContainer: 'ul'
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(9);

	module.exports = app.ItemView.extend({
	  tagName: 'li',

	  className: 'list-row',

	  template: 'orders.list-item',

	  initialize: function(){
	    this.statuses = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'order_status'
	    });
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: app.Behaviors.Tooltip
	    }
	  },

	  triggers: {
	    'click a[data-action="show"]': 'show:order',
	    'click a[data-action="customer"]': 'show:customer'
	  },

	  templateHelpers: function(){
	    var data = {};

	    var status = this.model.get('status');
	    if(this.statuses.hasOwnProperty('wc-' + status)){
	      data.status_label = this.statuses['wc-' + this.model.get('status')];
	    } else {
	      data.status = 'open';
	      data.status_label = app.polyglot.t('titles.open');
	    }

	    data.customer_name = this.customerName();

	    return data;
	  },

	  customerName: function(){
	    var customer = this.model.get('customer');
	    var name = customer.first_name + ' ' + customer.last_name;
	    if( name.trim() === '' ){
	      name = customer.email;
	    }
	    return name;
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 39 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(9);
	var View = __webpack_require__(40);
	var $ = __webpack_require__(4);
	//var ItemsView = require('./items');
	//var TotalsView = require('./totals');
	//var debug = app.debug('ordersApp');

	module.exports = app.Route.extend({

	  initialize: function () {
	    this.collection = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'orders'
	    });
	    this.tax = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax'
	    }) || {};
	  },

	  fetch: function(options) {
	    options = options || {};
	    var self = this;
	    if( this.collection.isNew() ){
	      return this.collection.fetch()
	        .then(function(){
	          return self._fetch(options);
	        });
	    }
	    return this._fetch(options);
	  },

	  _fetch: function(options){
	    this.model = this.findModel(options);
	    if(!this.model && options.remote_id){
	      var model = this.collection.add({
	        id: options.remote_id,
	        status: 'completed' // placeholder status, ie: closed order
	      });
	      return model.save({}, { wait: true })
	        .then(function(){
	          return model.fetch({ remote: true });
	        });
	    }
	  },

	  onFetch: function(options){
	    options = options || {};
	    if(!this.model){
	      this.model = this.findModel(options);
	    }
	  },

	  findModel: function(options){
	    var model;
	    if(options.local_id){
	      model = this.collection.get(options.local_id);
	    } else if(options.remote_id){
	      model = this.collection.findWhere({
	        id: options.remote_id
	      });
	    }
	    return model;
	  },

	  render: function () {
	    var self = this;

	    this.layout = new View({
	      model: this.model
	    });

	    this.listenTo(this.layout, {
	      'show' : function(){
	        this.showItems();
	        this.showTotals();
	      }
	    });

	    Radio.request('modal', 'open', this.layout)
	      .then(function(args){
	        var buttons = args.view.getButtons();
	        self.listenTo(buttons, {
	          'action:print': self.print,
	          'action:email': self.email
	        });
	      });
	  },

	  showItems: function(){
	    var view = new app.POSApp.Receipt.Views.Items({
	      model: this.model,
	      tax_display_cart: this.tax.tax_display_cart
	    });

	    this.layout.list.show(view);
	  },

	  showTotals: function(){
	    var view = new app.POSApp.Receipt.Views.Totals({
	      model: this.model,
	      tax_display_cart: this.tax.tax_display_cart,
	      tax_total_display: this.tax.tax_total_display
	    });

	    this.layout.totals.show(view);
	  },

	  print: function(){
	    Radio.request('print', 'print', {
	      template: 'receipt',
	      model: this.model
	    });
	  },

	  email: function(){
	    var self = this;

	    var view = new app.POSApp.Receipt.Views.Email({
	      order_id: this.model.get('id'),
	      email: this.model.get('customer.email')
	    });

	    Radio.request('modal', 'open', view)
	      .then(function(args){
	        var buttons = args.view.getButtons();
	        self.listenTo(buttons, 'action:send', function(btn, view){
	          var email = args.view.getRegion('content').currentView.getEmail();
	          self.send(btn, view, email);
	        });
	      });

	  },

	  send: function(btn, view, email){
	    var order_id = this.model.get('id'),
	        ajaxurl = Radio.request('entities', 'get', {
	          type: 'option',
	          name: 'ajaxurl'
	        });

	    btn.trigger('state', [ 'loading', '' ]);

	    function onSuccess(resp){
	      if(resp.result === 'success'){
	        btn.trigger('state', [ 'success', resp.message ]);
	      } else {
	        btn.trigger('state', [ 'error', resp.message ]);
	      }
	    }

	    function onError(jqxhr){
	      var message = null;
	      if(jqxhr.responseJSON && jqxhr.responseJSON.errors){
	        message = jqxhr.responseJSON.errors[0].message;
	      }
	      btn.trigger('state', ['error', message]);
	    }

	    $.getJSON( ajaxurl, {
	      action: 'wc_pos_email_receipt',
	      order_id: order_id,
	      email : email
	    })
	      .done(onSuccess)
	      .fail(onError);
	  }

	});

	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 40 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.LayoutView.extend({
	  template: 'pos.receipt.panel',

	  tagName: 'section',

	  regions: {
	    list    : '.list',
	    totals  : '.list-totals'
	  },

	  className: function(){
	    return 'receipt ' + this.model.get('status');
	  },

	  initialize: function(){
	    this.modal = {
	      header: {
	        title: app.polyglot.t('titles.order') +
	          ' #' + this.model.get('order_number')
	      },
	      footer: {
	        buttons: [{
	          action: 'print',
	          className: 'btn-primary pull-left'
	        }, {
	          action: 'email',
	          className: 'btn-primary pull-left'
	        }, {
	          action: 'close'
	        }]
	      }
	    };
	  },

	  onShow: function(){
	    this.$('.status').remove();
	    this.$('.list-actions').remove();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 41 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var ListRoute = __webpack_require__(42);
	var EditRoute = __webpack_require__(44);

	module.exports = app.Router.extend({

	  initialize: function(){

	    this.channel.reply({
	      'add:customer': function(options){
	        this.execute(this.add, options);
	      },
	      'edit:customer': function(options){
	        this.execute(this.edit, options);
	      }
	    }, this);

	  },

	  routes: {
	    'customers': 'list'
	  },

	  list: function(){
	    return new ListRoute();
	  },

	  add: function(){
	    return new EditRoute();
	  },

	  edit: function(){
	    return new EditRoute();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Radio = __webpack_require__(12).Radio;
	var Views = __webpack_require__(43);
	var Pagination = app.Components.Pagination.View;

	module.exports = app.Route.extend({

	  initialize: function() {
	    this.container  = app.layout.getRegion('main');
	    this.filtered   = Radio.request('entities', 'get', {
	      type    : 'filtered',
	      name    : 'customers',
	      perPage : 10
	    });
	  },

	  fetch: function() {
	    var collection = this.filtered.superset();
	    if( collection.isNew() ){
	      return collection.fetch()
	        .then(function(){
	          collection.fullSync();
	        });
	    } else {
	      this.filtered
	        .removeTransforms()
	        .setPerPage(10);
	    }
	  },

	  render: function() {
	    this.layout = new Views.Layout();

	    this.listenTo(this.layout, 'show', function () {
	      this.showActions();
	      this.showCustomers();
	      this.showPagination();
	    });

	    this.container.show( this.layout );
	  },

	  showActions: function(){
	    var view = new Views.Actions({
	      collection: this.filtered
	    });

	    this.listenTo(view, {
	      'add': function(){
	        Radio.request('router', 'add:customer');
	      }
	    });

	    this.layout.getRegion('actions').show( view );
	  },

	  showCustomers: function() {
	    var view = new Views.Customers({
	      collection: this.filtered
	    });

	    this.listenTo(view, {
	      'childview:edit:customer': function(childview, args){
	        var id = args.model.get('id');
	        Radio.request('router', 'edit:customer', {remote_id: id});
	      },
	      'childview:show:order': function(childview, args){
	        var id = parseInt( args.model.get('last_order_id'), 10 );
	        Radio.request('router', 'show:order', {remote_id: id});
	      },
	      'childview:show:orders': function(childview, args){
	        var id = args.model.get('id');
	        var orders = Radio.request('entities', 'get', {
	          name: 'orders',
	          type: 'filtered'
	        });
	        orders.query('customer_id:' + id);
	        this.navigate('orders', { trigger: true});
	      }
	    });

	    this.layout.getRegion('list').show( view );
	  },

	  showPagination: function(){
	    var view = new Pagination({
	      collection: this.filtered
	    });
	    this.layout.getRegion('footer').show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 43 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Views = {};

	Views.Layout = app.LayoutView.extend({
	  template: 'customers.panel',
	  tagName: 'section',
	  regions: {
	    actions : '.list-actions',
	    list    : '.list',
	    footer  : '.list-footer'
	  },
	  attributes: {
	    'class': 'panel customers-page'
	  }
	});

	Views.Actions = app.ItemView.extend({
	  template: 'customers.actions',

	  className: 'form-inline',

	  behaviors: {
	    Filter: {
	      behaviorClass: app.Behaviors.Filter
	    }
	  },

	  ui: {
	    add : 'a[data-action="add"]'
	  },

	  triggers: {
	    'click @ui.add' : 'add'
	  }
	});

	Views.Customer = app.ItemView.extend({

	  template: 'customers.list-item',

	  tagName: 'li',

	  className: 'list-row',

	  behaviors: {
	    Tooltip: {
	      behaviorClass: app.Behaviors.Tooltip
	    }
	  },

	  // todo: why? filtered collection perhaps?
	  modelEvents: {
	    'change': 'render'
	  },

	  ui: {
	    edit      : 'a[data-action="edit"]',
	    showOrder : 'a[data-action="showOrder"]',
	    showOrders: 'a[data-action="showOrders"]'
	  },

	  triggers: {
	    'click @ui.edit'      : 'edit:customer',
	    'click @ui.showOrder' : 'show:order',
	    'click @ui.showOrders': 'show:orders'
	  }
	});

	Views.NoCustomer = app.ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return app.polyglot.t('messages.no-customer');
	  }
	});

	Views.Customers = app.InfiniteListView.extend({
	  childView: Views.Customer,
	  emptyView: Views.NoCustomer,
	  childViewContainer: 'ul'
	});

	module.exports = Views;
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 44 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var View = __webpack_require__(45);
	var Radio = __webpack_require__(9);

	module.exports = app.Route.extend({

	  initialize: function () {
	    this.collection = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'customers'
	    });
	  },

	  fetch: function (options) {
	    this.mergeOptions(options, ['remote_id']);
	    var self = this;

	    if( this.collection.isNew() ){
	      return this.collection.fetch()
	        .then(function(){
	          return self._fetch();
	        });
	    }

	    return this._fetch();
	  },

	  _fetch: function(){
	    if(!this.remote_id){
	      return;
	    }

	    var model = this.collection.findWhere({ id: this.remote_id });

	    // feetch from server
	    if(!model){
	      return this.collection.add({ id: this.remote_id })
	        .fetch({ remote: true });
	    }
	  },

	  onFetch: function(){
	    if(this.remote_id){
	      this.model = this.collection.findWhere({ id: this.remote_id });
	    }

	    if(!this.model) {
	      this.model = this.collection.add(
	        { copy_billing_address: true },
	        { silent: true }
	      );
	    }

	    this.collection.active = this.model;
	  },

	  render: function () {
	    var self = this;
	    var view = new View({
	      model: this.model
	    });
	    Radio.request('modal', 'open', view)
	      .then(function(args){
	        var buttons = args.view.getRegion('footer').currentView;
	        self.listenTo(buttons, 'action:save', self.saveCustomer);
	      });
	  },

	  saveCustomer: function(btn){

	    // validate
	    if( !this.model.isValid(true) ){
	      btn.trigger('state', ['error', null]);
	      return;
	    }

	    btn.trigger('state', ['loading', '']);

	    // copy billing info if required
	    if( this.model.get('copy_billing_address') ){
	      this.model.set({ shipping_address: this.model.get('billing_address') });
	    }

	    // save to both idb & server
	    this.model.save([], { remote: true })
	      .done(function(model){
	        var m = model[0];
	        m.trigger('modal:save', m); // @todo remove this
	        btn.trigger('state', ['success', null]);
	      })
	      .fail(function(error){
	        Radio.trigger('global', 'error', {
	          status: error.target.error.name,
	          message: error.target.error.message
	        });

	        // Cannot click save after this error:
	        // Uncaught DataCloneError: Failed to execute 'put' on 'IDBObjectStore':
	        // An object could not be cloned.
	        //btn.trigger('state', ['error', error.target.error.message]);
	      });
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 45 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];

	module.exports = app.FormView.extend({

	  template: 'modals.customer-edit',

	  tagName: 'form',

	  className: 'customer-edit',

	  initialize: function() {
	    // title
	    var action = this.model.get('id') ? 'edit' : 'add';
	    var title = app.polyglot.t('titles.' + action + '-customer');

	    // modal setup
	    this.modal = {
	      className: 'modal-lg',
	      header: {
	        title: title
	      }
	    };
	  },

	  bindings: {
	    'input[name="first_name"]': 'first_name',
	    'input[name="last_name"]' : 'last_name',
	    'input[name="email"]'     : 'email',
	    'input[name="username"]'  : 'username',

	    // billing
	    'input[name="billing_address[first_name]"]': 'billing_address',
	    'input[name="billing_address[last_name]"]' : 'billing_address',
	    'input[name="billing_address[company]"]'   : 'billing_address',
	    'input[name="billing_address[address_1]"]' : 'billing_address',
	    'input[name="billing_address[address_2]"]' : 'billing_address',
	    'input[name="billing_address[city]"]'      : 'billing_address',
	    'input[name="billing_address[postcode]"]'  : 'billing_address',
	    'input[name="billing_address[country]"]'   : 'billing_address',
	    'input[name="billing_address[state]"]'     : 'billing_address',
	    'input[name="billing_address[email]"]'     : 'billing_address',
	    'input[name="billing_address[phone]"]'     : 'billing_address',

	    // shipping
	    'input[name="shipping_address[first_name]"]': 'shipping_address',
	    'input[name="shipping_address[last_name]"]' : 'shipping_address',
	    'input[name="shipping_address[company]"]'   : 'shipping_address',
	    'input[name="shipping_address[address_1]"]' : 'shipping_address',
	    'input[name="shipping_address[address_2]"]' : 'shipping_address',
	    'input[name="shipping_address[city]"]'      : 'shipping_address',
	    'input[name="shipping_address[postcode]"]'  : 'shipping_address',
	    'input[name="shipping_address[country]"]'   : 'shipping_address',
	    'input[name="shipping_address[state]"]'     : 'shipping_address',

	    // special
	    'input[name="copy_billing_address"]' : {
	      observe: 'copy_billing_address',
	      onGet: function(value){
	        this.$('table.shipping-address tbody').toggle(!value);
	        return value;
	      },
	      onSet: function(value){
	        this.$('table.shipping-address tbody').toggle(!value);
	        return value;
	      }
	    }
	  },

	  onRender: function(){
	    if( this.model.get('id') ){
	      this.$('input[name="username"]').prop('disabled', true);
	    }
	  },

	  /**
	   * prevent abandoned new customers in collection
	   */
	  onDestroy: function(){
	    if(this.model.isNew()){
	      this.model.destroy();
	    }
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 46 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var ListRoute = __webpack_require__(47);

	module.exports = app.Router.extend({

	  routes: {
	    'coupons' : 'list'
	  },

	  list: function(){
	    return new ListRoute();
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 47 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Views = __webpack_require__(48);
	var Radio = __webpack_require__(9);
	var Pagination = app.Components.Pagination.View;

	module.exports = app.Route.extend({

	  initialize: function() {
	    this.container  = app.layout.getRegion('main');
	    this.filtered   = Radio.request('entities', 'get', {
	      type    : 'filtered',
	      name    : 'coupons',
	      perPage : 10
	    });
	  },

	  fetch: function() {
	    var collection = this.filtered.superset();
	    if( collection.isNew() ){
	      return collection.fetch()
	        .then(function(){
	          collection.fullSync();
	        });
	    } else {
	      this.filtered.setPage(0);
	    }
	  },

	  render: function() {
	    this.layout = new Views.Layout();

	    this.listenTo(this.layout, 'show', function () {
	      this.showActions();
	      this.showCoupons();
	      this.showPagination();
	    });

	    this.container.show( this.layout );
	  },

	  showActions: function(){
	    var view = new Views.Actions({
	      collection: this.filtered
	    });

	    // show
	    this.layout.getRegion('actions').show( view );
	  },

	  showCoupons: function() {
	    var view = new Views.Coupons({
	      collection: this.filtered
	    });

	    // show
	    this.layout.getRegion('list').show( view );
	  },

	  showPagination: function(){
	    var view = new Pagination({
	      collection: this.filtered
	    });
	    this.layout.getRegion('footer').show(view);
	  }

	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 48 */
/***/ function(module, exports) {

	/* WEBPACK VAR INJECTION */(function(global) {var app = global['POS'];
	var Views = {};

	Views.Layout = app.LayoutView.extend({
	  template: 'coupons.panel',
	  tagName : 'section',
	  regions : {
	    actions : '.list-actions',
	    list    : '.list',
	    footer  : '.list-footer'
	  },
	  attributes: {
	    'class' : 'panel coupons-page'
	  }

	});

	Views.Actions = app.ItemView.extend({
	  template: 'coupons.actions',
	  behaviors: {
	    Filter: {
	      behaviorClass: app.Behaviors.Filter
	    }
	  }
	});

	Views.Coupon = app.ItemView.extend({
	  tagName: 'li',
	  className: 'list-row',
	  template: 'coupons.list-item'
	});

	Views.NoCoupon = app.ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return app.polyglot.t('messages.no-coupons');
	  }
	});

	Views.Coupons = app.CollectionView.extend({
	  tagName: 'ul',
	  className: 'striped',
	  childView: Views.Coupon,
	  emptyView: Views.NoCoupon
	});

	module.exports = Views;
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ }
/******/ ]);
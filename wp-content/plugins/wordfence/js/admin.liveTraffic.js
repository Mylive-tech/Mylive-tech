(function($) {

	var LISTING_LIMIT = 50;

	var LiveTrafficViewModel = function(listings, filters) {
		var self = this;
		var listingIDTable = {};
		self.listings = ko.observableArray(listings);
		self.listings.subscribe(function(items) {
			listingIDTable = {};
			for (var i = 0; i < items.length; i++) {
				listingIDTable[items[i].id()] = 1;
			}
			//console.log(items);
		});
		self.hasListing = function(id) {
			return id in listingIDTable;
		};
		self.filters = ko.observableArray(filters);

		var urlGroupBy = new GroupByModel('url', 'URL');
		var groupBys = [
			new GroupByModel('type', 'Type'),
			new GroupByModel('user_login', 'Username'),
			new GroupByModel('statusCode', 'HTTP Response Code'),
			new GroupByModel('action', 'Firewall Response', 'enum', ['ok', 'throttled', 'lockedOut', 'blocked', 'blocked:waf']),
			new GroupByModel('ip', 'IP'),
			urlGroupBy,
			new GroupByModel('host', 'Host')
		];

		self.presetFiltersOptions = ko.observableArray([
			new PresetFilterModel('All Hits', "all", []),
			new PresetFilterModel('Humans', "humans", [new ListingsFilterModel(self, 'type', 'human')]),
			new PresetFilterModel('Registered Users', "users", [new ListingsFilterModel(self, 'userID', '0', '!=')]),
			new PresetFilterModel('Crawlers', "crawlers", [new ListingsFilterModel(self, 'type', 'bot')]),
			new PresetFilterModel('Google Crawlers', "google", [new ListingsFilterModel(self, 'isGoogle', '1')]),
			new PresetFilterModel('Pages Not Found', "404s", [new ListingsFilterModel(self, 'statusCode', '404')]),
			new PresetFilterModel('Logins and Logouts', "logins", [
				new ListingsFilterModel(self, 'action', 'login', 'contains'),
				new ListingsFilterModel(self, 'action', 'logout', 'contains')
			]),
			//new PresetFilterModel('Top Consumers', "top_consumers", [new ListingsFilterModel(self, 'statusCode', '200')], urlGroupBy),
			//new PresetFilterModel('Top 404s', "top_404s", [new ListingsFilterModel(self, 'statusCode', '404')], urlGroupBy),
			new PresetFilterModel('Locked Out', "lockedOut", [new ListingsFilterModel(self, 'action', 'lockedOut')]),
			new PresetFilterModel('Blocked', "blocked", [new ListingsFilterModel(self, 'action', 'blocked', 'contains')]),
			new PresetFilterModel('Blocked By Firewall', "blocked:waf", [new ListingsFilterModel(self, 'action', 'blocked:waf')])
		]);

		self.showAdvancedFilters = ko.observable(false);
		self.showAdvancedFilters.subscribe(function(val) {
			if (val && self.filters().length == 0) {
				self.addFilter();
			}
		});

		self.presetFiltersOptionsText = function(item) {
			return item.text();
		};

		self.selectedPresetFilter = ko.observable();
		self.selectedPresetFilter.subscribe(function(item) {
			var clonedFilters = ko.toJS(item.filters());
			var newFilters = [];
			for (var i = 0; i < clonedFilters.length; i++) {
				newFilters.push(new ListingsFilterModel(self, clonedFilters[i].param, clonedFilters[i].value, clonedFilters[i].operator));
			}
			self.filters(newFilters);
			self.groupBy(item.groupBy());
		});

		self.filters.subscribe(function() {
			self.checkQueryAndReloadListings();
		});

		self.addFilter = function() {
			self.filters.push(new ListingsFilterModel(self));
		};

		self.removeFilter = function(item) {
			self.filters.remove(item);
		};

		var currentFilterQuery = '';
		var getURLEncodedFilters = function() {
			var dataString = '';
			ko.utils.arrayForEach(self.filters(), function(filter) {
				if (filter.getValue() !== false) {
					dataString += filter.urlEncoded() + '&';
				}
			});
			var groupBy = self.groupBy();
			if (groupBy) {
				dataString += 'groupby=' + encodeURIComponent(groupBy.param()) + '&';
			}
			var startDate = self.startDate();
			if (startDate) {
				dataString += 'startDate=' + encodeURIComponent(startDate) + '&';
			}
			var endDate = self.endDate();
			if (endDate) {
				dataString += 'endDate=' + encodeURIComponent(endDate) + '&';
			}
			if (dataString.length > 1) {
				return dataString.substring(0, dataString.length - 1);
			}
			return '';
		};

		self.filterGroupByOptions = ko.observableArray(groupBys);

		self.filterGroupByOptionsText = function(item) {
			return item.text() || item.param();
		};

		self.groupBy = ko.observable();
		self.groupBy.subscribe(function() {
			self.checkQueryAndReloadListings();
		});

		self.startDate = ko.observable();
		self.startDate.subscribe(function() {
			// console.log('start date change ' + self.startDate());
			self.checkQueryAndReloadListings();
		});

		self.endDate = ko.observable();
		self.endDate.subscribe(function() {
			// console.log('end date change ' + self.endDate());
			self.checkQueryAndReloadListings();
		});

		/**
		 * Pulls down fresh traffic data and resets the list.
		 *
		 * @param options
		 */
		self.checkQueryAndReloadListings = function(options) {
			if (currentFilterQuery !== getURLEncodedFilters()) {
				self.reloadListings(options);
			}
		};
		self.reloadListings = function(options) {
			pullDownListings(options, function(listings) {
				var newListings = [];
				for (var i = 0; i < listings.length; i++) {
					newListings.push(new ListingModel(listings[i]));
				}
				self.listings(newListings);
			})
		};

		/**
		 * Used in the infinite scroll
		 */
		self.loadNextListings = function(callback) {
			var lastTimestamp = self.filters[0];
			pullDownListings({
				since: lastTimestamp,
				limit: LISTING_LIMIT,
				offset: self.listings().length
			}, function() {
				self.appendListings.apply(this, arguments);
				typeof callback === 'function' && callback.apply(this, arguments);
			});
		};

		self.getCurrentQueryString = function(options) {
			var queryOptions = {
				since: null,
				limit: LISTING_LIMIT,
				offset: 0
			};
			for (var prop in queryOptions) {
				if (queryOptions.hasOwnProperty(prop) && options && prop in options) {
					queryOptions[prop] = options[prop];
				}
			}
			currentFilterQuery = getURLEncodedFilters();
			var data = currentFilterQuery;
			for (prop in queryOptions) {
				if (queryOptions.hasOwnProperty(prop)) {
					var val = queryOptions[prop];
					if (val === null || val === undefined) {
						val = '';
					}
					data += '&' + encodeURIComponent(prop) + '=' + encodeURIComponent(val);
				}
			}
			return data;
		};

		var pullDownListings = function(options, callback) {
			var data = self.getCurrentQueryString(options);

			WFAD.ajax('wordfence_loadLiveTraffic', data, function(response) {
				if (!response || !response.success) {
					return;
				}
				callback && callback(response.data, response);
				self.sql(response.sql);
			});
		};

		self.prependListings = function(listings, response) {
			for (var i = listings.length - 1; i >= 0; i--) {
				// Prevent duplicates
				if (self.hasListing(listings[i].id)) {
					continue;
				}
				var listing = new ListingModel(listings[i]);
				listing.highlighted(true);
				self.listings.unshift(listing);
			}

			//self.listings.sort(function(a, b) {
			//	if (a.ctime() < b.ctime()) {
			//		return 1;
			//	} else if (a.ctime() > b.ctime()) {
			//		return -1;
			//	}
			//	return 0;
			//});
		};

		self.appendListings = function(listings, response) {
			var highlight = 3;
			for (var i = 0; i < listings.length; i++) {
				// Prevent duplicates
				if (self.hasListing(listings[i].id)) {
					continue;
				}
				var listing = new ListingModel(listings[i]);
				listing.highlighted(highlight-- > 0);
				self.listings.push(listing);
			}

			//self.listings.sort(function(a, b) {
			//	if (a.ctime() < b.ctime()) {
			//		return 1;
			//	} else if (a.ctime() > b.ctime()) {
			//		return -1;
			//	}
			//	return 0;
			//});
		};

		self.whitelistWAFParamKey = function(path, paramKey, failedRules) {
			WFAD.ajax('wordfence_whitelistWAFParamKey', {
				path: path,
				paramKey: paramKey,
				failedRules: failedRules
			}, function(response) {

			});
		};

		/*
			Blocking functions
		*/
		self.unblockIP = function(item) {
			WFAD.unblockIP(item.IP(), function() {
				ko.utils.arrayForEach(self.listings(), function(listing) {
					if (listing.IP() == item.IP()) {
						listing.blocked(false);
					}
				});
			});
		};
		self.unblockNetwork = function(item) {
			WFAD.unblockNetwork(item.ipRangeID());
		};
		self.blockIP = function(item) {
			WFAD.blockIP(item.IP(), 'Manual block by administrator', function() {
				ko.utils.arrayForEach(self.listings(), function(listing) {
					if (listing.IP() == item.IP()) {
						listing.blocked(true);
					}
				});
			});
		};

		// For debuggering-a-ding
		self.sql = ko.observable('');
	};

	var ListingModel = function(data) {
		var self = this;

		self.id = ko.observable(0);
		self.ctime = ko.observable(0);
		self.IP = ko.observable('');
		self.jsRun = ko.observable(0);
		self.statusCode = ko.observable(200);
		self.isGoogle = ko.observable(0);
		self.userID = ko.observable(0);
		self.newVisit = ko.observable(0);
		self.URL = ko.observable('');
		self.referer = ko.observable('');
		self.UA = ko.observable('');
		self.loc = ko.observable();
		self.type = ko.observable('');
		self.blocked = ko.observable(false);
		self.rangeBlocked = ko.observable(false);
		self.ipRangeID = ko.observable(-1);
		self.extReferer = ko.observable();
		self.browser = ko.observable();
		self.user = ko.observable();
		self.hitCount = ko.observable();
		self.username = ko.observable('');

		// New fields/columns
		self.action = ko.observable('');
		self.actionDescription = ko.observable(false);
		self.actionData = ko.observable();

		self.highlighted = ko.observable(false);
		//self.highlighted.subscribe(function(val) {
		//	if (val) {
		//		_classes += ' highlighted';
		//		self.cssClasses(_classes);
		//	} else {
		//		_classes.replace(/  highlighted(\s*|$)/, ' ');
		//		self.cssClasses(_classes);
		//	}
		//});

		for (var prop in data) {
			if (data.hasOwnProperty(prop)) {
				self[prop] !== undefined && self[prop](data[prop]);
			}
		}

		// Use the same format as these update.
		self.timeAgo = ko.pureComputed(function() {
			var serverTime = WFAD.serverMicrotime;
			return $(WFAD.showTimestamp(this.ctime(), serverTime)).text();
		}, self);

		var formatBlockedParam = function(text, maxLength) {
			maxLength = maxLength || 100;
			if (text && text.length > maxLength) {
				return text.substring(0, Math.round(maxLength)) + "\u2026";
				// return text.substring(0, Math.round(maxLength / 2)) + " ... " + text.substring(text.length - Math.round(maxLength / 2));
			}
			return text;
		};

		self.displayURL = ko.pureComputed(function() {
			return formatBlockedParam(self.URL(), 135);
		});

		self.firewallAction = ko.pureComputed(function() {
			var desc = '';
			switch (self.action()) {
				case 'lockedOut':
					return 'locked out from logging in';

				case 'blocked:wordfence':
					desc = self.actionDescription();
					if (desc && desc.toLowerCase().indexOf('block') === 0) {
						return 'b' + desc.substring(1);
					}
					return 'blocked for ' + desc;

				case 'blocked:wfsn':
					return 'blocked by the Wordfence Security Network';

				case 'blocked:waf':
					var data = self.actionData();
					if (typeof data === 'object') {
						var paramKey = WFAD.base64_decode(data.paramKey);
						var paramValue = WFAD.base64_decode(data.paramValue);
						// var category = data.category;

						var matches = paramKey.match(/([a-z0-9_]+\.[a-z0-9_]+)(?:\[(.+?)\](.*))?/i);
						desc = self.actionDescription();
						if (matches) {
							switch (matches[1]) {
								case 'request.queryString':
									desc = self.actionDescription() + ' in query string: ' + matches[2] + '=' + formatBlockedParam(encodeURIComponent(paramValue));
									break;
								case 'request.body':
									desc = self.actionDescription() + ' in POST body: ' + matches[2] + '=' + formatBlockedParam(encodeURIComponent(paramValue));
									break;
								case 'request.cookie':
									desc = self.actionDescription() + ' in cookie: ' + matches[2] + '=' + formatBlockedParam(encodeURIComponent(paramValue));
									break;
								case 'request.fileNames':
									desc = 'a ' + self.actionDescription() + ' in file: ' + matches[2] + '=' + formatBlockedParam(encodeURIComponent(paramValue));
									break;
							}
						}
						if (desc) {
							return 'blocked by firewall for ' + desc;
						}
						return 'blocked by firewall';
					}
					return 'blocked by firewall for ' + self.actionDescription();
			}
			return desc;
		});

		self.cssClasses = ko.pureComputed(function() {
			var classes = 'wfActEvent';
			if (self.statusCode() == 403) {
				classes += ' wfActionBlocked';
			}
			if (self.statusCode() == 404) {
				classes += ' wf404';
			}
			if (self.jsRun() == 1) {
				classes += ' wfHuman';
			}
			if (self.actionData() && self.actionData().learningMode) {
				classes += ' wfWAFLearningMode';
			}
			if (self.highlighted()) {
				classes += ' highlighted';
			}
			return classes;
		});
	};

	var ListingsFilterModel = function(viewModel, param, value, operator) {
		var self = this;
		self.viewModel = viewModel;
		self.param = ko.observable('');
		self.value = ko.observable('');
		self.operator = ko.observable('');

		self.param(param);
		self.value(value);
		self.operator(operator || '=');

		var filterChanged = function() {
			self.viewModel && self.viewModel.checkQueryAndReloadListings && self.viewModel.checkQueryAndReloadListings();
		};
		self.param.subscribe(filterChanged);
		self.operator.subscribe(filterChanged);
		self.value.subscribe(function(value) {
			if (value instanceof FilterParamEnumOptionModel && value.operator()) {
				self.selectedFilterOperatorOptionValue(value.operator());
			}
			filterChanged();
		});

		var equalsOperator = new FilterOperatorModel('=');
		var notEqualsOperator = new FilterOperatorModel('!=', '\u2260');
		var containsOperator = new FilterOperatorModel('contains');
		var matchOperator = new FilterOperatorModel('match');
		self.filterOperatorOptions = ko.observableArray([
			equalsOperator,
			notEqualsOperator,
			containsOperator,
			matchOperator
		]);

		self.filterParamOptions = ko.observableArray([
			new FilterParamModel('type', 'Type', 'enum', [
				new FilterParamEnumOptionModel('human', 'Human'),
				new FilterParamEnumOptionModel('bot', 'Bot')
			]),
			new FilterParamModel('user_login', 'Username'),
			new FilterParamModel('userID', 'UserID'),
			new FilterParamModel('isGoogle', 'Google Bot', 'bool'),
			new FilterParamModel('ip', 'IP'),
			new FilterParamModel('ua', 'User Agent'),
			new FilterParamModel('referer', 'Referer'),
			new FilterParamModel('url', 'URL'),
			new FilterParamModel('statusCode', 'HTTP Response Code'),
			new FilterParamModel('action', 'Firewall Response', 'enum', [
				new FilterParamEnumOptionModel('', 'OK'),
				new FilterParamEnumOptionModel('throttled', 'Throttled'),
				new FilterParamEnumOptionModel('lockedOut', 'Locked Out'),
				new FilterParamEnumOptionModel('blocked', 'Blocked', containsOperator),
				new FilterParamEnumOptionModel('blocked:waf', 'Blocked WAF')
			]),
			new FilterParamModel('action', 'Logins', 'enum', [
				new FilterParamEnumOptionModel('loginOK', 'Logged In'),
				new FilterParamEnumOptionModel('loginFail', 'Failed Login'),
				new FilterParamEnumOptionModel('loginFailInvalidUsername', 'Failed Login: Invalid Username'),
				new FilterParamEnumOptionModel('loginFailValidUsername', 'Failed Login: Valid Username')
			]),
			new FilterParamModel('action', 'Security Event')
		]);

		self.filterParamOptionsText = function(item) {
			return item.text() || item.param();
		};

		self.selectedFilterParamOptionValue = ko.observable();
		self.selectedFilterParamOptionValue.subscribe(function(item) {
			self.param(item && item.param ? item.param() : '');
		});

		ko.utils.arrayForEach(self.filterParamOptions(), function(item) {
			if (self.param() == item.param()) {
				switch (item.type()) {
					case 'enum':
						// console.log(self.param(), item.param(), self.value(), values);
						switch (self.operator()) {
							case '=':
								ko.utils.arrayForEach(item.values(), function(enumOption) {
									if (enumOption.value() == self.value()) {
										self.selectedFilterParamOptionValue(item);
									}
								});
								break;
						}
						break;

					default:
						self.selectedFilterParamOptionValue(item);
						break;
				}
			}
		});

		self.filterOperatorOptionsText = function(item) {
			return item.text() || item.operator();
		};

		self.selectedFilterOperatorOptionValue = ko.observable();
		self.selectedFilterOperatorOptionValue.subscribe(function(item) {
			self.operator(item.operator());
		});

		ko.utils.arrayForEach(self.filterOperatorOptions(), function(item) {
			if (self.operator() == item.operator()) {
				self.selectedFilterOperatorOptionValue(item);
			}
		});

		self.getValue = function() {
			var value = self.value() instanceof FilterParamEnumOptionModel ? self.value().value() : self.value();
			return (typeof value === 'string' || typeof value === 'number') ? value : false;
		};
		self.urlEncoded = function() {
			var value = self.getValue();
			return 'param[]=' + encodeURIComponent(self.param()) + '&value[]=' + encodeURIComponent(value) +
				'&operator[]=' + encodeURIComponent(self.operator());
		};
	};

	var PresetFilterModel = function(text, value, filters, groupBy) {
		this.text = ko.observable('');
		this.value = ko.observable('');
		this.filters = ko.observableArray(filters);
		this.groupBy = ko.observable(groupBy);

		this.text(text);
		this.value(value);
	};

	var FilterParamModel = function(param, text, type, values) {
		this.text = ko.observable('');
		this.param = ko.observable('');
		this.type = ko.observable('');
		this.values = ko.observableArray(values);

		this.text(text);
		this.param(param);
		this.type(type || 'text');

		this.optionsText = function(item) {
			if (item instanceof FilterParamEnumOptionModel) {
				return item.label() || item.value();
			}
			return item;
		}
	};

	var FilterParamEnumOptionModel = function(value, label, operator) {
		this.value = ko.observable('');
		this.label = ko.observable('');
		this.operator = ko.observable('');

		this.value = ko.observable(value);
		this.label = ko.observable(label);
		this.operator = ko.observable(operator);

		this.toString = function() {
			return this.value();
		}
	};

	var FilterOperatorModel = function(operator, text) {
		this.text = ko.observable('');
		this.operator = ko.observable('');

		this.text(text);
		this.operator(operator);
	};

	var GroupByModel = function(param, text) {
		this.text = ko.observable('');
		this.param = ko.observable('');

		this.text(text);
		this.param(param);
	};

	ko.bindingHandlers.datetimepicker = {
		init: function(element, valueAccessor, allBindingsAccessor) {
			//initialize datepicker with some optional options
			var options = allBindingsAccessor().datepickerOptions || {},
				$el = $(element);

			$el.datetimepicker(options);

			//handle the field changing by registering datepicker's changeDate event
			ko.utils.registerEventHandler(element, "changeDate", function() {
				var observable = valueAccessor();
				observable($el.datetimepicker("getDate"));
			});

			//handle disposal (if KO removes by the template binding)
			ko.utils.domNodeDisposal.addDisposeCallback(element, function() {
				$el.datetimepicker("destroy");
			});

		},
		update: function(element, valueAccessor) {
			var value = ko.utils.unwrapObservable(valueAccessor()),
				$el = $(element);

			//handle date data coming via json from Microsoft
			if (String(value).indexOf('/Date(') == 0) {
				value = new Date(parseInt(value.replace(/\/Date\((.*?)\)\//gi, "$1")));
			}

			var current = $el.datetimepicker("getDate");

			if (value - current !== 0) {
				$el.datetimepicker("setDate", value);
			}
		}
	};


	$(function() {
		var liveTrafficWrapper = $('#wf-live-traffic');
		WFAD.wfLiveTraffic = new LiveTrafficViewModel();
		ko.applyBindings(WFAD.wfLiveTraffic, liveTrafficWrapper.get(0));
		liveTrafficWrapper.find('form').submit();
		WFAD.mode = 'liveTraffic';

		var legend = $('#wf-live-traffic-legend');
		var adminBar = $('#wpadminbar');

		var hasScrolled = false;
		var loadingListings = false;
		$(window).on('scroll', function() {
			var win = $(this);
			if (liveTrafficWrapper.offset().top < win.scrollTop() + adminBar.outerHeight() + 20) {
				legend.addClass('sticky');
			} else {
				legend.removeClass('sticky');
			}

			// console.log(win.scrollTop() + window.innerHeight, liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top);
			var currentScrollBottom = win.scrollTop() + window.innerHeight;
			var scrollThreshold = liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top;
			if (hasScrolled && !loadingListings && currentScrollBottom >= scrollThreshold) {
				// console.log('infinite scroll');

				loadingListings = true;
				hasScrolled = false;
				WFAD.wfLiveTraffic.loadNextListings(function() {
					loadingListings = false;
				});
			} else if (currentScrollBottom < scrollThreshold) {
				hasScrolled = true;
				// console.log('no infinite scroll');
			}
		});
	});
})
(jQuery);

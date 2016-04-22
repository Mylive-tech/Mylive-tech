/**
 * G1 admin namespace
 */
var G1admin = {};

(function($) {
jQuery(document).ready(function() {
    // replace revslider buttons
    $("#dialog_insert_button ul.list-buttons").each(function() {
        var $ul = $(this);

        if (typeof UniteLayersRev === 'object') {
            /*
            var origFunc = UniteLayersRev.setInitCaptionClasses;

            UniteLayersRev.setInitCaptionClasses = function(url) {
                var arr = $.parseJSON(url);

                // add classes here
                arr.push('3clicks-class-1');
                arr.push('3clicks-class-2');

                origFunc(JSON.stringify(arr));
            };
            */
        }

        // define buttons here
        var buttons = [
            //'<a class="g1-revslider-button" href="javascript:UniteLayersRev.insertButton(\'g1-button g1-style-solid\',\'html content 1\')">Button desc 1</a>',
        ];

        for (var i = 0; i < buttons.length; i += 1) {
            $ul.append($('<li>').append(buttons[i]));
        }
    });

	// INITIALIZE COLOR PICKER
	jQuery('.g1-field-color').each(function(){
		var $this = jQuery(this);
		
		var $container = $this.find('.g1-color-picker-container').eq(0);
		var $input = $this.find('input').eq(0);
		var $preview = $this.find('.g1-color-picker-preview').eq(0);
		var $previewCurrent = $this.find('.g1-color-picker-preview-current').eq(0);
		var $previewNew = $this.find('.g1-color-picker-preview-new').eq(0);
		var $toggle = $this.find('.g1-color-picker-toggle').eq(0);
		
		var openColorPicker = function(){
			$preview.addClass('on');
			$container.addClass('on');
			
			$container.farbtastic(function callback(color){			 			 
				 $previewNew.css('background-color', color);
				 $input.attr('value', color);
			 });
			
			jQuery.farbtastic($container).setColor($input.attr('value'));		
		};
		
		$this.blur(function(){
			if ( $preview.is( '.on' ) ) {
				//$previewCurrent.css( 'background-image', 'none' );
				$previewCurrent.css( 'background', $previewNew.css('background-color') );
				$preview.removeClass( 'on' );
				$container.removeClass( 'on' );
			}	
		});
		
		$previewCurrent.click(function(){			
			if ( $preview.is('.on') ) {	
				$previewNew.css('background-color', $previewCurrent.css('background-color'));			
			} else {				
				openColorPicker();
			}	
		});		
		
		$toggle.click( openColorPicker );	
	});
	
	jQuery('.g1-option-view-color input').blur(function(){
		var $preview = jQuery(this).siblings('.g1-color-picker-preview').eq(0);
		$preview.find('.g1-color-picker-preview-current').css('background-color', jQuery(this).attr('value'));
	});
	
	
	// INITIALIZE FORM UNITS
	jQuery('.g1-option-view .g1-help').each(function(){
		var context = this;		
		jQuery('.g1-help-content',context).hide();
		jQuery('.g1-help-toggle', context).toggleClass('g1-help-toggle-off').click(function(){
			jQuery(this).toggleClass('g1-help-toggle-on').toggleClass('g1-help-toggle-off');
			jQuery('.g1-help-content', context).toggle('fast');
		});
	});
	
	
	jQuery( '.g1-field-image-choice' ).each( function() {
		var container = jQuery(this);
		jQuery( 'div:has( input:checked )', this).addClass( 'g1-checked' );
				
		jQuery( 'div input', this ).change( function() {
			jQuery(this).blur();
			jQuery( 'div', container ).removeClass( 'g1-checked' );
			jQuery( 'div:has( input:checked )', container).addClass( 'g1-checked' );
		} ); 
		
	} );
	
	jQuery('input[type="range"]').rangeinput({ progress: true });

	// INITIALIZE SHORTCODE GENERATOR
	if ( typeof tinymce !== "undefined" && tinymce && typeof tinymce.create === 'function') {
        jQuery( '.g1-shortcode-generator' ).parent().each( function() {
			var $this = jQuery( this );

			var image = $this.find( 'h1 img' ).attr( 'src' );
			var title = $this.find( 'h1' ).text();

			var id = $this.attr( 'id' );
			id = id.replace( /\-/g, '_' );

			tinymce.create('tinymce.plugins.' + id, {
				init : function(ed, url){
                    var generator = G1ShortcodeGenerator.getInstance(ed, $this.attr( 'id' ));

			        ed.addButton(id, {
			        	title : title,
			            	onclick : function() {
			            		generator.showUI();
			                },
			            image: image
			        });
			    },
			    createControl : function(n, cm) {
			    	return null;
			    }
			});
		
			tinymce.PluginManager.add( id, tinymce.plugins[ id ]);
		});
	}

    function registerShortcodeGeneratorForHTMLEditor()
    {
        if ( typeof(QTags) !== 'function' ) {
            return;
        }

        if ( typeof(G1ShortcodeGenerator) === 'undefined' ) {
            return;
        }

        var qt = QTags;

        var generator = G1ShortcodeGenerator.getInstance(null, 'g1_shortcode_manager');

        qt.ShortcodeGeneratorHTMLButton = function() {
            qt.Button.call(this, 'g1_shotgen', '[/]', 'f', 'General Shortcode Generator');
        };
        qt.ShortcodeGeneratorHTMLButton.prototype = new qt.Button();
        qt.ShortcodeGeneratorHTMLButton.prototype.callback = function(e, c) {
            generator.showUI();
        };

        edButtons[edButtons.length] = new qt.ShortcodeGeneratorHTMLButton();
    }

    registerShortcodeGeneratorForHTMLEditor();

    $('.g1-option-view-multichoice-text input[type="checkbox"]').live('change', function() {
        reload_multichoice_text_option_value($(this).parents('.g1-option-view-multichoice-text'));
    });

    $(document).bind('g1-field-loaded', function() {
        populate_multichoice_options_from_value();
    });

    function reload_multichoice_text_option_value($option) {
        var $value = $('input[type="hidden"]', $option);

        var values = [];
        $('input[type="checkbox"]:checked', $option).each(function() {
            values.push($(this).val());
        });

        $value.val(values.join(','));
    }

    function populate_multichoice_options_from_value() {
        $('.g1-option-view-multichoice-text').each(function() {
            var value = $(this).find('input[type=hidden]').val();

            if (!value) {
                return;
            }

            var values = value.split(',');

            for (var i = 0; i < values.length; i += 1) {
                $(this).find('input[name=' + values[i] + ']').attr('checked', 'checked');
            }
        });
    }

    populate_multichoice_options_from_value();

    // HELP ToGGLES
    $('.g1-help').each(function() {
        var $this = $(this);
        var $label = $('.g1-label label', $this.parent());
        var $helpSwitch = $('<a class="g1-help-switch">[?]</a>');
        $helpSwitch.click(function(e) {
            e.preventDefault();
            $this.toggle();
        });

        $label.append($helpSwitch);
    });

    $(window).load(function() {
        if ($('body').hasClass('nav-menus-php')) {
            $(document).bind('DOMNodeInserted', function(e) {
                var $target = $(e.target);

                if ($target.hasClass('menu-item')) {
                    // 100ms delay to read new menu item depth value
                    setTimeout(function() {
                        registerMenuItemElements($target);
                    }, 100);

                }
            });
        }
    });

    $('#menu-to-edit a.item-edit').click(function() {
        var $menuItem = $(this).parents('.menu-item');

        // menu item registered?
        if ($menuItem.find('.g1-type,.g1-color').length > 0) {
            return;
        }

        registerMenuItemElements($menuItem);
    });

    function registerMenuItemElements ($this) {
        var $cssClassesInput = $('.field-css-classes input.edit-menu-item-classes', $this);

        var isMenuDepth0 = $this.hasClass('menu-item-depth-0');
        var isMenuDepth1 = $this.hasClass('menu-item-depth-1');

        // item custom components
        var $typeSelect = $this.find('.g1-type-wrapper');
        var $colorSelect = $this.find('.g1-color-wrapper');
        var $iconSelect = $this.find('.g1-icon-wrapper');

        if (!isMenuDepth0 && !isMenuDepth1) {
            $typeSelect.remove();
            $colorSelect.remove();
            $iconSelect.remove();

            $cssClassesInput.val('');
            return;
        }

        if (isMenuDepth0) {
            $colorSelect.remove();
            $iconSelect.remove();

            // remove color and icon classes
            var classes = $cssClassesInput.val().split(' ');
            var newClasses = [];

            for (var i = 0; i < classes.length; i += 1) {
                    if (!classes[i].match('g1-color') && !classes[i].match('g1-menu-icon')) {
                        newClasses.push(classes[i]);
                    }

            }
            $cssClassesInput.val(newClasses.join(' '));
        }

        if (isMenuDepth1) {
            $typeSelect.remove();

            // remove type class
            var classes = $cssClassesInput.val().split(' ');
            var newClasses = [];

            for (var i = 0; i < classes.length; i += 1) {
                if (!classes[i].match('g1-type')) {
                    newClasses.push(classes[i]);
                }

            }
            $cssClassesInput.val(newClasses.join(' '));
        }

        var $cssClassesAttributeField = $('.field-css-classes', $this);

        var matches = $this.attr('id').match(/[0-9]+$/);
        var menuItemId = matches[0];

        var selectCallback = function () {
            var value = $(this).find('option:selected').val();
            //var field = $(this).data('field');
            var field = $(this).attr('data-g1-field');

            var classes = [];
            if ($cssClassesInput.val().length > 0) {
                classes = $cssClassesInput.val().split(' ');
            }

            for (var i in classes) {
                if (classes.hasOwnProperty(i)) {
                    if (classes[i].match(field)) {
                        classes.splice(i, 1);
                    }
                }
            }

            var foundIndex = $.inArray(value, classes);

            // add class
            if (value.length > 0 && foundIndex === -1) {
                classes.push(value);
            }

            $cssClassesInput.val(classes.join(' '));
        };

        var createSelectField = function ( label, fieldName, id, options, $storage ) {
            var storage = $storage.val();

            var field = '<p class="' + fieldName + '-wrapper description description-thin">';
            var fieldLabel = '<label for="'+ id +'">'+ label +'</label>';

            var fieldSelect = '<select class="'+ fieldName +' widefat" id="'+ id +'" data-g1-field="'+ fieldName +'">';

            for (var i in options) {
                if (options.hasOwnProperty(i)) {
                    var selected = '';

                    if (i.length > 0 && storage.match(i)) {
                        selected = 'selected="selected"';
                    }

                    var option = '<option '+ selected +' value="'+ i +'">'+ options[i] +'</option>';
                    fieldSelect += option;
                }
            }

            fieldSelect += '</select>';

            field += fieldLabel;
            field += '<br>';
            field += fieldSelect;

            var $field = $(field);

            $field.find('select').change(selectCallback);

            return $field;
        };

        // define 'type' option
        var types = {
            '': '',
            'g1-type-drops':'Dropdown',
            'g1-type-tile-2': 'Tiles x2',
            'g1-type-tile-3': 'Tiles x3',
            'g1-type-tile-4': 'Tiles x4',
            'g1-type-tile-5': 'Tiles x5',
            'g1-type-tile-6': 'Tiles x6',
            'g1-type-column-2': 'Columns x2',
            'g1-type-column-3': 'Columns x3',
            'g1-type-column-4': 'Columns x4',
            'g1-type-column-5': 'Columns x5',
            'g1-type-column-6': 'Columns x6'
        };

        if ($typeSelect.length === 0 && isMenuDepth0) {
            var $type = createSelectField('Menu type', 'g1-type', 'g1-menu-type-' + menuItemId, types, $cssClassesInput);
            $cssClassesAttributeField.before($type);
        }

        // define 'color' option
        var colors = {
            '': '',
            'g1-color-orange-1': 'Orange 1',
            'g1-color-orange-2': 'Orange 2',
            'g1-color-red-1': 'Red 1',
            'g1-color-red-2': 'Red 2',
            'g1-color-blue-1': 'Blue 1',
            'g1-color-blue-2': 'Blue 2',
            'g1-color-purple-1': 'Purple 1',
            'g1-color-purple-2': 'Purple 2',
            'g1-color-green-1': 'Green 1',
            'g1-color-green-2': 'Green 2',
            'g1-color-turquoise-1': 'Turquoise 1',
            'g1-color-turquoise-2': 'Turquoise 2'
        };

        if ($colorSelect.length === 0 && isMenuDepth1) {
            var $color = createSelectField('Tile color', 'g1-color', 'g1-color-' + menuItemId, colors, $cssClassesInput);
            $cssClassesAttributeField.before($color);
        }

        // define 'icon' option
        var icons = {
            '': ''
        };

        var iconList = getFontAwesomeList();
        for (var i in iconList) {
            var icon = iconList[i];
            var key = 'g1-menu-icon-' + icon['name'];
            var value = icon['name'];

            icons[key] = value;
        }

        if ($iconSelect.length === 0 && isMenuDepth1) {
            var $icon = createSelectField('Icon', 'g1-menu-icon', 'g1-menu-icon-' + menuItemId, icons, $cssClassesInput);
            $cssClassesAttributeField.before($icon);
        }

        $cssClassesInput.keyup(function() {
            var selects = [];

            if (typeof $type !== 'undefined') {
                selects.push($('select', $type));;
            }

            if (typeof $color !== 'undefined') {
                selects.push($('select', $color));;
            }

            if (typeof $icon !== 'undefined') {
                selects.push($('select', $icon));;
            }

            for (var i in selects) {
                var $select = selects[i];
                var $selectedOption = $select.find('option:selected');

                // remove selection
                if (!$cssClassesInput.val().match($selectedOption.val())) {
                    $selectedOption.removeAttr('selected');
                }
            }
        });
    }

    function getFontAwesomeList () {
        var fontAwesomeList = [{"code":"f042","name":"adjust"},{"code":"f170","name":"adn"},{"code":"f037","name":"align-center"},{"code":"f039","name":"align-justify"},{"code":"f036","name":"align-left"},{"code":"f038","name":"align-right"},{"code":"f0f9","name":"ambulance"},{"code":"f13d","name":"anchor"},{"code":"f17b","name":"android"},{"code":"f107","name":"angle-down"},{"code":"f104","name":"angle-left"},{"code":"f105","name":"angle-right"},{"code":"f106","name":"angle-up"},{"code":"f179","name":"apple"},{"code":"f187","name":"archive"},{"code":"f063","name":"arrow-down"},{"code":"f060","name":"arrow-left"},{"code":"f061","name":"arrow-right"},{"code":"f062","name":"arrow-up"},{"code":"f069","name":"asterisk"},{"code":"f04a","name":"backward"},{"code":"f05e","name":"ban-circle"},{"code":"f080","name":"bar-chart"},{"code":"f02a","name":"barcode"},{"code":"f0c3","name":"beaker"},{"code":"f0fc","name":"beer"},{"code":"f0a2","name":"bell"},{"code":"f0f3","name":"bell-alt"},{"code":"f171","name":"bitbucket"},{"code":"f172","name":"bitbucket-sign"},{"code":"f032","name":"bold"},{"code":"f0e7","name":"bolt"},{"code":"f02d","name":"book"},{"code":"f02e","name":"bookmark"},{"code":"f097","name":"bookmark-empty"},{"code":"f0b1","name":"briefcase"},{"code":"f15a","name":"btc"},{"code":"f188","name":"bug"},{"code":"f0f7","name":"building"},{"code":"f0a1","name":"bullhorn"},{"code":"f140","name":"bullseye"},{"code":"f073","name":"calendar"},{"code":"f133","name":"calendar-empty"},{"code":"f030","name":"camera"},{"code":"f083","name":"camera-retro"},{"code":"f0d7","name":"caret-down"},{"code":"f0d9","name":"caret-left"},{"code":"f0da","name":"caret-right"},{"code":"f0d8","name":"caret-up"},{"code":"f0a3","name":"certificate"},{"code":"f046","name":"check"},{"code":"f096","name":"check-empty"},{"code":"f147","name":"check-minus"},{"code":"f14a","name":"check-sign"},{"code":"f078","name":"chevron-down"},{"code":"f053","name":"chevron-left"},{"code":"f054","name":"chevron-right"},{"code":"f13a","name":"chevron-sign-down"},{"code":"f137","name":"chevron-sign-left"},{"code":"f138","name":"chevron-sign-right"},{"code":"f139","name":"chevron-sign-up"},{"code":"f077","name":"chevron-up"},{"code":"f111","name":"circle"},{"code":"f0ab","name":"circle-arrow-down"},{"code":"f0a8","name":"circle-arrow-left"},{"code":"f0a9","name":"circle-arrow-right"},{"code":"f0aa","name":"circle-arrow-up"},{"code":"f10c","name":"circle-blank"},{"code":"f0c2","name":"cloud"},{"code":"f0ed","name":"cloud-download"},{"code":"f0ee","name":"cloud-upload"},{"code":"f158","name":"cny"},{"code":"f121","name":"code"},{"code":"f126","name":"code-fork"},{"code":"f0f4","name":"coffee"},{"code":"f013","name":"cog"},{"code":"f085","name":"cogs"},{"code":"f150","name":"collapse"},{"code":"f117","name":"collapse-alt"},{"code":"f151","name":"collapse-top"},{"code":"f0db","name":"columns"},{"code":"f075","name":"comment"},{"code":"f0e5","name":"comment-alt"},{"code":"f086","name":"comments"},{"code":"f0e6","name":"comments-alt"},{"code":"f14e","name":"compass"},{"code":"f0c5","name":"copy"},{"code":"f09d","name":"credit-card"},{"code":"f125","name":"crop"},{"code":"f13c","name":"css3"},{"code":"f0c4","name":"cut"},{"code":"f0e4","name":"dashboard"},{"code":"f108","name":"desktop"},{"code":"f103","name":"double-angle-down"},{"code":"f100","name":"double-angle-left"},{"code":"f101","name":"double-angle-right"},{"code":"f102","name":"double-angle-up"},{"code":"f01a","name":"download"},{"code":"f019","name":"download-alt"},{"code":"f17d","name":"dribble"},{"code":"f16b","name":"dropbox"},{"code":"f044","name":"edit"},{"code":"f14b","name":"edit-sign"},{"code":"f052","name":"eject"},{"code":"f141","name":"ellipsis-horizontal"},{"code":"f142","name":"ellipsis-vertical"},{"code":"f0e0","name":"envelope"},{"code":"f003","name":"envelope-alt"},{"code":"f12d","name":"eraser"},{"code":"f153","name":"eur"},{"code":"f0ec","name":"exchange"},{"code":"f12a","name":"exclamation"},{"code":"f06a","name":"exclamation-sign"},{"code":"f152","name":"expand"},{"code":"f116","name":"expand-alt"},{"code":"f08e","name":"external-link"},{"code":"f14c","name":"external-link-sign"},{"code":"f070","name":"eye-close"},{"code":"f06e","name":"eye-open"},{"code":"f09a","name":"facebook"},{"code":"f082","name":"facebook-sign"},{"code":"f03d","name":"facetime-video"},{"code":"f049","name":"fast-backward"},{"code":"f050","name":"fast-forward"},{"code":"f182","name":"female"},{"code":"f0fb","name":"fighter-jet"},{"code":"f15b","name":"file"},{"code":"f016","name":"file-alt"},{"code":"f15c","name":"file-text"},{"code":"f0f6","name":"file-text-alt"},{"code":"f008","name":"film"},{"code":"f0b0","name":"filter"},{"code":"f06d","name":"fire"},{"code":"f134","name":"fire-extinguisher"},{"code":"f024","name":"flag"},{"code":"f11d","name":"flag-alt"},{"code":"f11e","name":"flag-checkered"},{"code":"f16e","name":"flickr"},{"code":"f07b","name":"folder-close"},{"code":"f114","name":"folder-close-alt"},{"code":"f07c","name":"folder-open"},{"code":"f115","name":"folder-open-alt"},{"code":"f031","name":"font"},{"code":"f0f5","name":"food"},{"code":"f04e","name":"forward"},{"code":"f180","name":"foursquare"},{"code":"f119","name":"frown"},{"code":"f0b2","name":"fullscreen"},{"code":"f11b","name":"gamepad"},{"code":"f154","name":"gbp"},{"code":"f06b","name":"gift"},{"code":"f09b","name":"github"},{"code":"f113","name":"github-alt"},{"code":"f092","name":"github-sign"},{"code":"f184","name":"gittip"},{"code":"f000","name":"glass"},{"code":"f0ac","name":"globe"},{"code":"f0d5","name":"google-plus"},{"code":"f0d4","name":"google-plus-sign"},{"code":"f0c0","name":"group"},{"code":"f0fd","name":"h-sign"},{"code":"f0a7","name":"hand-down"},{"code":"f0a5","name":"hand-left"},{"code":"f0a4","name":"hand-right"},{"code":"f0a6","name":"hand-up"},{"code":"f0a0","name":"hdd"},{"code":"f025","name":"headphones"},{"code":"f004","name":"heart"},{"code":"f08a","name":"heart-empty"},{"code":"f015","name":"home"},{"code":"f0f8","name":"hospital"},{"code":"f13b","name":"html5"},{"code":"f01c","name":"inbox"},{"code":"f03b","name":"indent-left"},{"code":"f03c","name":"indent-right"},{"code":"f129","name":"info"},{"code":"f05a","name":"info-sign"},{"code":"f156","name":"inr"},{"code":"f16d","name":"instagram"},{"code":"f033","name":"italic"},{"code":"f157","name":"jpy"},{"code":"f084","name":"key"},{"code":"f11c","name":"keyboard"},{"code":"f159","name":"krw"},{"code":"f109","name":"laptop"},{"code":"f06c","name":"leaf"},{"code":"f0e3","name":"legal"},{"code":"f094","name":"lemon"},{"code":"f149","name":"level-down"},{"code":"f148","name":"level-up"},{"code":"f0eb","name":"lightbulb"},{"code":"f0c1","name":"link"},{"code":"f0e1","name":"linkedin"},{"code":"f08c","name":"linkedin-sign"},{"code":"f17c","name":"linux"},{"code":"f03a","name":"list"},{"code":"f022","name":"list-alt"},{"code":"f0cb","name":"list-ol"},{"code":"f0ca","name":"list-ul"},{"code":"f124","name":"location-arrow"},{"code":"f023","name":"lock"},{"code":"f175","name":"long-arrow-down"},{"code":"f177","name":"long-arrow-left"},{"code":"f178","name":"long-arrow-right"},{"code":"f176","name":"long-arrow-up"},{"code":"f0d0","name":"magic"},{"code":"f076","name":"magnet"},{"code":"f122","name":"mail-reply-all"},{"code":"f183","name":"male"},{"code":"f041","name":"map-marker"},{"code":"f136","name":"maxcdn"},{"code":"f0fa","name":"medkit"},{"code":"f11a","name":"meh"},{"code":"f130","name":"microphone"},{"code":"f131","name":"microphone-off"},{"code":"f068","name":"minus"},{"code":"f056","name":"minus-sign"},{"code":"f146","name":"minus-sign-alt"},{"code":"f10b","name":"mobile-phone"},{"code":"f0d6","name":"money"},{"code":"f186","name":"moon"},{"code":"f047","name":"move"},{"code":"f001","name":"music"},{"code":"f011","name":"off"},{"code":"f00c","name":"ok"},{"code":"f05d","name":"ok-circle"},{"code":"f058","name":"ok-sign"},{"code":"f0c6","name":"paper-clip"},{"code":"f0ea","name":"paste"},{"code":"f04c","name":"pause"},{"code":"f040","name":"pencil"},{"code":"f095","name":"phone"},{"code":"f098","name":"phone-sign"},{"code":"f03e","name":"picture"},{"code":"f0d2","name":"pinterest"},{"code":"f0d3","name":"pinterest-sign"},{"code":"f072","name":"plane"},{"code":"f04b","name":"play"},{"code":"f01d","name":"play-circle"},{"code":"f144","name":"play-sign"},{"code":"f067","name":"plus"},{"code":"f055","name":"plus-sign"},{"code":"f0fe","name":"plus-sign-alt"},{"code":"f02f","name":"print"},{"code":"f08d","name":"pushpin"},{"code":"f12e","name":"puzzle-piece"},{"code":"f029","name":"qrcode"},{"code":"f128","name":"question"},{"code":"f059","name":"question-sign"},{"code":"f10d","name":"quote-left"},{"code":"f10e","name":"quote-right"},{"code":"f074","name":"random"},{"code":"f021","name":"refresh"},{"code":"f00d","name":"remove"},{"code":"f05c","name":"remove-circle"},{"code":"f057","name":"remove-sign"},{"code":"f18b","name":"renren"},{"code":"f0c9","name":"reorder"},{"code":"f01e","name":"repeat"},{"code":"f112","name":"reply"},{"code":"f122","name":"reply-all"},{"code":"f065","name":"resize-full"},{"code":"f07e","name":"resize-horizontal"},{"code":"f066","name":"resize-small"},{"code":"f07d","name":"resize-vertical"},{"code":"f079","name":"retweet"},{"code":"f018","name":"road"},{"code":"f135","name":"rocket"},{"code":"f09e","name":"rss"},{"code":"f143","name":"rss-sign"},{"code":"f0c7","name":"save"},{"code":"f05b","name":"screenshot"},{"code":"f002","name":"search"},{"code":"f045","name":"share"},{"code":"f064","name":"share-alt"},{"code":"f14d","name":"share-sign"},{"code":"f132","name":"shield"},{"code":"f07a","name":"shopping-cart"},{"code":"f0c8","name":"sign-blank"},{"code":"f012","name":"signal"},{"code":"f090","name":"signin"},{"code":"f08b","name":"signout"},{"code":"f0e8","name":"sitemap"},{"code":"f17e","name":"skype"},{"code":"f118","name":"smile"},{"code":"f0dc","name":"sort"},{"code":"f15d","name":"sort-by-alphabet"},{"code":"f15e","name":"sort-by-alphabet-alt"},{"code":"f160","name":"sort-by-attributes"},{"code":"f161","name":"sort-by-attributes-alt"},{"code":"f162","name":"sort-by-order"},{"code":"f163","name":"sort-by-order-alt"},{"code":"f0dd","name":"sort-down"},{"code":"f0de","name":"sort-up"},{"code":"f110","name":"spinner"},{"code":"f16c","name":"stackexchange"},{"code":"f005","name":"star"},{"code":"f006","name":"star-empty"},{"code":"f089","name":"star-half"},{"code":"f123","name":"star-half-empty"},{"code":"f048","name":"step-backward"},{"code":"f051","name":"step-forward"},{"code":"f0f1","name":"stethoscope"},{"code":"f04d","name":"stop"},{"code":"f0cc","name":"strikethrough"},{"code":"f12c","name":"subscript"},{"code":"f0f2","name":"suitcase"},{"code":"f185","name":"sun"},{"code":"f12b","name":"superscript"},{"code":"f0ce","name":"table"},{"code":"f10a","name":"tablet"},{"code":"f02b","name":"tag"},{"code":"f02c","name":"tags"},{"code":"f0ae","name":"tasks"},{"code":"f120","name":"terminal"},{"code":"f034","name":"text-height"},{"code":"f035","name":"text-width"},{"code":"f00a","name":"th"},{"code":"f009","name":"th-large"},{"code":"f00b","name":"th-list"},{"code":"f165","name":"thumbs-down"},{"code":"f088","name":"thumbs-down-alt"},{"code":"f164","name":"thumbs-up"},{"code":"f087","name":"thumbs-up-alt"},{"code":"f145","name":"ticket"},{"code":"f017","name":"time"},{"code":"f043","name":"tint"},{"code":"f014","name":"trash"},{"code":"f181","name":"trello"},{"code":"f091","name":"trophy"},{"code":"f0d1","name":"truck"},{"code":"f173","name":"tumblr"},{"code":"f174","name":"tumblr-sign"},{"code":"f099","name":"twitter"},{"code":"f081","name":"twitter-sign"},{"code":"f0e9","name":"umbrella"},{"code":"f0cd","name":"underline"},{"code":"f0e2","name":"undo"},{"code":"f127","name":"unlink"},{"code":"f09c","name":"unlock"},{"code":"f13e","name":"unlock-alt"},{"code":"f01b","name":"upload"},{"code":"f093","name":"upload-alt"},{"code":"f155","name":"usd"},{"code":"f007","name":"user"},{"code":"f0f0","name":"user-md"},{"code":"f189","name":"vk"},{"code":"f027","name":"volume-down"},{"code":"f026","name":"volume-off"},{"code":"f028","name":"volume-up"},{"code":"f071","name":"warning-sign"},{"code":"f18a","name":"weibo"},{"code":"f17a","name":"windows"},{"code":"f0ad","name":"wrench"},{"code":"f168","name":"xing"},{"code":"f169","name":"xing-sign"},{"code":"f167","name":"youtube"},{"code":"f16a","name":"youtube-play"},{"code":"f166","name":"youtube-sign"},{"code":"f00e","name":"zoom-in"},{"code":"f010","name":"zoom-out"}];

        if (typeof G1admin.fontAwesomeListFilter !== 'undefined') {
            fontAwesomeList = G1admin.fontAwesomeListFilter(fontAwesomeList);
        }

        return fontAwesomeList;
    }
});
})(jQuery);
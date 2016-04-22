function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function doover_mce_submit() {

	var output;
	var shortcode = document.getElementById('shortcode').value;
	
	switch( shortcode ) {
		case 0:
		case "0":
			tinyMCEPopup.close();
			break;
	
		// ************************* general **********************
		case "accordion":
			output = "[" + shortcode + "]" +
				"[acc_item title=\"Section 1 title\"] Accordion section 1 content [/acc_item]" +
				"[acc_item title=\"Section 2 title\"] Accordion section 2 content [/acc_item]" +
				"[/" + shortcode + "]";
			break;	
			
		case "acc_item":
			output = "[" + shortcode + " title=\"Accordion section title\"]" +
				" Insert your content here " +
				"[/" + shortcode + "]";
			break;	
			
		case "blockquote":
			output = "[" + shortcode + " author=\"Muffin Group\" link=\"http://websiteaddress.com\" link_title=\"Website\"]" +
				" Insert your content here " +
				"[/" + shortcode + "]";
			break;	
			
		case "box_call_to_action":
			output = "[" + shortcode + " title=\"Box title\" style=\"contact\" btn_title=\"Button text\" link=\"http://livetechexchange.com\"]" +
				" Insert your content here " +
				"[/" + shortcode + "]";
			break;
			
		case "button":
			output = "[" + shortcode + " style=\"small\" colour=\"gray\" title=\"Read more\" link=\"http://livetechexchange.com\"]";
			break;
			
		case "dropcap":
			output = "[" + shortcode + " style=\"circle\" colour=\"blue\"]" +
				" D " +
				"[/" + shortcode + "]oover";
			break;
			
		case "highlight":
			output = "[" + shortcode + " colour=\"blue\"]" +
				" Insert your content here " +
				"[/" + shortcode + "]";
			break;
			
		case "homepage_box":
			output = "[" + shortcode + "]" +
				"<h3>Box title</h3> Insert your content here " +
				"[/" + shortcode + "]";
			break;
			
		case "homepage_features":
			output = "[" + shortcode + " title=\"Title\" desc=\"Description\" help_title=\"Need help?\" help_link=\"http://livetechexchange.com\" btn_title=\"Read more\" btn_link=\"http://livetechexchange.com\" ]" +
				"[homepage_feature title=\"Feature 1 title\" img=\"\" link_href=\"http://livetechexchange.com\" link_title=\"Read more\"] Insert your feature content here [/homepage_feature]" +
				"[homepage_feature_last title=\"Feature 2 title\" img=\"\" link_href=\"http://livetechexchange.com\" link_title=\"Read more\"] Insert your feature content here [/homepage_feature_last]" +
				"[/" + shortcode + "]";
			break;
			
		case "homepage_feature":
		case "homepage_feature_last":
			output = "[" + shortcode + " title=\"Feature title\" img=\"\" link_href=\"http://livetechexchange.com\" link_title=\"Read more\" ]" +
				" Insert your content here " +
				"[/" + shortcode + "]";
			break;
		
		case "illustration_overlay":
			output = "[" + shortcode + " image=\"\" overlay=\"\" alt=\"\"]";
			break;
			
		case "image":
			output = "[" + shortcode + " image=\"\" alt=\"\" width=\"\" height=\"\"]";
			break;
			
		case "image_frame":
			output = "[" + shortcode + " image=\"\" zoom=\"\" alt=\"\" width=\"\" height=\"\" float=\"left\" ]";
			break;
			
		case "list":
			output = "[" + shortcode + "]" +
				" <ul><li>First list element</li><li>Second list element</li></ul> " +
				"[/" + shortcode + "]";
			break;
			
		case "links":
			output = "[" + shortcode + "]" +
				" <ul><li><a href=\"http://livetechexchange.com\">First list element</a></li><li><a href=\"http://livetechexchange.com\">First list element</a></li></ul> " +
				"[/" + shortcode + "]";
			break;
			
		case "message":
			output = "[" + shortcode + " style=\"info\"]" +
				" Insert your message here " +
				"[/" + shortcode + "]";
			break;
			
		case "pricing_table":
			output = "<table><thead><tr><th>Column 1 heading</th><th>Column 2 heading</th><th>Column 3 heading</th></tr></thead><tbody><tr><td>Row 1 col 1 content</td><td>Row 1 col 2 content</td><td>Row 1 col 3 content</td></tr><tr><td>Row 2 col 1 content</td><td>Row 2 col 2 content</td><td>Row 2 col 3 content</td></tr></tbody></table>";
			break;
			
		case "tabs":
			output = "[" + shortcode + "]" +
				"[tab title=\"Tab 1 title\"] Tab 1 content [/tab]" +
				"[tab title=\"Tab 2 title\"] Tab 2 content [/tab]" +
				"[/" + shortcode + "]";
			break;
			
		case "tab":
		case "tab_last":
			output = "[" + shortcode + " title=\"Tab 1 title\"]" +
				" Insert your tab content here " +
				"[/" + shortcode + "]";
			break;
			
		case "table":
			output = "<table><thead><tr><th>Column 1 heading</th><th>Column 2 heading</th><th>Column 3 heading</th></tr></thead><tbody><tr><td>Row 1 col 1 content</td><td>Row 1 col 2 content</td><td>Row 1 col 3 content</td></tr><tr><td>Row 2 col 1 content</td><td>Row 2 col 2 content</td><td>Row 2 col 3 content</td></tr></tbody></table>";
			break;
			
		case "vimeo":
			output = "[" + shortcode + " video=\"19819283\" width=\"710\" height=\"426\"]";
			break;
			
		case "youtube":
			output = "[" + shortcode + " video=\"mmeN4XGti8o\" width=\"710\" height=\"426\"]";
			break;
		
		// ************************* pages **********************	
		case "clients":
			output = "[" + shortcode + "]" +
				"[client image=\"\" link=\"http://livetechexchange.com\" width=\"\" height=\"\" title=\"Live-Tech\"]" +
				"[client_last image=\"\" link=\"http://livetechexchange.com\" width=\"\" height=\"\" title=\"Live-Tech\"]" +
				"[/" + shortcode + "]";
			break;
			
		case "client":
		case "client_last":
			output = "[" + shortcode + " image=\"\" link=\"http://livetechexchange.com\" width=\"210\" height=\"\" title=\"Live-Tech\"]";
			break;
			
		case "faq":
			output = "[" + shortcode + "]" +
				"[faq_answer question=\"FAQ question 1\"] Insert your FAQ answer 1 here [/faq_answer]" +
				"[faq_answer question=\"FAQ question 2\"] Insert your FAQ answer 2 here [/faq_answer]" +
				"[/" + shortcode + "]";
			break;	
			
		case "faq_answer":
			output = "[" + shortcode + " question=\"FAQ question\"]" +
				" Insert your FAQ answer here " +
				"[/" + shortcode + "]";
			break;
			
		case "features":
			output = "[" + shortcode + "]" +
				"[feature title=\"Feature 1 title\" image=\"\" image_width=\"\" image_height=\"\" link=\"http://livetechexchange.com\" link_title=\"Read more\" ] Insert your Feature 1 content here [/feature]" +
				"[feature_last title=\"Feature 1 title\" image=\"\" image_width=\"\" image_height=\"\" link=\"http://livetechexchange.com\" link_title=\"Read more\" ] Insert your Feature 1 content here [/feature_last]" +
				"[/" + shortcode + "]";
			break;	
			
		case "feature":
		case "feature_last":
			output = "[" + shortcode + " title=\"Feature title\" image=\"\" image_width=\"\" image_height=\"\" link=\"http://livetechexchange.com\" link_title=\"Read more\"]" +
				" Insert your feature content here " +
				"[/" + shortcode + "]";
			break;
			
		case "offer":
			output = "[" + shortcode + "]" +
				"[offer_item image_position=\"left\"]<img src=\"\" alt=\"\" /><h3>Offer item title</h3><p>Insert your offet item content here</p>[button title=\"Read more\" link=\"http://livetechexchange.com\"][/offer_item]" +
				"[/" + shortcode + "]";
			break;
			
		case "offer_item":
			output = "[" + shortcode + " image_position=\"left\" ]" +
				"<img src=\"\" alt=\"\" /><h3>Offer item title</h3><p>Insert your offet item content here</p>[button title=\"Read more\" link=\"http://livetechexchange.com\"]" +
				"[/" + shortcode + "]";
			break;
	
		default:
			output = "[" + shortcode + "] Insert your content here [/" + shortcode + "]";
	}
	

	if (window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent',false, output);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return true;
}
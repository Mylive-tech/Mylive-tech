var cd = {
	codes : Array,
	init : function() {
		cd.codes = document.getElementsByClassName('code1');
		cd.attach();
	},
	attach : function() {
		var i;
		for ( i=0;i<cd.codes.length;i++ ) {
			Event.observe(cd.codes[i],'click',cd.collapse,false);
			Element.cleanWhitespace(cd.codes[i].parentNode);
		}
	},
	getEventSrc : function (e) {
		if (!e) e = window.event;
		if (e.originalTarget)
			return e.originalTarget;
		else if (e.srcElement)
		return e.srcElement;
	},
	collapse : function(e) {
		var el = cd.getEventSrc(e).nextSibling;
		if ( Element.hasClassName(el,'closed') ) {
			var opened = document.getElementsByClassName('code1');
			for (i = 0; i < opened.length; i++) {
				/*if (!Element.hasClassName(opened[i],'closed'))
					new Effect.Parallel(
						[
							new Effect.SlideUp(opened[i],{sync:true}),
							new Effect.Fade(opened[i],{sync:true})
						],
						{
							duration:0.3,
							fps:70
						}
					);
					Element.addClassName(opened[i],'closed')
					*/
			}
			new Effect.Parallel(
				[
					new Effect.SlideDown(el,{sync:true}),
					new Effect.Appear(el,{sync:true})
				],
				{
					duration:0.3,
					fps:70
				}
			);
			Element.removeClassName(el,'closed');
		} else {
			new Effect.Parallel(
				[
					new Effect.SlideUp(el,{sync:true}),
					new Effect.Fade(el,{sync:true})
				],
				{
					duration:0.3,
					fps:70
				}
			);
			Element.addClassName(el,'closed')
		}
	}
};
Event.observe(window, 'load', cd.init, false);

function showWhich(phpSelf)
{
	var aux = phpSelf;
	var lastIndex = phpSelf.lastIndexOf('/');
	var fileName = aux.substring(lastIndex+1);
	if (fileName == 'conf_settings.php?c=19&r=1') {
		document.getElementById("Articles").style.display = "block";
		document.getElementById("Articles").removeClassName('closed');
	} else {
		var temp = new Array();
		temp = fileName.split('.');
		switch(temp[0]) {
			case 'index': 
				break;
			case 'dir_categs':
			case 'dir_approve_categs':
			case 'dir_categs_edit':
				document.getElementById("Categories").style.display = "block";
				document.getElementById("Categories").removeClassName('closed');
				break;
			case 'dir_media':
			case 'dir_media_edit':
				document.getElementById("Media Manager").style.display = "block";
				document.getElementById("Media Manager").removeClassName('closed');
				break;
			case 'dir_links':
			case 'dir_links_edit':
			case 'dir_approve_links':
			case 'dir_reviewed_links':
			case 'dir_review_links_edit':
			case 'dir_validate':
			case 'dir_link_comments':
			case 'dir_approve_link_comments':	
			case 'dir_link_import':
				document.getElementById("Links").style.display = "block";
				document.getElementById("Links").removeClassName('closed');
				break;
			case 'dir_tags':
			case 'dir_tags_edit':
				document.getElementById("Tags").style.display = "block";
				document.getElementById("Tags").removeClassName('closed');
				break;
			case 'article_list':
			case 'article_edit':
			case 'article_reviewed':
			case 'article_reviewed_edit':
			case 'article_details':
			case 'article_approve':
			case 'dir_comments':
            case 'dir_approve_comments':
			    document.getElementById("Articles").style.display = "block";
				document.getElementById("Articles").removeClassName('closed');
				break;	
            case 'dir_widgets':
            case 'dir_widget_zones':
            case 'dir_widgets_edit':
            case 'dir_widgets_per_zone':
            case 'dir_widgets_pick_zones':
            case 'dir_widget_howto':
            case 'dir_inline_widgets':
            case 'dir_inline_widget_edit':
            	document.getElementById("Widgets").style.display = "block";
				document.getElementById("Widgets").removeClassName('closed');
				break;
			case 'dir_pages_edit':
			case 'dir_pages':	
				document.getElementById("Pages").style.display = "block";
				document.getElementById("Pages").removeClassName('closed');
				break;	
			case 'dir_link_types':
			case 'dir_link_types_edit':
			case 'dir_submit_items_edit':
			case 'dir_submit_items':	
				document.getElementById("Link Types").style.display = "block";
				document.getElementById("Link Types").removeClassName('closed');
				break;	
			case 'conf_users':
			case 'conf_groups':
			case 'conf_permissions':
			case 'conf_users_edit':
			case 'conf_users_actions':
				document.getElementById("User Management").style.display = "block";
				document.getElementById("User Management").removeClassName('closed');
				break;
			case 'email_message':
			case 'email_message_edit':
			case 'conf_language':
			case 'dir_language':
			case 'conf_payment':
			case 'article_payment':
			case 'spider':
			case 'conf_profile':
			case 'conf_users':
            case 'conf_admin_templates':
			case 'conf_sitemap':
			case 'conf_bancontrol':
			case 'conf_database':
			case 'conf_maintenance':
			case 'calculate_counts':
			case 'task_manager':
				document.getElementById("System").style.display = "block";
				document.getElementById("System").removeClassName('closed');
				break;

            case 'conf_templates_edit':
            case 'conf_templates':
            case 'conf_logo':
            case 'conf_background':
            case 'conf_fonts':
                document.getElementById("Theme").style.display = "block";
                document.getElementById("Theme").removeClassName('closed');
            break;

			case 'conf_settings':
			case 'conf_menu':
				document.getElementById("Settings").style.display = "block";
				document.getElementById("Settings").removeClassName('closed');
				break;	
			case 'email_send':
			case 'email_send_and_add_link':
			case 'email_sent_view':
			case 'email_import':
			case 'email_export':
			case 'newsletter_send':
				document.getElementById("Emailer").style.display = "block";
				document.getElementById("Emailer").removeClassName('closed');
				break;																	
		}
	}	
}
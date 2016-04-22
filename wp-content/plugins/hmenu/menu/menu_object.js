{
    "menu": {
        "config": {
            "development_mode": false
        },
        "structure": [
            {
                "id": "dashboard",
                "title": "Dashboard",
                "icon": "dashboard",
                "type": "link",
				"show_in_sidebar": true,
                "auto_load_subview": false,
                "viewpath": "dashboard/",
                "header": {
                    "auto_generate": false,
                    "show_save": false,
                    "header_label": "",
                    "header_title": ""
                }
            },
            {
                "id": "dropdown_menus",
                "title": "Menus",
                "icon": "menus",
                "type": "dropdown",
                "auto_load_subview": false,
                "submenu": [
                    {
                        "id": "dropdown_submenu_holder",
                        "type": "holder"
                    },
                    {
                        "id": "dropdown_menu_btn",
                        "title": "Add new menu",
                        "type": "button"
                    }
                ],
                "viewpath": "menus/",
                "header": {
                    "auto_generate": true,
                    "show_save": true,
                    "header_label": "",
                    "header_title": ""
                },
                "views": [
                    {
                        "id": "menu_layout",
                        "title": "Layout",
                        "icon": "menu",
                        "submenu": [
                            {
                                "id": "menu_sub_layout",
                                "title": "Layout and Order",
								"auto_load_components": false,
                                "view": "layout.edit"
                            },
							{
                                "id": "menu_sub_structure",
                                "title": "Navigation Structure",
								"auto_load_components": true,
                                "view": "layout.structure"
                            }
                        ]
                    },
					{
                        "id": "menu_styling",
                        "title": "Styling",
                        "icon": "brush",
                        "submenu": [
                            {
                                "id": "menu_sub_main_nav",
                                "title": "Main Navigation",
								"auto_load_components": true,
                                "view": "styling.main"
                            },
							{
                                "id": "menu_sub_standard",
                                "title": "Standard Dropdown",
								"auto_load_components": true,
                                "view": "styling.standard"
                            },
							{
                                "id": "menu_sub_mega",
                                "title": "Mega Menu",
								"auto_load_components": true,
                                "view": "styling.mega"
                            },
							{
                                "id": "menu_sub_search",
                                "title": "Search Field",
								"auto_load_components": true,
                                "view": "styling.search"
                            },
							{
                                "id": "menu_sub_product",
                                "title": "Woo Cart Icon",
								"auto_load_components": true,
                                "view": "styling.product"
                            },
							{
                                "id": "menu_sub_social",
                                "title": "Social Icons",
								"auto_load_components": true,
                                "view": "styling.social"
                            },
							{
                                "id": "menu_sub_mobile",
                                "title": "Mobile Devices",
								"auto_load_components": true,
                                "view": "styling.mobile"
                            },
							{
                                "id": "menu_sub_icons",
                                "title": "Icons",
								"auto_load_components": true,
                                "view": "styling.icons"
                            }
                        ]
                    },
					{
                        "id": "menu_settings",
                        "title": "Settings",
                        "icon": "settings",
                        "submenu": [
                            {
                                "id": "menu_sub_intergrate",
                                "title": "Menu Integration",
								"auto_load_components": true,
                                "view": "settings.integration"
                            },
							{
                                "id": "menu_sub_responsive",
                                "title": "Responsive",
								"auto_load_components": true,
                                "view": "settings.responsive"
                            },
							{
                                "id": "menu_sub_animation",
                                "title": "Animation",
								"auto_load_components": true,
                                "view": "settings.animation"
                            },
							{
                                "id": "menu_sub_advanced",
                                "title": "Advanced",
								"auto_load_components": true,
                                "view": "settings.advanced"
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
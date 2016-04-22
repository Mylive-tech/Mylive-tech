jQuery(window).load(function(){
					if( jQuery("#foo1 div").length > 4 ){
						jQuery("#foo1").carouFredSel({ responsive: true, 
							width: "100%",
							height: "250px",
							items: {
								
							visible: {
									min: 1,
									max: 4
							},
							width: 160,
						minimum: 4,
								height: "auto"
							},
							scroll: {
								items: 4,
								pauseOnHover: true,
								wipe: true
							},
							auto: {
								play: false,
								pauseDuration: 1
							},
							prev: {
								button: "#foo1_prev",
								key: "left"
							},
							next: {
								button: "#foo1_next",
								key: "right"
							}
						});
					}
				});
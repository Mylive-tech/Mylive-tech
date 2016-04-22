<?php
	// JQUERY 1.11
	//wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'jquery111', __WCX_WCREPORT_JS_URL__. 'back-end/jquery.min.js', true);
	//wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'jquery111');			wp_enqueue_script('jquery');
	
	// FONTAWESOME
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'font-awesome', __WCX_WCREPORT_CSS_URL__. 'back-end/font-awesome/font-awesome.min.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'font-awesome');
	
	// BOOTSTRAP
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'bootstrap-css', __WCX_WCREPORT_CSS_URL__. 'back-end/bootstrap/bootstrap.min.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'bootstrap-css');
	
	// JQUERY UI DATE PICKER
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_script('jquery-ui-datepicker');
	
	// JQPLOT 
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-jqplot', __WCX_WCREPORT_URL__.'/plugins/jqplot/jquery.jqplot.min.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-jqplot');	
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot', __WCX_WCREPORT_URL__.'/plugins/jqplot/jquery.jqplot.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot1', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.canvasTextRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot1');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot2', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.canvasAxisLabelRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot2');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot3', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.pieRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot3');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot4', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.donutRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot4');	
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot5', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.dateAxisRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot5');		
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot6', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.canvasAxisTickRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot6');		
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot7', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.categoryAxisRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot7');		
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot8', __WCX_WCREPORT_URL__.'/plugins/jqplot/jqplot.barRenderer.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-jqplot8');	
	
	// DATATABLES
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-datatables', __WCX_WCREPORT_URL__.'/plugins/datatables/css/jquery.dataTables.min.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-datatables');	
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-moments', __WCX_WCREPORT_URL__.'/plugins/moments/moment.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-moments');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-datatables', __WCX_WCREPORT_URL__.'/plugins/datatables/js/jquery.dataTables.min.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-datatables');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-datatables-tabletools', __WCX_WCREPORT_URL__.'/plugins/datatables/js/dataTables.tableTools.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-datatables-tabletools');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-moments-datatable', __WCX_WCREPORT_URL__.'/plugins/moments/datetime-moment.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-moments-datatable');	
	
	// TABLETOOLS
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-datatables-tabletools', __WCX_WCREPORT_URL__.'/plugins/datatables/css/dataTables.tableTools.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-datatables-tabletools');		

	// PLUGIN MAIN
	wp_register_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-custom', __WCX_WCREPORT_CSS_URL__. 'back-end/plugin-style.css', true);
	wp_enqueue_style(__WCX_WCREPORT_FIELDS_PERFIX__.'css-custom');
	wp_register_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-custom', __WCX_WCREPORT_JS_URL__. 'back-end/custom-js.js', true);
	wp_enqueue_script(__WCX_WCREPORT_FIELDS_PERFIX__.'js-custom');	
	

		
?>
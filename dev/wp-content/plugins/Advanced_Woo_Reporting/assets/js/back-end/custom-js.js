// PREVENT FROM CONFLICT WITH ANY $S
//$.noConflict();
jQuery( document ).ready(function( $ ) {
	
	// ADD DATEPICKER TO TEXTBOXES WITH .datepick CLASS
	if(typeof $('.datepick').datepicker !== 'undefined' && $.isFunction($('.datepick').datepicker))
	$('.datepick').datepicker({
        dateFormat : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
    });
	
	// ADDING DATE SORTING TO DATATABLES
	$.fn.dataTable.moment( 'MMMM - YYYY' );
	$.fn.dataTable.moment( 'MMMM-YYYY' );
	
	// CONFIGURING MANY TYPE OF DATATABLES USING DATATABLES PLUGIN
	var pth = $("script[src*='dataTables.tableTools.js']").attr('src');
	var n = pth.indexOf('?');
	pth = pth.substring(0, n != -1 ? n : pth.length).replace('dataTables.tableTools.js','').replace('js/','')+"swf/copy_csv_xls_pdf.swf";
	$('.datatable').dataTable( {
        "dom": 'T<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": pth,
			"aButtons": [
                {
                    "sExtends":    "copy",
                    "sButtonText": "Copy to clipboard"
                },
                {
                    "sExtends":    "print",
                    "sButtonText": "Print View"
                },
				{
                    "sExtends":    "csv",
                    "sButtonText": "Save as CSV"
                },
                {
                    "sExtends":    "xls",
                    "sButtonText": "Save as XLS"
                },
                {
                    "sExtends":    "pdf",
                    "sButtonText": "Save as PDF"
                }
            ]
        }
    } );
	$('.npnsnodatatable').dataTable({
		paging: false,
		"searching": false,
		"ordering": false
	});
	$('.npnsdatatable').dataTable({
		paging: false,
		"searching": false
	});
	$('.npnodatatable').dataTable({
		paging: false,
		"ordering": false
	});
	$('.nodatatable').dataTable({
		"ordering": false
	});
	$('.ntdatatable').dataTable();
	
	// SHOWING HIDDEN DEFAULT POSTBOXES
	$(".postbox").removeClass("hide");
	
	// FETCHING DATA FROM TABLES TO GENERATE CHARTS
	var dataRenderer = function(tablecontin) {
		var myTableArray = [[]];
		tablecontin[0].find("table").children("tbody").children("tr").each(function() {
			var arrayOfThisRow = [];
			var tableData = $(this).find('td:first-of-type,td:last-of-type');
			if (tableData.length > 0) {
				tableData.each(function() {
					text = $(this).text();
					if($(this).is($("td:last-of-type"))){
						text = parseFloat(text.substr(1));
					}
					arrayOfThisRow.push(text);
				});
				myTableArray[0].push(arrayOfThisRow);
			}
			return myTableArray;
		});

		return myTableArray;
	};
	
	// CREATE CUSTOMIZED CHART
	function createchart(chartlocation,data,type){
		switch(type){
			case "barpref":
				var plot1 = $.jqplot(chartlocation,data,{    
					animate: true,
					animateReplot: true,
					series:[{renderer:$.jqplot.BarRenderer}],
					axesDefaults: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
							angle: -30
						}
					},
					axes: {
						xaxis: {
							renderer: $.jqplot.CategoryAxisRenderer,
							tickOptions: {
								labelPosition: 'middle'
							}
						},
						yaxis: {
							autoscale:true,
							tickRenderer: $.jqplot.CanvasAxisTickRenderer,
							tickOptions: {
								labelPosition: 'start'
							}
						}
					},
					dataRenderer: dataRenderer
				});
				break;
			case "piepref":
				var plot1 = $.jqplot(chartlocation,data,{    
					animate: true,
					animateReplot: true,
					seriesDefaults: {
						renderer: jQuery.jqplot.PieRenderer, 
						rendererOptions: {
							showDataLabels: true
						}
					}, 
					legend: { show:true, location: 'e' },
					dataRenderer: dataRenderer
				});
				break;
			case "scatterpref":
				var plot1 = $.jqplot(chartlocation,data, {          
					animate: true,
					animateReplot: true,
					axes:{
						xaxis: {
							renderer: $.jqplot.CategoryAxisRenderer,
							tickOptions: {
								labelPosition: 'middle'
							}
						},
						yaxis: {
							autoscale:true,
							tickRenderer: $.jqplot.CanvasAxisTickRenderer,
							tickOptions: {
								labelPosition: 'start'
							}
						}
					},
					dataRenderer: dataRenderer
				 });
				break;
			default:
				var plot1 = $.jqplot(chartlocation,data,{    
					animate: true,
					animateReplot: true,
					series:[{renderer:$.jqplot.BarRenderer}],
					axesDefaults: {
						tickRenderer: $.jqplot.CanvasAxisTickRenderer,
						tickOptions: {
							angle: -30
						}
					},
					axes: {
						xaxis: {
							renderer: $.jqplot.CategoryAxisRenderer,
							tickOptions: {
								labelPosition: 'middle'
							}
						},
						yaxis: {
							autoscale:true,
							tickRenderer: $.jqplot.CanvasAxisTickRenderer,
							tickOptions: {
								labelPosition: 'start'
							}
						}
					},
					dataRenderer: dataRenderer
				});
				break;
		}
	}
	
	// DEFAULT MULTICHART BOX STATE
	$(".multicharted").each(function() {	
		whichindex = 1;
		$contin = $(this).children(".inside");
		$contin.children(".row.report_table").addClass( "hide" );
		$contin.children(".row.report_chart").addClass( "hide" );
		$contin.children(".row.report_chart").removeClass( "hide" );
		chartlocation = $contin.children(".row.report_chart").children(".chartlocation").attr("id");
		if(typeof plot1  != 'undefined' ){plot1.redraw();plot1.destroy();}
		$('#'+chartlocation).html("");
		$source = $contin.children(".row.report_table:nth-of-type("+whichindex+")");
		data = [ $source , 1 ];
		createchart(chartlocation,data,$source.find("table").children("thead").attr("class"));
	});
	
	// MULTICHART SHOW CHARTS
	$(document).on('click', ".showchart", function() {
		$(this).parent().children(".showchart").children(".eleman").removeClass("actived");
		$(this).children(".eleman").addClass("actived");
		whichindex = $(this).index(".showchart")+1;
		$contin = $(this).parent().parent().children(".inside");
		$contin.children(".row.report_table").addClass( "hide" );
		$contin.children(".row.report_chart").addClass( "hide" );
		$contin.children(".row.report_chart").removeClass( "hide" );
		chartlocation = $contin.children(".row.report_chart").children(".chartlocation").attr("id");
		if(typeof plot1  != 'undefined' ){plot1.redraw();plot1.destroy();}
		$('#'+chartlocation).html("");
		$source = $contin.children(".row.report_table:nth-of-type("+whichindex+")");
		data = [ $source , 1 ];
		data = [ $contin.children(".row.report_table:nth-of-type("+whichindex+")") , 1 ];
		createchart(chartlocation,data,$source.find("table").children("thead").attr("class"));
	});
	
	// SINGLECHART SHOW CHARTS OR TABLE
	$(document).on('click', ".showbarchart", function() {
		$(this).parent().children(".showpiechart").children(".eleman").removeClass("actived");
		$(this).parent().children(".showtable").children(".eleman").removeClass("actived");
		$(this).children(".eleman").addClass("actived");
		whichindex = $(this).index(".showbarchart")+1;
		whichindex = 1;
		$contin = $(this).parent().parent().children(".inside");
		$contin.children(".row.report_table").addClass( "hide" );
		$contin.children(".row.report_chart").addClass( "hide" );
		$contin.children(".row.report_chart").removeClass( "hide" );
		chartlocation = $contin.children(".row.report_chart").children(".chartlocation").attr("id");
		if(typeof plot1  != 'undefined' ){plot1.redraw();plot1.destroy();}
		$('#'+chartlocation).html("");
		$source = $contin.children(".row.report_table:nth-of-type("+whichindex+")");
		data = [ $source , 1 ];
		createchart(chartlocation,data,"barpref");
	});
	$(document).on('click', ".showpiechart", function() {
		$(this).parent().children(".showbarchart").children(".eleman").removeClass("actived");
		$(this).parent().children(".showtable").children(".eleman").removeClass("actived");
		$(this).children(".eleman").addClass("actived");
		whichindex = $(this).index(".showpiechart")+1;
		whichindex = 1;
		$contin = $(this).parent().parent().children(".inside");
		$contin.children(".row.report_table").addClass( "hide" );
		$contin.children(".row.report_chart").addClass( "hide" );
		$contin.children(".row.report_chart").removeClass( "hide" );
		chartlocation = $contin.children(".row.report_chart").children(".chartlocation").attr("id");
		if(typeof plot1  != 'undefined' ){plot1.redraw();plot1.destroy();}
		$('#'+chartlocation).html("");
		$source = $contin.children(".row.report_table:nth-of-type("+whichindex+")");
		data = [ $source , 1 ];
		createchart(chartlocation,data,"piepref");
	});
	$(document).on('click', ".showtable", function() {
		$(this).parent().children(".showbarchart").children(".eleman").removeClass("actived");
		$(this).parent().children(".showpiechart").children(".eleman").removeClass("actived");
		$(this).children(".eleman").addClass("actived");
		$(this).parent().parent().children(".inside").children(".row.report_table").removeClass( "hide" );
		$(this).parent().parent().children(".inside").children(".row.report_chart").addClass( "hide" );
	});
	
	// YEAR SELECT IN PROJECTED AND PREFERENCES
	$(".msel.mp0").show();
	$(".optyr").val('mp0');
	$('.optyr').on('change', function() {
		$(".msel").hide();
		$(".msel."+$(this).val()).show();
	});
	
	// MAKE PRINT PAGE FULL VIEW
	$(".DTTT_button_print").click(function() {
		$("#wpcontent").addClass("fullview");
		$("#wpcontent").prepend("<span class='checkprint hide'></span>");
	});
	
	// CHECK ESC BUTTON
	$(document).keyup(function(e) {
		if($("checkprint")){
			if (e.keyCode == 27){
				$("#wpcontent").removeClass("fullview");     
				$("checkprint").remove();
			}
		}
	});
	
});
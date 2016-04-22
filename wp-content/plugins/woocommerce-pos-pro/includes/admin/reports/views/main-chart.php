<div class="chart-container">
  <div class="chart-placeholder main"></div>
</div>
<script type="text/javascript">

  // global variable for export
  var main_chart;

  jQuery(function(){

    var data = jQuery.parseJSON( '<?php echo json_encode( $chart_data ); ?>' );

    var drawGraph = function( highlight ) {

      // clone data object
      var series = jQuery.extend(true, [], data);

      // highlight matching ids
      if ( highlight !== undefined ) {
        jQuery.map(series, function(obj){
          if(obj.id === highlight) {
            obj.color = 'rgb(156,93,144)';
            if ( obj.bars )
              obj.bars.fillColor = 'rgb(235,223,233)';
            if ( obj.lines ) {
              obj.lines.lineWidth = 5;
            }
          }
        });
      }

      main_chart = jQuery.plot(
        jQuery('.chart-placeholder.main'),
        series,
        {
          legend: {
            show: false
          },
          grid: {
            color: '#aaa',
            borderColor: 'transparent',
            borderWidth: 0,
            hoverable: true
          },
          xaxes: [ {
            color: '#aaa',
            position: "bottom",
            tickColor: 'transparent',
            mode: "time",
            timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
            monthNames: <?php global $wp_locale; echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
            tickLength: 1,
            minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
            font: {
              color: "#aaa"
            }
          } ],
          yaxes: [
            {
              min: 0,
              minTickSize: 1,
              tickDecimals: 0,
              color: '#d4d9dc',
              font: { color: "#aaa" }
            },
            {
              position: "right",
              min: 0,
              tickDecimals: 2,
              alignTicksWithAxis: 1,
              color: 'transparent',
              font: { color: "#aaa" }
            }
          ]
        }
      );

      jQuery('.chart-placeholder.main').resize();
    }

    drawGraph();

    jQuery('.highlight_series').hover(
      function() {
        drawGraph( jQuery(this).data('series') );
      },
      function() {
        drawGraph();
      }
    );
  });
</script>
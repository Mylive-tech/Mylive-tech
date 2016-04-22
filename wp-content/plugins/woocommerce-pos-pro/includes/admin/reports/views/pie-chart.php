<div class="chart-container">
  <div class="chart-placeholder pie" style="height:200px"></div>
  <?php if(isset($legend) && !empty($legend)): ?>
  <ul class="pie-chart-legend">
    <?php foreach($legend as $label): ?>
      <li style="border-color: <?php echo $label['color']; ?>"><?php echo $label['title']; ?></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
<script type="text/javascript">
  jQuery(function(){

    var series = jQuery.parseJSON( '<?php echo json_encode( $chart_data ); ?>' );

    jQuery.plot(
      jQuery('.chart-placeholder.pie'),
      series,
      {
        grid: {
          hoverable: true
        },
        series: {
          pie: {
            show: true,
            radius: 1,
            innerRadius: 0.6,
            label: {
              show: false
            }
          },
          enable_tooltip: true,
          append_tooltip: " <?php echo isset($append_tooltip) ? $append_tooltip : ''; ?>"
        },
        legend: {
          show: false
        }
      }
    );

    jQuery('.chart-placeholder.pie').resize();
  });
</script>
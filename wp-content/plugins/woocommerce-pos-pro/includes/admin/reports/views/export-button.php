<a
  href="#"
  download="<?php echo $filename; ?>"
  class="export_csv"
  data-export="<?php echo $export_type; ?>"
  data-xaxes="<?php /* translators: woocommerce */ _e( 'Date', 'woocommerce' ); ?>"
  data-groupby="<?php echo $this->chart_groupby; ?>"
  >
  <?php /* translators: woocommerce */ _e( 'Export CSV', 'woocommerce-pos-pro' ); ?>
</a>
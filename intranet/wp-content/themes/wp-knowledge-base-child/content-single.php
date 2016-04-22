<?php
/**
 * @package iPanelThemes Knowledgebase
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header page-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="entry-meta text-muted">
			<?php 
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
				$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);
			?>
			<span class="posted-on"><i class="glyphicon glyphicon-calendar"></i> Last Updated: 	 <?php echo $time_string; ?></span>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content();
		?>
		<?php
			wp_link_pages( array(
				'before' => __( '<p class="pagination-p">Pages:</p>', 'ipt_kb' ) . '<ul class="pagination">',
				'after'  => '</ul><div class="clearfix"></div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta text-muted well well-sm">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'ipt_kb' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'ipt_kb' ) );

			if ( ! ipt_kb_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					$meta_text = __( 'Tags: <i class="glyphicon glyphicon-tags"></i>&nbsp;&nbsp;%2$s.', 'ipt_kb' );
				} else {
					$meta_text = __( '', 'ipt_kb' );
				}

			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( 'Categories: <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;%1$s. <br /> Tags: <i class="glyphicon glyphicon-tags"></i>&nbsp;&nbsp;%2$s.', 'ipt_kb' );
				} else {
					$meta_text = __( 'Categories: <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;%1$s.', 'ipt_kb' );
				}

			} // end check for categories on this blog

			printf(
				$meta_text,
				$category_list,
				$tag_list
			);
		?>

		<?php edit_post_link( __( 'Edit', 'ipt_kb' ), '<span class="edit-link pull-right"><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->

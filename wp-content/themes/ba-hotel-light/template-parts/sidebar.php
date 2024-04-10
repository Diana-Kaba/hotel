<?php
/**
 * Sidebar
 *
 */

?>

<aside id="sidebar-<?php echo esc_attr($sidebar_name); ?>" class="widget-area sidebar <?php echo esc_attr(apply_filters( 'bahotel_l_style', "sidebar-".$sidebar_name, "sidebar-".$sidebar_name )); ?> <?php echo esc_attr(apply_filters( 'bahotel_l_column_width', $sidebar_width_class, "sidebar-".$sidebar_name )); ?>">

	<?php 
	do_action( 'bahotel_l_before_dynamic_sidebar', $sidebar_name );
	
	dynamic_sidebar( $sidebar_name );
	
	do_action( 'bahotel_l_after_dynamic_sidebar', $sidebar_name );
	?>
	
</aside>

<?php

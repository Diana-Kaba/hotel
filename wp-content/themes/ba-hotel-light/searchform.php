<?php
/**
 * Default search form.
 *
 */


?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url( '/' )); ?>">
	<label class="search-label">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'scr_title', 'ba-hotel-light' ); ?></span>
		<input type="search" class="search-field"
			placeholder="<?php esc_attr_e( 'Search', 'ba-hotel-light' ); ?>"
			value="<?php echo get_search_query(); ?>" name="s"
			title="<?php echo esc_attr_x( 'Search for:', 'title', 'ba-hotel-light' ); ?>" />
	</label>
	<button type="submit" class="search-submit">
		<label class="screen-reader-text"><?php echo esc_html_x( 'Search', 'scr_submit', 'ba-hotel-light' ); ?></label>
		<span class="lnr lnr-magnifier"></span>
	</button>
</form>
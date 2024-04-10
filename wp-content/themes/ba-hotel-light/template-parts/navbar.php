<?php
/**
 * Main navigation bar.
 *
 */

?>
<nav id="site-navigation" class="navbar navbar-expand-lg <?php echo esc_attr(apply_filters( 'bahotel_l_navbar_style', '')); ?>" role="navigation">

	<div class="container">
	
		<!-- Brand -->
		<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr(get_bloginfo( 'name' )); ?>" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php $custom_logo_id = get_theme_mod( 'custom_logo' ); ?>
				<?php $image = wp_get_attachment_image_src( $custom_logo_id, 'full' ); ?>
				<img class="site-logo" src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr(get_bloginfo( 'name' )); ?>" />
			<?php endif;
			    if ( get_theme_mod('header_text', true) ) : ?>
                <div class="brand_text_wrapper">
				   <h1><?php echo esc_html(get_bloginfo( 'name' )); ?></h1>
                    <?php if ( $tagline = get_bloginfo('description') ) : ?>
                        <span class="site-description"><?php echo esc_html( $tagline ); ?></span>
                    <?php endif; ?>
                 </div>
			<?php endif; ?>

		</a>
        
     <div class="header-top-row">   
        
        <div class="header-menu-row" role="navigation">
		
		<!-- Main menu -->
		<?php
		$walker = apply_filters( 'bahotel_l_nav_menu_walker', '' );
		$fallback = apply_filters( 'bahotel_l_nav_menu_fallback', '' );
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_class' => 'navbar-nav',
			'menu_id' => 'nav_menu',
			'container' => 'div',
			'container_class' => 'collapse navbar-collapse navbar-collapse-primary justify-content-end',
			'container_id' => 'nav_menu_container',
			'walker' => new $walker,
			'fallback_cb' => $fallback,
		) );
		?>
        
           <span class="menu-underline"></span>
        
        </div>
        
        <?php do_action( 'bahotel_l_header_navbar_after' ); ?>
        
        <!-- Toggler/collapsible button -->
        <div class="header-contacts-toggler navbar-toggler">
		  <button type="button" class="navbar-toggler" data-toggle="collapse" data-target=".navbar-collapse-primary" aria-controls="primary-menu" aria-expanded="false" tabindex="0">
			<span class="navbar-toggler-icon"></span>
		  </button>
        </div>
        
      </div>
        
	</div>
	
</nav><!-- #site-navigation -->
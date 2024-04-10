<?php
/**
 * Default header for our theme.
 *
 * Contains all the code before the actual content.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?>
<!DOCTYPE html>
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div id="page" class="site">
	
	<a class="skip-link screen-reader-text" href="#content" tabindex="0"><?php esc_html_e( 'Skip to content', 'ba-hotel-light' ); ?></a>
	
	<?php do_action( 'bahotel_l_get_panel', 'before-header' ); ?>
	
	<!-- Header -->
	
	<header id="header" class="<?php echo esc_attr(apply_filters( 'bahotel_l_style', 'site-header', 'header' )); ?>">
    
        <?php get_template_part( 'template-parts/navbar');?>
	
		<?php do_action( 'bahotel_l_get_panel', 'header' ); ?>
		
	</header><!-- #header -->
    
    <?php get_template_part( 'template-parts/content-tags/content-tag-header-thumbnail' ); ?>
    
    <?php do_action( 'bahotel_l_after_header' ); ?>
	
	<?php do_action( 'bahotel_l_get_panel', 'before-content' ); ?>
	
	<!-- Content -->
    
	<div id="content" class="site-content <?php echo esc_attr(apply_filters( 'bahotel_l_style', 'container', 'content' ));
    if (Bahotel_L_Settings::$layout_current != 'frontpage'){
       echo apply_filters( 'bahotel_l_page_option', true, 'header_margin' ) ? ' content-margin' : '';
    } 
    ?>">
    
       <div class="row">
       
            <?php do_action( 'bahotel_l_content_before' ); ?>
			
			<?php do_action( 'bahotel_l_get_panel', 'left' ); ?>
			
			<div id="primary" class="content-area <?php echo esc_attr(apply_filters( 'bahotel_l_column_width', 'col-lg-'.Bahotel_L_Settings::$layout_vars['width']['main'], 'primary' )); ?>">
				
				<div id="content-main" class="row">
				
					<!-- Main -->
				
					<main id="main" class="site-main <?php echo esc_attr(apply_filters( 'bahotel_l_column_width', 'col-lg-12', 'content' )); ?>">

						<?php do_action( 'bahotel_l_main_before' ); ?>
    
    
<?php
/**
 * The footer for our theme.
 *
 * Contains all the code after the actual content.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?>
                        <?php do_action( 'bahotel_l_main_after' ); ?>
						
					</main><!-- #main -->
				
				</div>
			
            </div><!-- #primary -->
			
			<?php do_action( 'bahotel_l_get_panel', 'right' ); ?>
			
		</div><!-- .row -->

	</div><!-- #content -->
	
	<?php do_action( 'bahotel_l_get_panel', 'before-footer' ); ?>
	
	<!-- Footer -->
	
	<footer id="footer" class="<?php echo esc_attr( apply_filters( 'bahotel_l_style', 'site-footer', 'footer' )); ?>">
		
		<?php do_action( 'bahotel_l_footer_before' ); ?>
		
		<?php do_action( 'bahotel_l_get_panel', 'footer' ); ?>
		
		<div class="container footer-widgets">
			<div class="row">
				<?php do_action( 'bahotel_l_get_panel', 'footer-left' ); ?>
				<?php do_action( 'bahotel_l_get_panel', 'footer-middle-left' ); ?>
                <?php do_action( 'bahotel_l_get_panel', 'footer-middle-right' ); ?>
				<?php do_action( 'bahotel_l_get_panel', 'footer-right' ); ?>
			</div>
		</div>
	
		<!-- Info -->
		
		<div class="container footer-copyright">
	
		   <!-- Copyright -->
		   <div id="copyrights" class="copyrights"><?php
           
           $copyright_text = apply_filters( 'bahotel_l_copyright_text', '');
           
           echo wp_kses_post($copyright_text);
           
           if ( function_exists( 'the_privacy_policy_link' ) ) {
              the_privacy_policy_link( ' <span class="footer-privacy-link">', '</span> ' );
           }
           
           ?></div>
        
        </div>
		
		<?php do_action( 'bahotel_l_footer_after' ); ?>
		
	</footer><!-- #footer -->

	<?php do_action( 'bahotel_l_page_bottom' ); ?>

</div><!-- #page -->

<!-- footer -->
<?php wp_footer(); ?>

</body>
</html>
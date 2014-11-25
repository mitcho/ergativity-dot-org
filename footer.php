		<div class="footer section large-padding bg-dark">
		
			<?php if ( is_active_sidebar( 'footer-a' ) ) : ?>
			
				<div class="column column-1 left">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-a' ); ?>
											
					</div>
					
				</div>
				
			<?php endif; ?> <!-- /footer-a -->
				
			<?php if ( is_active_sidebar( 'footer-b' ) ) : ?>
			
				<div class="column column-2 left">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-b' ); ?>
											
					</div> <!-- /widgets -->
					
				</div>
				
			<?php endif; ?> <!-- /footer-b -->
			
			<div class="clear"></div>
		
		</div> <!-- /footer -->
		
		<div class="credits">
		
			<div class="credits-inner">
			
				<p class="credits-left">
				
					&copy; 2012&ndash;<?php echo date("Y") ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>">McGill University</a>
				
				</p>
				
				<p class="credits-right">
					
					<!--<span></span> &mdash;--> <a title="<?php _e('To the top', 'wilson'); ?>" class="tothetop"><?php _e('Up', 'wilson' ); ?> &uarr;</a>
					
				</p>
				
				<div class="clear"></div>
			
			</div> <!-- /credits-inner -->
			
		</div> <!-- /credits -->
	
	</div> <!-- /content -->
	
	<div class="clear"></div>
	
</div> <!-- /wrapper -->

<?php wp_footer(); ?>

</body>
</html>
<?php get_header(); ?>

<div class="content">
	
		<div class="posts">
	
			<div <?php post_class(); ?>>
			
				<div class="post-inner">

				<div class="post-header">
				    <h2 class="post-title">Ergativity Questionnaire</h2>
				</div> <!-- /post-header -->
													                                    	    
				<div class="post-content">

	<ul id='questions'>
	<?php 

	$answers = get_user_meta( get_current_user_id(), '_erg_answers', true );
	if ( !is_array($answers) )
		$answers = array();

	if (have_posts()) : while (have_posts()) : the_post(); ?>

	    <li
		<?php if ( isset($answers[get_the_ID()]) ) echo ' class="answered"';?>
	    ><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>

	<?php endwhile; endif; ?>
	</ul>

				</div> <!-- /post-content -->
				            
				<div class="clear"></div>
				
			</div> <!-- /post-inner -->
			
		</div> <!-- /post -->
		
	</div> <!-- /posts -->

<?php get_footer(); ?>
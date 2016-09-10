<?php

	function martinehooptopbeter_show_excerpt_title() {

		$fields = get_post_custom_values('excerpt_title');
		foreach ( $fields as $key => $value ) {
			echo '<h2>' . esc_attr($value) . '</h2>';
		}

	}

?>
<?php get_header(); ?>
	
	<section class="jumbophoto">
	
		<div class="sitewidth" style="background-image: url(<?php bloginfo('template_url'); ?>/img/martine-met-kinderen-op-strand.jpg)">
			<div class="dimmed">

				<?php if (have_posts()) : ?>
					<?php the_post(); ?>
					<?php the_title( '<h1>', '</h1>' ); ?>
					<?php the_content(); ?>
				<?php endif ?>
				
				<?php

					// Default to no URL
					$url = "#";
					
					// Get all pages in the main navigation, but exclude the front page.
					// We want the first page after the front page, so that we can link to it.
					$location = 'main';
					if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $location ] ) ) {
						
						$menu = wp_get_nav_menu_object( $locations[ $location ] );
						$mainnav = wp_get_nav_menu_items( $menu->term_id, array() );
						
						if (sizeof($mainnav) >= 2) {
							$url = $mainnav[1]->url;
						}
					}
				
				?>

				<div class="buttons">
					<a href="<?php echo esc_attr($url) ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>
		</div>

	</section>

	<?php

		// Get top level pages
		query_posts( array( 'post_parent' => 0, 'post_type' => 'page', 'order' => 'ASC', 'post_per_page' => -1 ) );

	?>

	<?php while (have_posts()) : the_post(); ?>
	
		<?php switch ($post->post_name):

			case 'over-mij': ?>
	
	<section class="aboutme">
		<div class="sitewidth">
	
			<div class="text">

				<?php martinehooptopbeter_show_excerpt_title(); ?>
				<?php the_excerpt(); ?>
			
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>
				
				<img src="<?php bloginfo('template_url'); ?>/img/portret-martine.jpg" class="portrait" alt="Portret Martine" />
			</div>

		</div>
	</section>
	
			<?php break; ?>

			<?php case 'help-mij': ?>

	<section class="crowdfunding clearfix">
		<div class="sitewidth">
	
			<div class="photo"><img src="<?php bloginfo('template_url'); ?>/img/clinica-ruiz.jpg" alt="Clínica Ruiz" /></div>
			
			<div class="text">

				<?php martinehooptopbeter_show_excerpt_title(); ?>
				<?php the_excerpt(); ?>
				
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>
	
		</div>
	</section>

			<?php break; ?>
	
			<?php case 'multiple-sclerose': ?>

	<section class="aboutms">
		<div class="sitewidth">

			<div class="index accent clearfix">
			
				<ul>
					<li><a href="#">Wat is MS</a></li>
					<li><a href="#">Klachten bij MS</a></li>
					<li><a href="#">Vormen van MS</a></li>
					<li><a href="#">Hoe beïnvloed MS mijn leven</a></li>
				</ul>
				
			</div>
			
			<div class="text">
			
				<?php martinehooptopbeter_show_excerpt_title(); ?>
				<?php the_excerpt(); ?>
				
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>

		
		</div>
	</section>
	
			<?php break; ?>

		<?php endswitch; ?>

	<?php endwhile; ?>

<?php get_footer(); ?>
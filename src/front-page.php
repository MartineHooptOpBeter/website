<?php require_once 'donations-class.php' ?><?php

	function martinehooptopbeter_show_excerpt_title() {

		if ($fields = get_post_custom_values('excerpt_title')) {
			foreach ( $fields as $key => $value ) {
				echo '<h2>' . esc_attr($value) . '</h2>';
			}
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

		$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
		
		$itemCount = $donations->getDonationsListCount();
		$totalCount = $itemCount;
		$totalValue = $donations->getTotalDonationsAmount();
		
		if ($donations_options = get_option('donations_options')) {
			$totalCount += intval($donations_options['offline_number']);
			$totalValue += intval($donations_options['offline_amount']);
		}

		$goalValue = $config['donate_goal'];
		if ($goalValue > 0) {
			$goalPercentage = ((float)$totalValue / (float)$goalValue) * 100;
			if ($goalPercentage > 100) { $goalPercentage = 100.0; }
		} else {
			$goalPercentage = 0;
		}

		$pageSize = 10;
		$pageMax = intval($itemCount / $pageSize) + 1;

		$page = intval($page);
		$page = $page > 0 ? $page : 1;
		$page = $page > $pageMax ? $pageMax : $page;

		$items = $donations->getDonationsList(($page - 1) * $pageSize, $pageSize, 'DESC');

		if (count($items) > 0) :

?>	<section class="donate">
		<div class="sitewidth clearfix">

			<div class="text">
			
				<div class="meter">
					<span style="width: <?php echo number_format($goalPercentage, 2, '.', '') ?>%"><span></span></span>
				</div>
				<div class="metertext clearfix">
					<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s of %2$s', 'martinehooptopbeter')), array('<a href="/donaties/">' . esc_attr(Donation::formatEuroPrice($totalValue)) . '</a>', esc_attr(Donation::formatEuroPrice($goalValue)))); ?></span>
					<?php if ($totalCount == 1) : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donation', 'martinehooptopbeter'), $totalCount)); ?></span>
					<?php else : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donations', 'martinehooptopbeter'), $totalCount)); ?></span>
					<?php endif; ?>
				</div>
				
			</div>

			<div class="action">
			
				<p>Help je ook mee?</p>
				<div class="buttons">
					<a href="/doneren/" class="btn"><?php _e('Donate Online', 'martinehooptopbeter'); ?></a>
				</div>
				
			</div>
		
		</div>
	</section>
	<?php endif; ?>

	<?php

		// Get top level pages
		query_posts( array( 'post_parent' => 0, 'post_type' => 'page', 'order' => 'ASC', 'orderby' => 'menu_order', 'post_per_page' => -1 ) );

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

			<?php case 'stamceltransplantatie': ?>

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

				<?php $aboutMsPage = get_permalink(); ?>
			
				<?php $subposts = new WP_Query( array( 'post_parent' => $post->ID, 'post_type' => 'page', 'order' => 'ASC' ) ); ?>
				<?php if ($subposts->have_posts()) : ?>
				<ul>
					<?php while ($subposts->have_posts()) : $subposts->the_post(); $subpostTile = get_the_title(); ?>
						<?php if ($subpostTile) : ?>
							<li><a href="<?php echo esc_attr($aboutMsPage); ?>#<?php echo esc_attr(rawurlencode($post->post_name)); ?>"><?php echo esc_attr($subpostTile); ?></a></li>
						<?php endif ;?>
					<?php endwhile; ?>
				</ul>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>

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
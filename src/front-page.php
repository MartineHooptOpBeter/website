<?php

    @@HEADER@@

	require_once 'donations.class.php';
	require_once 'ponyplayday-registrations-service.class.php';
	require_once 'configuration.php';
	require_once 'utilities.class.php';

	function martinehooptopbeter_show_excerpt_title() {

		if ($fields = get_post_custom_values('excerpt_title')) {
			foreach ( $fields as $key => $value ) {
				echo '<h2>' . esc_attr($value) . '</h2>';
			}
		}

	}

?><?php get_header(); ?>
	
	<section class="jumbophoto">
	
		<div class="sitewidth" style="background-image: url(<?php bloginfo('template_url'); ?>/img/martine-met-kinderen-op-strand.jpg)">
			<div class="dimmed">

				<?php if (have_posts()) : ?>
					<?php the_post(); ?>
					<h1><?php echo esc_html(get_the_title()); ?></h1>
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

		$configuration = new Configuration();
		$donations = new Donations($configuration->getPaymentsDatabaseDataSourceName(), $configuration->getPaymentsDatabaseUsername(), $configuration->getPaymentsDatabasePassword());
		
		$itemCount = $donations->getDonationsListCount();
		$totalCount = $itemCount;
		$totalValue = $donations->getTotalDonationsAmount();
		
		if ($donations_options = get_option('donations_options')) {
			$totalCount += intval($donations_options['offline_number']);
			$totalValue += intval($donations_options['offline_amount']);
		}

		$goalValue = $configuration->getDonationsGoalValue();
		$goalPercentage = $donations->getPercentageOfGoal($totalValue, $goalValue, 100.0);
		
		$startdate = $configuration->getDonationsStartDate();

		if ($totalCount > 0) :

?>	<section class="donate">
		<div class="sitewidth clearfix">

			<div class="text">
			
					<?php if (isset($goalValue) && is_numeric($goalValue) && ($goalValue > 0)) : ?>
				<div class="meter">
					<span style="width: <?php echo Donation::formatDecimal($goalPercentage * 100.0); ?>%"><span></span></span>
				</div>
					<?php endif; ?>
				<div class="metertext clearfix">
					<?php if (isset($goalValue) && is_numeric($goalValue) && ($goalValue > 0)) : ?>
						<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s of %2$s', 'martinehooptopbeter')), array('<a href="' . __('/donations/', 'martinehooptopbeter') . '">' . esc_attr(Donation::formatEuroPrice($totalValue)) . '</a>', esc_attr(Donation::formatEuroPrice($goalValue)))); ?></span>
					<?php else : ?>
						<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s', 'martinehooptopbeter')), array('<a href="' . __('/donations/', 'martinehooptopbeter') . '">' . esc_attr(Donation::formatEuroPrice($totalValue)) . '</a>')); ?></span>
					<?php endif; ?>
					<?php if ($totalCount == 1) : ?>
						<?php if ($startdate != null) : ?>
							<span class="number"><?php echo esc_attr(sprintf(__('%1$s donation since %2$s', 'martinehooptopbeter'), $totalCount, Utilities::formatShortDate($startdate, get_locale()))); ?></span>
						<?php else : ?>
							<span class="number"><?php echo esc_attr(sprintf(__('%1$s donation', 'martinehooptopbeter'), $totalCount)); ?></span>
						<?php endif; ?>
					<?php else : ?>
						<?php if ($startdate != null) : ?>
							<span class="number"><?php echo esc_attr(sprintf(__('%1$s donations since %2$s', 'martinehooptopbeter'), $totalCount, Utilities::formatShortDate($startdate, get_locale()))); ?></span>
						<?php else : ?>
							<span class="number"><?php echo esc_attr(sprintf(__('%1$s donations', 'martinehooptopbeter'), $totalCount)); ?></span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				
			</div>

			<div class="action">
			
				<p><?php _e('Want to help?', 'martinehooptopbeter'); ?></p>
				<div class="buttons">
					<a href="<?php _e('/donate/', 'martinehooptopbeter'); ?>" class="btn"><?php _e('Donate Online', 'martinehooptopbeter'); ?></a>
				</div>
				
			</div>
		
		</div>
	</section>
	<?php endif; ?>

	<?php

		// Get top level pages
		query_posts( array( 'post_parent' => 0, 'post_type' => 'page', 'order' => 'ASC', 'orderby' => 'menu_order', 'posts_per_page' => -1 ) );

	?>

	<?php while (have_posts()) : the_post(); ?>
	
		<?php switch ($post->post_name):

			case 'over-mij': case 'about-martine': ?>
	
	<section class="aboutme">
		<div class="sitewidth">
	
			<div class="text">

				<?php martinehooptopbeter_show_excerpt_title(); ?>
				<?php echo esc_html(get_the_excerpt()); ?>
			
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>
				
				<img src="<?php bloginfo('template_url'); ?>/img/portret-martine.jpg" class="portrait" alt="Portret Martine" />
			</div>

		</div>
	</section>
	
			<?php break; ?>

			<?php case 'stamceltransplantatie': case 'stem-cell-transplantation': ?>

	<section class="crowdfunding clearfix">
		<div class="sitewidth">
	
			<div class="photo"><img src="<?php bloginfo('template_url'); ?>/img/clinica-ruiz.jpg" alt="Clínica Ruiz" /></div>
			
			<div class="text">

				<?php martinehooptopbeter_show_excerpt_title(); ?>
				<?php echo esc_html(get_the_excerpt()); ?>
				
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>
	
		</div>
	</section>

			<?php break; ?>
	
			<?php case 'multiple-sclerose': case 'multiple-sclerosis' ?>

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
				<?php echo esc_html(get_the_excerpt()); ?>
				
				<div class="buttons">
					<a href="<?php echo get_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>

		
		</div>
	</section>
	
			<?php break; ?>

		<?php endswitch; ?>

	<?php endwhile; ?>

<?php

	$args = array('numberposts' => 6, 'post_status' => 'publish');
	$recent_posts = wp_get_recent_posts($args);
	
	if ($recent_posts && (count($recent_posts) > 0)) {

		foreach($recent_posts as $recent) {
			if (has_post_thumbnail($recent["ID"])) {
				$latest_thumbnail = get_the_post_thumbnail($recent["ID"], 'martinehooptopbeter_square-400', '');
				break;
			}
		}

?>	<section class="bloglatest">
		<div class="sitewidth clearfix">

			<div class="photo"><?php echo $latest_thumbnail; ?></div>

			<div class="text">

				<h2><?php _e('Latest blog postings', 'martinehooptopbeter'); ?></h2>

				<ul>
				<?php foreach($recent_posts as $recent): ?>
					<li><a href="<?php echo get_permalink($recent["ID"]); ?>"><?php echo esc_attr($recent["post_title"]); ?></a>
				<?php endforeach; ?>
				</ul>

				<div class="buttons">
					<a href="/blog/" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>

<?php

		$ponyplaydayservice = new PonyPlayDayRegistrationsService($configuration);
		if ($ponyplaydayservice->isRegistrationPossible() && $ponyplaydayservice->isRegistrationStillOpen()) {

?>			<div class="action">
			
				<p><?php _e('Register for Pony Play Day', 'martinehooptopbeter'); ?></p>
				<div class="buttons">
					<a href="<?php _e('/ponyspeeldag/', 'martinehooptopbeter'); ?>" class="btn"><?php _e('Register', 'martinehooptopbeter'); ?></a>
				</div>
				
			</div>

<?php

		}

?>		</div>
	</section>

	<?php } ?>

	<?php wp_reset_query(); ?>

<?php get_footer(); ?>
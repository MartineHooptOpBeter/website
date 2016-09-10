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
					<a href="<?php echo esc_attr($url) ?>" class="btn">Lees verder</a>
				</div>

			</div>
		</div>

	</section>
	
	<section class="aboutme">
	
		<div class="sitewidth">
	
			<div class="text">
				<h2>Ik ben Martine</h2>
				<p>Als 41-jarige vrouw met twee schatten van dochters hoor je toch volop van het leven te genieten? Toch? Maar daar kwam zes jaar abrupt een streep doorheen toen bij mijn toenmalige vriend keelkanker werd geconstateerd en een maand later bij mij de diagnose MS werd gesteld.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>
				
				<img src="<?php bloginfo('template_url'); ?>/img/portret-martine.jpg" class="portrait" alt="Portret Martine" />
			</div>

		</div>
	
	</section>

	<section class="crowdfunding clearfix">
	
		<div class="sitewidth">
	
			<div class="photo"><img src="<?php bloginfo('template_url'); ?>/img/clinica-ruiz.jpg" alt="Clínica Ruiz" /></div>
			
			<div class="text">

				<h2>Stamceltherapie</h2>
				<p>De stamceltherapie, welke in totaal vier weken duurt, in de <a href="http://www.clinicaruiz.com/inicio.php" target="_blank">Clínica Ruiz</a> kliniek in Mexico kost $54.500. Met de kosten van de nabehandeling, reiskosten en de benodigde MRI scan kan dit oplopen tot wel &euro; 65.000,-. Omdat ik dit niet alleen kan betalen ben ik een crowdfunding actie begonnen.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>

			</div>
	
		</div>

	</section>

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
			
				<h2>Multiple sclerose</h2>
				<p>Multiple sclerose (MS) is een chronische aandoening van het centrale zenuwstelsel. Anders dan vaak wordt gedacht is multiple sclerose dus geen spierziekte. Bij MS gaat het isolerende laagje rondom de zenuwbanen (de myelineschede) langzaam stuk. Daardoor kunnen de zenuwprikkels niet meer goed geleid worden door de zenuwbanen.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>

			</div>

		
		</div>
	
	</section>

<?php get_footer(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<link rel="stylesheet" href="style.css" type="text/css" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="robots" content="index,follow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php

	global $page, $paged;

	if (function_exists('meta_title')) {
		meta_title('|', true, 'right');
	} else {
		wp_title('|', true, 'right');
	}

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
	echo ' | ' . esc_attr( $site_description );

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . esc_attr(sprintf( __( 'Pagina %s', 'martinehooptopbeter' ), max( $paged, $page ) ) );

	?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto:700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/allpages.css" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
</head>

<body>

	<section class="navigation clearfix">
	
		<div class="sitewidth">
	
			<a href="/" class="logo"><img src="<?php bloginfo('template_url'); ?>/img/martine-hoopt-op-beter.svg" alt="Martine hoopt op beter"></a>
		
			<nav>
				<ul>
					<li><a href="/" class="active">Home</a></li>
					<li><a href="/">Over mij</a></li>
					<li><a href="/">Help mij</a></li>
					<li><a href="/">Multiple Sclerosis</a></li>
					<li><a href="/">Contact</a></li>
				</ul>
			</nav>

		</div>
	
	</section>
	
	<section class="jumbophoto">
	
		<div class="sitewidth" style="background-image: url(<?php bloginfo('template_url'); ?>/img/martine-met-kinderen-op-strand.jpg)">
			<div class="dimmed">
		
				<h1>Help mij in mijn strijd tegen MS en de hoop op een beter leven!</h1>
				<p>Ondanks alle medicatie heeft MS na ruim zes jaar al heel veel van mijn mobiliteit afgenomen. Artsen in Nederland lijken mij niet te kunnen helpen en daarom heb ik mijn hoop volledig gevestigd op een stamceltherapie in Mexico. Help je mij mee op de kans op een beter leven?</p>
				<a href="#" class="btn">Lees verder</a>

			</div>
		</div>

	</section>
	
	<section class="aboutme">
	
		<div class="sitewidth">
	
			<div class="text">
				<h2>Ik ben Martine</h2>
				<p>Als 41-jarige vrouw met twee schatten van dochters hoor je toch volop van het leven te genieten? Toch? Maar daar kwam zes jaar abrupt een streep doorheen toen bij mijn toenmalige vriend keelkanker werd geconstateerd en een maand later bij mij de diagnose MS werd gesteld.</p>
				<a href="#" class="btn">Lees verder</a>
				<img src="<?php bloginfo('template_url'); ?>/img/portret-martine.jpg" class="portrait" alt="Portret Martine" />
			</div>

		</div>
	
	</section>

</body>

</html>
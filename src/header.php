<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<link rel="stylesheet" href="style.css" type="text/css" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index,follow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto:700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/allpages.css" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
<?php wp_head(); ?>
</head>

<body>

	<section class="navigation clearfix">
	
		<div class="sitewidth">
	
			<a href="/" class="logo"><img src="<?php bloginfo('template_url'); ?>/img/martine-hoopt-op-beter.svg" alt="<?php bloginfo( 'name' ); ?>"></a>

			<nav role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'martinehooptopbeter' ); ?>">
				<?php
					$location = 'main';
					if ( has_nav_menu( $location ) ) {
						wp_nav_menu(array(
							'container' => false, 
							'depth' => 0,
							'menu_id' => 'mainnav',
							'theme_location' => $location,
						));
					}
				?>
			</nav>

		</div>
	
	</section>

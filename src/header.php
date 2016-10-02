<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index,follow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Open+Sans|Roboto:700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/allpages.css" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
<?php wp_head(); ?>
</head>

<body>
	<?php require_once('config.php'); ?>
	<?php if (isset($config['googleanalytics_trackingid'])) : ?>
	<script> (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	
		ga('create', '<?php echo $config['googleanalytics_trackingid']; ?>', 'auto');
		ga('send', 'pageview');

	</script>
	<?php endif; ?>

	<section class="navigation clearfix">
	
		<div class="sitewidth">
	
			<a href="/" class="logo"><img src="<?php bloginfo('template_url'); ?>/img/martine-hoopt-op-beter.svg" alt="<?php bloginfo( 'name' ); ?>"></a>

			<nav role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'martinehooptopbeter' ); ?>">
				
				<label for="menu-toggle">Menu</label>
				<input type="checkbox" id="menu-toggle"/> 
				
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

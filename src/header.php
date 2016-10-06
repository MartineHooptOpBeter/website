<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index,follow" />
	<meta property="og:title" content="<?php wp_title( '-', true, 'right' ); ?>" />
	<meta property="og:description" content="<?php bloginfo( 'description' ); ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="<?php bloginfo('template_url'); ?>/img/martine-hoopt-op-beter.jpg" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="1200" />
	<meta property="og:url" content="<?php echo esc_attr(get_permalink()); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Open+Sans|Roboto:700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/allpages.css" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
	<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('template_url'); ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('template_url'); ?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('template_url'); ?>/favicon-16x16.png">
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
	
		<div class="sitewidth clearfix">
	
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

			<ul class="social">
				<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_permalink()); ?>" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" title="<?php echo vsprintf(__('Share this page on %1$s', 'martinehooptopbeter'), array(__('Facebook', 'martinehooptopbeter'))); ?>">&#xF799;</a>
				<li><a href="https://twitter.com/share?url=<?php echo rawurlencode(get_permalink()); ?>&amp;text=<?php echo rawurlencode(__('Help my friend Martine in her fight against MS. #ms #multiplesclerosis #fundraiser', 'martinehooptopbeter')); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" title="<?php echo vsprintf(__('Share this page on %1$s', 'martinehooptopbeter'), array(__('Twitter', 'martinehooptopbeter'))); ?>">&#xF798;</a>
				<li><a href="https://www.linkedin.com/shareArticle?url=<?php echo rawurlencode(get_permalink()); ?>&amp;title=<?php echo rawurlencode(__('I would like to ask you to support my co-worker / business affiliate Martine Siemelink in her fight against Multiple Sclerose (MS) and make a small (or large) donation. Thank you!', 'martinehooptopbeter')); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" title="<?php echo vsprintf(__('Share this page on %1$s', 'martinehooptopbeter'), array(__('LinkedIn', 'martinehooptopbeter'))); ?>">&#xF797;</a>
			</ul>

		</div>
	
	</section>

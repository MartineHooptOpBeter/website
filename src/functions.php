<?php

    @@HEADER@@

	function martinehooptopbeter_setup() {

		// Remove RSD link in the head
		remove_action('wp_head', 'rsd_link');
		
		// Remove WLM manifest in the head
		remove_action('wp_head', 'wlwmanifest_link');
		
		// Remove Wordpress generator in the head
		remove_action('wp_head', 'wp_generator');
		
		// Remove Wordpress emoji support
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('admin_print_styles', 'print_emoji_styles');

		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'martinehooptopbeter', get_template_directory() . '/languages' );

		// Main Navitation
		register_nav_menu( 'main', __( 'Main Navigation', 'martinehooptopbeter' ) );

		// Add support for page excerpts
		add_post_type_support( 'page', 'excerpt' );

		// Add support for post thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'martinehooptopbeter_square-200', 200, 200, true );
		add_image_size( 'martinehooptopbeter_square-400', 400, 400, true );
		add_image_size( 'martinehooptopbeter_fullwidth', 1200, 600, true );

		// Disable auto CSS for galleries
		add_filter('use_default_gallery_style', '__return_false');

		// We support HTML5 tags
		add_theme_support('html5', array('gallery', 'caption'));
	}
	add_action( 'after_setup_theme', 'martinehooptopbeter_setup' );

	function martinehooptopbeter_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		if ( $paged >= 2 || $page >= 2 )
			$title .= esc_attr(sprintf( __( 'Page %s', 'martinehooptopbeter' ), max( $paged, $page ) ) );

		return $title;
	}
	add_filter( 'wp_title', 'martinehooptopbeter_wp_title', 10, 2 );

	include 'settings-donations.php';

?>
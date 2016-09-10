<?php

	function martinehooptopbeter_setup() {

		// Remove RSD link in the head
		remove_action('wp_head', 'rsd_link');
		
		// Remove WLM manifest in the head
		remove_action('wp_head', 'wlwmanifest_link');
		
		// Remove Wordpress generator in the head
		remove_action('wp_head', 'wp_generator');

		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'martinehooptopbeter', get_template_directory() . '/languages' );

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

?>
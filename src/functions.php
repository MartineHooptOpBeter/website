<?php

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
			$title .= esc_attr(sprintf( __( 'Pagina %s', 'martinehooptopbeter' ), max( $paged, $page ) ) );

		return $title;
	}
	add_filter( 'wp_title', 'martinehooptopbeter_wp_title', 10, 2 );

?>
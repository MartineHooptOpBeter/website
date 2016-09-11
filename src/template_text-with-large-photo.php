<?php
/**
 * Template Name: Text with Large Photo
 */
?>
<?php

    if ( has_post_thumbnail() ) { 
        the_post_thumbnail( 'martinehooptopbeter_fullwidth' );
    }

    the_title( '<h2>', '</h2>' );
    the_content();

?>
<?php
/**
 * Template Name: Text Only
 */
?>
	<section class="content <?php echo $showDivider ? ' divider' : ''; ?>">
		<div class="sitewidth clearfix">

            <?php the_title( '<h2>', '</h2>' ); ?>
            <?php the_content(); ?>

		</div>
	</section>

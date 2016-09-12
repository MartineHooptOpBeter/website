<?php
/**
 * Template Name: Text with Large Photo
 */
?>
	<section class="content<?php echo $showDivider ? '  divider' : ''; ?>">
		<div class="sitewidth">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_fullwidth' ); } ?>

			<div class="text">
				<?php the_title( '<h2>', '</h2>' ); ?>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

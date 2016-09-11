<?php
/**
 * Template Name: Text with Photo Left
 */
?>
	<section class="content left<?php echo $showDivider ? ' divider' : ''; ?>">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<?php the_title( '<h2>', '</h2>' ); ?>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

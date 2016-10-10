<?php /* Template Name: Text with Large Photo */ ?>
<?php

    @@HEADER@@
	
?>	<section class="content<?php echo $showDivider ? '  divider' : ''; ?>" id="<?php echo esc_attr(rawurlencode($post->post_name)); ?>">
		<div class="sitewidth">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_fullwidth' ); } ?>

			<div class="text">
				<?php the_title( '<h2>', '</h2>' ); ?>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

<?php /* Template Name: Text with Photo Right */ ?>
<?php

    @@HEADER@@
	
?>	<section class="content right<?php echo $showDivider ? ' divider' : ''; ?>" id="<?php echo esc_attr(rawurlencode($post->post_name)); ?>">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<?php the_title( '<h2>', '</h2>' ); ?>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

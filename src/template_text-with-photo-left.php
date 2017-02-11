<?php /* Template Name: Text with Photo Left */ ?>
<?php

    @@HEADER@@
	
?>	<section class="content left<?php echo $showDivider ? ' divider' : ''; ?>" id="<?php echo esc_attr(rawurlencode($post->post_name)); ?>">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<h2><?php echo esc_html(get_the_title()); ?></h2>
				<?php the_content(); ?>
			</div>

		</div>
	</section>

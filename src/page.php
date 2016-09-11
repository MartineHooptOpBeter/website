<?php get_header(); ?>

	<?php if (have_posts()) : the_post(); ?>
		<?php $hasContent = (strlen(get_the_content()) > 0); ?>

		<?php if ($hasContent) : ?>

	<section class="content right dark divider">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<?php the_title( '<h1>', '</h1>' ); ?>
				<div class="intro"><?php the_content(); ?></div>
			</div>

		</div>
	</section>

		<?php endif ?>

	<?php endif ?>

<?php 

	$blocks = new WP_Query( array( 'post_parent' => $post->ID, 'post_type' => 'page', 'order' => 'ASC' ) ); 

	if ($blocks->have_posts()) {

?>	<section class="content<?php echo (!$hasContent) ? '  divider' : ''; ?>">
		<div class="sitewidth">

<?php

		do {
			$blocks->the_post();
			
			switch (get_post_meta( $post->ID, '_wp_page_template', true )) {

				case "template_text-with-large-photo.php":
					require_once "template_text-with-large-photo.php";

			}

		} while ($blocks->have_posts());

?>		</div>
	</section>
<?php

	}

?>

<?php get_footer(); ?>